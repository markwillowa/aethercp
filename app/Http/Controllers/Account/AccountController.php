<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Contracts\Repositories\AccountRepositoryInterface;
use App\Contracts\Repositories\CharacterRepositoryInterface;
use App\Contracts\PasswordHasherInterface;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AccountController extends Controller
{
    public function __construct(
        protected AccountRepositoryInterface $accounts,
        protected CharacterRepositoryInterface $characters,
        protected PasswordHasherInterface $hasher
    ) {}

    public function profile()
    {
        $user = Auth::user();
        $gameAccount = $this->accounts->findById($user->account_id);
        $charCount = $this->accounts->getCharacterCount($user->account_id);

        return view('account.profile', compact('user', 'gameAccount', 'charCount'));
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|max:32|confirmed',
        ]);

        $user = Auth::user();
        $gameAccount = $this->accounts->findById($user->account_id);

        if (!$gameAccount || !$this->hasher->verify($request->input('current_password'), $gameAccount->user_pass)) {
            throw ValidationException::withMessages([
                'current_password' => ['Your current password does not match.'],
            ]);
        }

        $newHashedGamePass = $this->hasher->hash($request->input('password'));
        $this->accounts->updatePassword($user->account_id, $newHashedGamePass);

        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        ActivityLog::log('account.password_change', "Password changed successfully.", $user->account_id);

        return back()->with('success', 'Your password has been updated across web and game servers.');
    }

    public function changeEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:100',
        ]);

        $user = Auth::user();
        $newEmail = trim($request->input('email'));

        if (!config('emulator.registration.allow_duplicate_emails', false)) {
            if ($this->accounts->emailExists($newEmail)) {
                throw ValidationException::withMessages([
                    'email' => ['This email address is already taken.'],
                ]);
            }
        }

        $this->accounts->updateEmail($user->account_id, $newEmail);
        $user->update(['email' => $newEmail]);

        ActivityLog::log('account.email_change', "Email updated to {$newEmail}.", $user->account_id);

        return back()->with('success', 'Your email address has been successfully updated.');
    }

    public function changePincode(Request $request)
    {
        $request->validate([
            'pincode' => 'required|digits:4',
        ]);

        $user = Auth::user();
        $pincode = $request->input('pincode');

        $this->accounts->updatePincode($user->account_id, $pincode);

        ActivityLog::log('account.pincode_change', "Security pincode updated.", $user->account_id);

        return back()->with('success', 'Your 4-digit character security PIN has been updated.');
    }

    public function characters()
    {
        $user = Auth::user();
        $characters = $this->characters->getCharactersByAccount($user->account_id);

        return view('account.characters', compact('user', 'characters'));
    }

    public function unstuck(Request $request, int $charId)
    {
        $user = Auth::user();
        $char = $this->characters->findById($charId);

        if (!$char || (int) $char->account_id !== (int) $user->account_id) {
            return back()->with('error', 'Character not found or access denied.');
        }

        if ($char->online > 0) {
            return back()->with('error', 'Character must be logged off before resetting position.');
        }

        // Reset to Prontera town square
        $this->characters->resetPosition($charId, 'prontera', 156, 191);

        ActivityLog::log('character.unstuck', "Reset position for character {$char->name} to Prontera.", $user->account_id);

        return back()->with('success', "Character {$char->name} has been unstuck and moved to Prontera!");
    }

    public function deleteCharacter(Request $request, int $charId)
    {
        $user = Auth::user();
        $char = $this->characters->findById($charId);

        if (!$char || (int) $char->account_id !== (int) $user->account_id) {
            return back()->with('error', 'Character not found or access denied.');
        }

        if ($char->online > 0) {
            return back()->with('error', 'Character must be logged off before deletion.');
        }

        if ($char->guild_id > 0) {
            // Check if guild master
            $guildMaster = \Illuminate\Support\Facades\DB::connection('ragnarok')
                ->table('guild')
                ->where('guild_id', $char->guild_id)
                ->value('char_id');

            if ((int) $guildMaster === $charId) {
                return back()->with('error', 'Guild Masters cannot be deleted. Transfer guild leadership first.');
            }
        }

        $charName = $char->name;
        $deleted = $this->characters->deleteCharacter($charId, $user->account_id);

        if ($deleted) {
            ActivityLog::log('character.delete', "Deleted character {$charName} (ID: {$charId})", $user->account_id);
            return back()->with('success', "Character {$charName} has been successfully deleted.");
        }

        return back()->with('error', 'Failed to delete character. Please verify character status.');
    }
}

