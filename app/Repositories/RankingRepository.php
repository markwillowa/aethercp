<?php

namespace App\Repositories;

use App\Contracts\EmulatorInterface;
use App\Contracts\Repositories\RankingRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class RankingRepository implements RankingRepositoryInterface
{
    public function __construct(
        protected EmulatorInterface $emulator
    ) {}

    protected function getCharTable(): string
    {
        return $this->emulator->quoteIdentifier($this->emulator->getTableName('char'));
    }

    protected function getGuildTable(): string
    {
        return $this->emulator->getTableName('guild');
    }

    protected function shouldFilterGm(): bool
    {
        return (bool) config('emulator.hide_gm_from_rankings', true);
    }

    public function getBaseLevelRanking(int $limit = 25): array
    {
        $ttl = (int) config('aether.cache.rankings', 300);

        return Cache::remember("aether_ranking_base_level_{$limit}", $ttl, function () use ($limit) {
            $query = DB::connection('ragnarok')
                ->table(DB::raw($this->getCharTable()))
                ->select('char_id', 'name', 'class', 'base_level', 'job_level', 'base_exp');

            if ($this->shouldFilterGm()) {
                $loginTable = $this->emulator->getTableName('login');
                $gmThreshold = (int) config('emulator.gm_level_threshold', 1);
                $query->whereNotIn('account_id', function ($sub) use ($loginTable, $gmThreshold) {
                    $sub->select('account_id')->from($loginTable)->where('group_id', '>=', $gmThreshold);
                });
            }

            return $query->orderBy('base_level', 'desc')
                ->orderBy('base_exp', 'desc')
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }

    public function getJobLevelRanking(int $limit = 25): array
    {
        $ttl = (int) config('aether.cache.rankings', 300);

        return Cache::remember("aether_ranking_job_level_{$limit}", $ttl, function () use ($limit) {
            $query = DB::connection('ragnarok')
                ->table(DB::raw($this->getCharTable()))
                ->select('char_id', 'name', 'class', 'base_level', 'job_level', 'job_exp');

            if ($this->shouldFilterGm()) {
                $loginTable = $this->emulator->getTableName('login');
                $gmThreshold = (int) config('emulator.gm_level_threshold', 1);
                $query->whereNotIn('account_id', function ($sub) use ($loginTable, $gmThreshold) {
                    $sub->select('account_id')->from($loginTable)->where('group_id', '>=', $gmThreshold);
                });
            }

            return $query->orderBy('job_level', 'desc')
                ->orderBy('job_exp', 'desc')
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }

    public function getZenyRanking(int $limit = 25): array
    {
        $ttl = (int) config('aether.cache.rankings', 300);

        return Cache::remember("aether_ranking_zeny_{$limit}", $ttl, function () use ($limit) {
            $query = DB::connection('ragnarok')
                ->table(DB::raw($this->getCharTable()))
                ->select('char_id', 'name', 'class', 'base_level', 'zeny');

            if ($this->shouldFilterGm()) {
                $loginTable = $this->emulator->getTableName('login');
                $gmThreshold = (int) config('emulator.gm_level_threshold', 1);
                $query->whereNotIn('account_id', function ($sub) use ($loginTable, $gmThreshold) {
                    $sub->select('account_id')->from($loginTable)->where('group_id', '>=', $gmThreshold);
                });
            }

            return $query->orderBy('zeny', 'desc')
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }

    public function getMvpRanking(int $limit = 25): array
    {
        $ttl = (int) config('aether.cache.rankings', 300);

        return Cache::remember("aether_ranking_mvp_{$limit}", $ttl, function () use ($limit) {
            $query = DB::connection('ragnarok')
                ->table(DB::raw($this->getCharTable()))
                ->select('char_id', 'name', 'class', 'base_level', 'fame');

            if ($this->shouldFilterGm()) {
                $loginTable = $this->emulator->getTableName('login');
                $gmThreshold = (int) config('emulator.gm_level_threshold', 1);
                $query->whereNotIn('account_id', function ($sub) use ($loginTable, $gmThreshold) {
                    $sub->select('account_id')->from($loginTable)->where('group_id', '>=', $gmThreshold);
                });
            }

            return $query->orderBy('fame', 'desc')
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }

    public function getGuildRanking(int $limit = 25): array
    {
        $ttl = (int) config('aether.cache.rankings', 300);

        return Cache::remember("aether_ranking_guild_{$limit}", $ttl, function () use ($limit) {
            return DB::connection('ragnarok')
                ->table($this->getGuildTable())
                ->select('guild_id', 'name', 'master', 'guild_lv', 'connect_member', 'max_member', 'average_lv', 'exp')
                ->orderBy('guild_lv', 'desc')
                ->orderBy('exp', 'desc')
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }
}

