<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'account_id',
        'username',
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'last_login_ip',
        'last_login_at',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'account_id' => 'integer',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['admin', 'staff'], true);
    }

    /**
     * Plaintext password temporary attribute for synchronizing to emulator.
     */
    public ?string $plain_password = null;

    /**
     * Model boot hooks to synchronize web user creation with Ragnarok emulator login table.
     */
    protected static function booted(): void
    {
        static::created(function (User $user) {
            $user->syncToRagnarokLogin();
        });

        static::updated(function (User $user) {
            if ($user->wasChanged(['password', 'role', 'email'])) {
                $user->syncToRagnarokLogin();
            }
        });
    }

    /**
     * Synchronize this user record with the Ragnarok game server login table.
     */
    public function syncToRagnarokLogin(): void
    {
        try {
            if (!config('database.connections.ragnarok.database')) {
                return;
            }

            $roDb = \Illuminate\Support\Facades\DB::connection('ragnarok');
            $hasher = app(\App\Services\Emulator\EmulatorPasswordHasher::class);

            $targetGroupId = ($this->role === 'admin') ? 99 : 0;

            // Determine game password
            if (!empty($this->plain_password)) {
                $gamePassword = $hasher->hash($this->plain_password);
            } else {
                $gamePassword = $this->password;
            }

            // 1. Check if account already exists by account_id or username
            $existing = null;
            if ($this->account_id) {
                $existing = $roDb->table('login')->where('account_id', $this->account_id)->first();
            }
            if (!$existing && $this->username) {
                $existing = $roDb->table('login')->whereRaw('LOWER(userid) = LOWER(?)', [$this->username])->first();
            }

            if ($existing) {
                // Update existing record
                $updateData = [];
                if ($this->role === 'admin' && (int)$existing->group_id < 99) {
                    $updateData['group_id'] = 99;
                }
                if ($this->email && $existing->email !== $this->email) {
                    $updateData['email'] = $this->email;
                }
                if (!empty($this->plain_password)) {
                    $updateData['user_pass'] = $gamePassword;
                }
                if (!empty($updateData)) {
                    $roDb->table('login')->where('account_id', $existing->account_id)->update($updateData);
                }

                if ($this->account_id !== (int)$existing->account_id) {
                    $this->account_id = (int)$existing->account_id;
                    $this->saveQuietly();
                }
            } else {
                // 2. Insert new account into ragnarok.login table
                $insertData = [
                    'userid' => $this->username,
                    'user_pass' => $gamePassword,
                    'sex' => 'M',
                    'email' => $this->email ?: ($this->username . '@aether.local'),
                    'group_id' => $targetGroupId,
                    'state' => 0,
                    'character_slots' => (int) config('emulator.registration.default_char_slots', 9),
                    'birthdate' => date('Y-m-d'),
                    'pincode' => '',
                    'last_ip' => request()->ip() ?? '127.0.0.1',
                ];

                $newAccountId = (int) $roDb->table('login')->insertGetId($insertData);
                if ($newAccountId > 0) {
                    $this->account_id = $newAccountId;
                    $this->saveQuietly();
                }
            }
        } catch (\Throwable $e) {
            // Log synchronization error silently without breaking web user creation
            \Illuminate\Support\Facades\Log::warning("Failed to sync user '{$this->username}' to ragnarok.login: " . $e->getMessage());
        }
    }
}
