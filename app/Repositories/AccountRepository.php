<?php

namespace App\Repositories;

use App\Contracts\EmulatorInterface;
use App\Contracts\Repositories\AccountRepositoryInterface;
use Illuminate\Support\Facades\DB;

class AccountRepository implements AccountRepositoryInterface
{
    public function __construct(
        protected EmulatorInterface $emulator
    ) {}

    protected function getTable(): string
    {
        return $this->emulator->getTableName('login');
    }

    public function findById(int $accountId): ?object
    {
        return DB::connection('ragnarok')
            ->table($this->getTable())
            ->where('account_id', $accountId)
            ->first();
    }

    public function findByUsername(string $username): ?object
    {
        $caseSensitive = (bool) config('emulator.registration.case_sensitive_username', false);

        $query = DB::connection('ragnarok')
            ->table($this->getTable());

        if ($caseSensitive) {
            $query->whereRaw('BINARY userid = ?', [$username]);
        } else {
            $query->whereRaw('LOWER(userid) = LOWER(?)', [$username]);
        }

        return $query->first();
    }

    public function findByEmail(string $email): ?object
    {
        return DB::connection('ragnarok')
            ->table($this->getTable())
            ->where('email', $email)
            ->first();
    }

    public function usernameExists(string $username): bool
    {
        return $this->findByUsername($username) !== null;
    }

    public function emailExists(string $email): bool
    {
        return DB::connection('ragnarok')
            ->table($this->getTable())
            ->where('email', $email)
            ->exists();
    }

    public function create(array $data): int
    {
        $insert = [
            'userid' => $data['username'],
            'user_pass' => $data['password'],
            'sex' => strtoupper($data['gender'] ?? 'M'),
            'email' => $data['email'],
            'group_id' => (int) ($data['group_id'] ?? config('emulator.registration.default_group_id', 0)),
            'state' => 0,
            'character_slots' => (int) ($data['character_slots'] ?? config('emulator.registration.default_char_slots', 9)),
            'birthdate' => $data['birthdate'] ?? date('Y-m-d'),
            'pincode' => $data['pincode'] ?? '',
            'last_ip' => $data['ip'] ?? request()->ip() ?? '127.0.0.1',
        ];

        return (int) DB::connection('ragnarok')
            ->table($this->getTable())
            ->insertGetId($insert);
    }

    public function updatePassword(int $accountId, string $hashedPassword): bool
    {
        return DB::connection('ragnarok')
            ->table($this->getTable())
            ->where('account_id', $accountId)
            ->update(['user_pass' => $hashedPassword]) > 0;
    }

    public function updateEmail(int $accountId, string $email): bool
    {
        return DB::connection('ragnarok')
            ->table($this->getTable())
            ->where('account_id', $accountId)
            ->update(['email' => $email]) > 0;
    }

    public function updatePincode(int $accountId, string $pincode): bool
    {
        return DB::connection('ragnarok')
            ->table($this->getTable())
            ->where('account_id', $accountId)
            ->update([
                'pincode' => $pincode,
                'pincode_change' => time(),
            ]) > 0;
    }

    public function updateLastLogin(int $accountId, string $ip): bool
    {
        return DB::connection('ragnarok')
            ->table($this->getTable())
            ->where('account_id', $accountId)
            ->update([
                'lastlogin' => now(),
                'last_ip' => $ip,
                'logincount' => DB::raw('logincount + 1'),
            ]) > 0;
    }

    public function getCharacterCount(int $accountId): int
    {
        $charTable = $this->emulator->getTableName('char');
        return DB::connection('ragnarok')
            ->table(DB::raw($this->emulator->quoteIdentifier($charTable)))
            ->where('account_id', $accountId)
            ->count();
    }

    public function setAccountState(int $accountId, int $state, int $unbanTime = 0): bool
    {
        return DB::connection('ragnarok')
            ->table($this->getTable())
            ->where('account_id', $accountId)
            ->update([
                'state' => $state,
                'unban_time' => $unbanTime,
            ]) > 0;
    }
}

