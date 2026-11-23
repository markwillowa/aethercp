<?php

namespace App\Repositories;

use App\Contracts\EmulatorInterface;
use App\Contracts\Repositories\GuildRepositoryInterface;
use Illuminate\Support\Facades\DB;

class GuildRepository implements GuildRepositoryInterface
{
    public function __construct(
        protected EmulatorInterface $emulator
    ) {}

    protected function getTable(): string
    {
        return $this->emulator->getTableName('guild');
    }

    public function findById(int $guildId): ?object
    {
        return DB::connection('ragnarok')
            ->table($this->getTable())
            ->where('guild_id', $guildId)
            ->first();
    }

    public function findByName(string $name): ?object
    {
        return DB::connection('ragnarok')
            ->table($this->getTable())
            ->where('name', $name)
            ->first();
    }

    public function getMembers(int $guildId): array
    {
        $memberTable = $this->emulator->getTableName('guild_member');
        return DB::connection('ragnarok')
            ->table($memberTable)
            ->where('guild_id', $guildId)
            ->orderBy('exp', 'desc')
            ->get()
            ->toArray();
    }

    public function getGuilds(int $limit = 20, int $page = 1): array
    {
        $offset = ($page - 1) * $limit;

        return DB::connection('ragnarok')
            ->table($this->getTable())
            ->select('guild_id', 'name', 'master', 'guild_lv', 'connect_member', 'max_member', 'average_lv', 'exp')
            ->orderBy('guild_lv', 'desc')
            ->orderBy('exp', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function getCastles(): array
    {
        $castleTable = $this->emulator->getTableName('guild_castle');
        $guildTable = $this->getTable();

        try {
            return DB::connection('ragnarok')
                ->table($castleTable . ' as c')
                ->leftJoin($guildTable . ' as g', 'c.guild_id', '=', 'g.guild_id')
                ->select('c.castle_id', 'c.guild_id', 'c.economy', 'c.defense', 'c.trigger_defense', 'g.name as guild_name', 'g.master as guild_master')
                ->get()
                ->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function getEmblemRaw(int $guildId): ?string
    {
        $record = DB::connection('ragnarok')
            ->table($this->getTable())
            ->where('guild_id', $guildId)
            ->select('emblem_data', 'emblem_len')
            ->first();

        if (!$record || empty($record->emblem_data)) {
            return null;
        }

        return $record->emblem_data;
    }
}

