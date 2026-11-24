<?php

namespace App\Repositories;

use App\Contracts\EmulatorInterface;
use App\Contracts\Repositories\CharacterRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CharacterRepository implements CharacterRepositoryInterface
{
    public function __construct(
        protected EmulatorInterface $emulator
    ) {}

    protected function getTable(): string
    {
        return $this->emulator->quoteIdentifier($this->emulator->getTableName('char'));
    }

    public function getCharactersByAccount(int $accountId): array
    {
        return DB::connection('ragnarok')
            ->table(DB::raw($this->getTable()))
            ->where('account_id', $accountId)
            ->orderBy('char_num', 'asc')
            ->get()
            ->toArray();
    }

    public function getByAccountId(int $accountId): array
    {
        return $this->getCharactersByAccount($accountId);
    }

    public function findById(int $charId): ?object
    {
        return DB::connection('ragnarok')
            ->table(DB::raw($this->getTable()))
            ->where('char_id', $charId)
            ->first();
    }

    public function findByName(string $name): ?object
    {
        return DB::connection('ragnarok')
            ->table(DB::raw($this->getTable()))
            ->where('name', $name)
            ->first();
    }

    public function getOnlineCharacters(int $limit = 50): array
    {
        $gmThreshold = (int) config('emulator.gm_level_threshold', 1);
        $hideGm = (bool) config('emulator.hide_gm_from_online', true);

        $query = DB::connection('ragnarok')
            ->table(DB::raw($this->getTable()))
            ->where('online', '>', 0)
            ->select('char_id', 'account_id', 'name', 'class', 'base_level', 'job_level', 'last_map');

        if ($hideGm) {
            $loginTable = $this->emulator->getTableName('login');
            $query->whereNotIn('account_id', function ($sub) use ($loginTable, $gmThreshold) {
                $sub->select('account_id')
                    ->from($loginTable)
                    ->where('group_id', '>=', $gmThreshold);
            });
        }

        return $query->orderBy('base_level', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function resetPosition(int $charId, string $map = 'prontera', int $x = 156, int $y = 191): bool
    {
        // Safety: only allow unstuck when character is offline
        return DB::connection('ragnarok')
            ->table(DB::raw($this->getTable()))
            ->where('char_id', $charId)
            ->where('online', 0)
            ->update([
                'last_map' => $map,
                'last_x' => $x,
                'last_y' => $y,
                'save_map' => $map,
                'save_x' => $x,
                'save_y' => $y,
            ]) > 0;
    }

    public function deleteCharacter(int $charId, int $accountId): bool
    {
        // Safety: only allow deletion if character belongs to account and is offline
        $char = DB::connection('ragnarok')
            ->table(DB::raw($this->getTable()))
            ->where('char_id', $charId)
            ->where('account_id', $accountId)
            ->where('online', 0)
            ->first();

        if (!$char) {
            return false;
        }

        // Delete from char table
        return DB::connection('ragnarok')
            ->table(DB::raw($this->getTable()))
            ->where('char_id', $charId)
            ->where('account_id', $accountId)
            ->delete() > 0;
    }

    public function getInventory(int $charId): array
    {
        $invTable = $this->emulator->getTableName('inventory');
        return DB::connection('ragnarok')
            ->table($invTable)
            ->where('char_id', $charId)
            ->get()
            ->toArray();
    }

    public function getCartInventory(int $charId): array
    {
        $cartTable = $this->emulator->getTableName('cart_inventory');
        return DB::connection('ragnarok')
            ->table($cartTable)
            ->where('char_id', $charId)
            ->get()
            ->toArray();
    }

    public function getStorage(int $accountId): array
    {
        $storageTable = $this->emulator->getTableName('storage');
        return DB::connection('ragnarok')
            ->table($storageTable)
            ->where('account_id', $accountId)
            ->get()
            ->toArray();
    }
}

