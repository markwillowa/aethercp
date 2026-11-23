<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Contracts\Repositories\AccountRepositoryInterface;
use App\Contracts\PasswordHasherInterface;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(
        protected AccountRepositoryInterface $accounts,
        protected PasswordHasherInterface $hasher
    ) {}

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('account.profile');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginInput = trim($request->input('username'));
        $password = $request->input('password');

        // Check if there is a local web user (by username or email)
        $localUser = User::where('username', $loginInput)
            ->orWhere('email', $loginInput)
            ->first();

        // Check emulator game account (by username or email)
        $gameAccount = $this->accounts->findByUsername($loginInput)
            ?? $this->accounts->findByEmail($loginInput);

        // If game account not found directly by input, try finding by local user's account_id
        if (!$gameAccount && $localUser && $localUser->account_id) {
            $gameAccount = $this->accounts->findById($localUser->account_id);
        }

        // Verify that at least one record exists
        if (!$gameAccount && !$localUser) {
            throw ValidationException::withMessages([
                'username' => ['These credentials do not match our records.'],
            ]);
        }

        // Check account suspension / ban if game account exists
        if ($gameAccount && ($gameAccount->state > 0 || ($gameAccount->unban_time > 0 && $gameAccount->unban_time > time()))) {
            throw ValidationException::withMessages([
                'username' => ['This account is currently suspended or banned.'],
            ]);
        }

        // Check password validity
        $passwordValid = false;

        // 1. Verify against emulator password hasher (MD5, Bcrypt, SHA256, plaintext)
        if ($gameAccount && $this->hasher->verify($password, $gameAccount->user_pass)) {
            $passwordValid = true;
        }

        // 2. Fallback to local Laravel bcrypt password (for web-created or seeded admin users)
        if (!$passwordValid && $localUser && Hash::check($password, $localUser->password)) {
            $passwordValid = true;
        }

        if (!$passwordValid) {
            throw ValidationException::withMessages([
                'password' => ['The provided password is incorrect.'],
            ]);
        }

        // Determine user model to log in
        $user = $localUser;
        $role = $localUser?->role ?? 'user';

        if ($gameAccount) {
            $role = ($gameAccount->group_id >= (int) config('emulator.gm_level_threshold', 1)) ? 'admin' : $role;
        }

        if (!$user && $gameAccount) {
            $user = User::create([
                'account_id' => $gameAccount->account_id,
                'username' => $gameAccount->userid,
                'name' => $gameAccount->userid,
                'email' => $gameAccount->email ?: ($gameAccount->userid . '@game.local'),
                'password' => Hash::make($password),
                'role' => $role,
                'is_active' => true,
                'last_login_ip' => $request->ip(),
                'last_login_at' => now(),
            ]);
        } elseif ($user) {
            $updateData = [
                'last_login_ip' => $request->ip(),
                'last_login_at' => now(),
            ];
            if ($gameAccount) {
                $updateData['account_id'] = $gameAccount->account_id;
                $updateData['role'] = $role;
            }
            $user->update($updateData);
        }

        // Update emulator login metrics
        if ($gameAccount) {
            $this->accounts->updateLastLogin($gameAccount->account_id, $request->ip());
        }

        // Log in session
        Auth::login($user, $request->filled('remember'));
        $request->session()->regenerate();

        ActivityLog::log('auth.login', "User {$user->username} logged in successfully.", $user->account_id);

        return redirect()->intended(route('account.profile'))->with('success', "Welcome back, {$user->username}!");
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('account.profile');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $minUser = (int) config('emulator.registration.min_username', 4);
        $maxUser = (int) config('emulator.registration.max_username', 23);
        $minPass = (int) config('emulator.registration.min_password', 6);
        $maxPass = (int) config('emulator.registration.max_password', 32);

        $request->validate([
            'username' => [
                'required',
                'string',
                "min:{$minUser}",
                "max:{$maxUser}",
                'regex:/^[a-zA-Z0-9_]+$/',
            ],
            'password' => [
                'required',
                'string',
                "min:{$minPass}",
                "max:{$maxPass}",
                'confirmed',
            ],
            'email' => 'required|email|max:100',
            'gender' => 'required|in:M,F',
            'birthdate' => 'nullable|date',
        ]);

        $username = trim($request->input('username'));
        $email = trim($request->input('email'));
        $password = $request->input('password');

        // Check if username already exists in emulator
        if ($this->accounts->usernameExists($username) || User::where('username', $username)->exists()) {
            throw ValidationException::withMessages([
                'username' => ['This username is already taken.'],
            ]);
        }

        // Check duplicate email rule
        if (!config('emulator.registration.allow_duplicate_emails', false)) {
            if ($this->accounts->emailExists($email) || User::where('email', $email)->exists()) {
                throw ValidationException::withMessages([
                    'email' => ['This email address is already registered.'],
                ]);
            }
        }

        // 1. Hash password with emulator hasher
        $hashedGamePassword = $this->hasher->hash($password);

        // 2. Insert into Ragnarok login table
        $accountId = $this->accounts->create([
            'username' => $username,
            'password' => $hashedGamePassword,
            'email' => $email,
            'gender' => $request->input('gender'),
            'birthdate' => $request->input('birthdate', date('Y-m-d')),
            'ip' => $request->ip(),
        ]);

        // 3. Create AetherCP web user
        $user = User::create([
            'account_id' => $accountId,
            'username' => $username,
            'name' => $username,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'user',
            'is_active' => true,
            'last_login_ip' => $request->ip(),
            'last_login_at' => now(),
        ]);

        // 4. Log in new user
        Auth::login($user);
        $request->session()->regenerate();

        ActivityLog::log('auth.register', "New account registered: {$username} (ID: {$accountId})", $accountId);

        return redirect()->route('account.profile')->with('success', 'Your account has been registered successfully! Welcome to the server.');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            ActivityLog::log('auth.logout', "User logged out.", Auth::user()->account_id);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have been logged out.');
    }
}

