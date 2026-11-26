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
        $guildTable = $this->getTable();
        $charTable = $this->emulator->getTableName('char');

        return DB::connection('ragnarok')
            ->table($guildTable . ' as g')
            ->leftJoin($charTable . ' as gm', 'g.char_id', '=', 'gm.char_id')
            ->where('g.guild_id', $guildId)
            ->select([
                'g.*',
                DB::raw('COALESCE(gm.name, g.master) as master'),
                'gm.name as master_char_name',
            ])
            ->first();
    }

    public function findByName(string $name): ?object
    {
        $guildTable = $this->getTable();
        $charTable = $this->emulator->getTableName('char');

        return DB::connection('ragnarok')
            ->table($guildTable . ' as g')
            ->leftJoin($charTable . ' as gm', 'g.char_id', '=', 'gm.char_id')
            ->where('g.name', $name)
            ->select([
                'g.*',
                DB::raw('COALESCE(gm.name, g.master) as master'),
                'gm.name as master_char_name',
            ])
            ->first();
    }

    public function getMembers(int $guildId): array
    {
        $guildTable = $this->getTable();
        $charTable = $this->emulator->getTableName('char');
        $memberTable = $this->emulator->getTableName('guild_member');

        $guild = DB::connection('ragnarok')->table($guildTable)->where('guild_id', $guildId)->select('char_id')->first();
        $masterCharId = $guild ? (int) $guild->char_id : 0;

        return DB::connection('ragnarok')
            ->table($charTable . ' as c')
            ->leftJoin($memberTable . ' as gm', function ($join) use ($guildId) {
                $join->on('gm.char_id', '=', 'c.char_id')
                     ->where('gm.guild_id', '=', $guildId);
            })
            ->where(function ($q) use ($guildId, $masterCharId) {
                $q->where('c.guild_id', $guildId);
                if ($masterCharId > 0) {
                    $q->orWhere('c.char_id', $masterCharId);
                }
            })
            ->select([
                'c.char_id',
                'c.account_id',
                'c.name',
                'c.class',
                'c.base_level as lv',
                'c.job_level',
                'c.online',
                DB::raw('COALESCE(gm.exp, 0) as exp'),
                DB::raw('COALESCE(gm.position, 0) as position'),
            ])
            ->orderByRaw('CASE WHEN c.char_id = ? THEN 0 ELSE 1 END', [$masterCharId])
            ->orderByDesc('exp')
            ->orderByDesc('c.base_level')
            ->get()
            ->toArray();
    }

    public function getGuilds(int $limit = 20, int $page = 1): array
    {
        $offset = ($page - 1) * $limit;
        $guildTable = $this->getTable();
        $charTable = $this->emulator->getTableName('char');

        return DB::connection('ragnarok')
            ->table($guildTable . ' as g')
            ->leftJoin($charTable . ' as gm', 'g.char_id', '=', 'gm.char_id')
            ->select([
                'g.guild_id',
                'g.char_id',
                'g.name',
                DB::raw('COALESCE(gm.name, g.master) as master'),
                'g.guild_lv',
                'g.connect_member',
                'g.max_member',
                'g.average_lv',
                'g.exp',
            ])
            ->orderBy('g.guild_lv', 'desc')
            ->orderBy('g.exp', 'desc')
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

