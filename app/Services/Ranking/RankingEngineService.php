<?php

namespace App\Services\Ranking;

use App\Contracts\EmulatorInterface;
use App\Models\SiteSetting;
use App\Services\Emulator\JobHelper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RankingEngineService
{
    public const CATEGORIES = ['lvl', 'job', 'pvp', 'mvp', 'guild'];

    public function __construct(
        protected EmulatorInterface $emulator
    ) {}

    /**
     * Normalize category aliases (e.g., 'base' -> 'lvl').
     */
    public function normalizeCategory(string $category): string
    {
        return match (strtolower(trim($category))) {
            'base', 'level', 'lvl' => 'lvl',
            'job', 'job_level' => 'job',
            'pvp', 'kills' => 'pvp',
            'mvp', 'boss' => 'mvp',
            'guild', 'guilds' => 'guild',
            'zeny' => 'zeny',
            default => 'lvl',
        };
    }

    /**
     * Retrieve centralized ranking configuration from SiteSetting with safe defaults.
     */
    public function getConfig(): array
    {
        $maxBase = (int) SiteSetting::get('ranking_max_base_level', config('aether.rates.max_base_level', 99));
        $maxJob = (int) SiteSetting::get('ranking_max_job_level', config('aether.rates.max_job_level', 50));

        $enabledRaw = SiteSetting::get('ranking_enabled_categories', null);
        $enabledCategories = $enabledRaw ? (json_decode($enabledRaw, true) ?: self::CATEGORIES) : self::CATEGORIES;

        $guildWeightsRaw = SiteSetting::get('ranking_guild_weights', null);
        $guildWeights = $guildWeightsRaw ? (json_decode($guildWeightsRaw, true) ?: []) : [
            'castles' => 100,
            'level' => 10,
            'members' => 5,
        ];

        return [
            'max_base_level' => max(1, $maxBase),
            'max_job_level' => max(1, $maxJob),
            'homepage_limit' => max(1, min(25, (int) SiteSetting::get('ranking_homepage_limit', 5))),
            'leaderboard_limit' => max(5, min(100, (int) SiteSetting::get('ranking_leaderboard_limit', 25))),
            'cache_ttl' => max(0, (int) SiteSetting::get('ranking_cache_ttl', config('aether.cache.rankings', 60))),
            'guild_method' => SiteSetting::get('ranking_guild_method', 'combined'), // castles, level, members, combined
            'guild_weights' => $guildWeights,
            'enabled_categories' => $enabledCategories,
            'public_visibility' => (bool) SiteSetting::get('ranking_public_visibility', true),
            'hide_gm' => (bool) config('emulator.hide_gm_from_rankings', true),
            'gm_threshold' => (int) config('emulator.gm_level_threshold', 1),
            'excluded_chars' => $this->parseCsvIds(SiteSetting::get('ranking_excluded_chars', '')),
            'excluded_accounts' => $this->parseCsvIds(SiteSetting::get('ranking_excluded_accounts', '')),
            'excluded_guilds' => $this->parseCsvIds(SiteSetting::get('ranking_excluded_guilds', '')),
        ];
    }

    /**
     * Get paginated or limited leaderboard data for any category.
     */
    public function getLeaderboard(string $category, ?int $limit = null, int $page = 1, array $filters = []): array
    {
        $cat = $this->normalizeCategory($category);
        $cfg = $this->getConfig();
        $perPage = $limit ?? $cfg['leaderboard_limit'];
        $page = max(1, $page);
        $search = trim((string) ($filters['search'] ?? ''));
        $classFilter = isset($filters['class']) && $filters['class'] !== '' ? (int) $filters['class'] : null;

        $cacheKey = sprintf(
            'aether_rank_v2_%s_%d_%d_%s_%s',
            $cat,
            $perPage,
            $page,
            md5($search),
            $classFilter !== null ? (string) $classFilter : 'all'
        );

        $ttl = $search !== '' || $classFilter !== null ? 15 : $cfg['cache_ttl'];

        $payload = Cache::remember($cacheKey, $ttl, function () use ($cat, $perPage, $page, $search, $classFilter, $cfg) {
            return match ($cat) {
                'lvl' => $this->buildLevelLeaderboard($perPage, $page, $search, $classFilter, $cfg),
                'job' => $this->buildJobLeaderboard($perPage, $page, $search, $classFilter, $cfg),
                'pvp' => $this->buildPvpLeaderboard($perPage, $page, $search, $classFilter, $cfg),
                'mvp' => $this->buildMvpLeaderboard($perPage, $page, $search, $classFilter, $cfg),
                'guild' => $this->buildGuildLeaderboard($perPage, $page, $search, $cfg),
                'zeny' => $this->buildZenyLeaderboard($perPage, $page, $search, $classFilter, $cfg),
                default => $this->buildLevelLeaderboard($perPage, $page, $search, $classFilter, $cfg),
            };
        });

        return $payload;
    }

    /**
     * Invalidate leaderboard caches.
     */
    public function invalidateCache(?string $category = null): void
    {
        // With file/database cache drivers, clearing tagged or specific keys;
        // for full consistency on event updates we can bump a cache version or flush ranking keys.
        Cache::flush();
    }

    protected function buildLevelLeaderboard(int $perPage, int $page, string $search, ?int $classFilter, array $cfg): array
    {
        $conn = DB::connection('ragnarok');
        $charTable = $this->emulator->getTableName('char');
        $guildTable = $this->emulator->getTableName('guild');

        $query = $conn->table("{$charTable} as c")
            ->leftJoin("{$guildTable} as g", 'c.guild_id', '=', 'g.guild_id')
            ->select(
                'c.char_id',
                'c.account_id',
                'c.name',
                'c.class',
                'c.sex',
                'c.base_level',
                'c.job_level',
                'c.base_exp',
                'c.guild_id',
                'c.online',
                'c.last_login',
                'g.name as guild_name',
                'g.emblem_len as guild_emblem_len'
            );

        $this->applyCharacterFilters($query, 'c', $search, $classFilter, $cfg);

        $total = (clone $query)->count();
        $rows = $query->orderByDesc('c.base_level')
            ->orderByDesc('c.base_exp')
            ->orderBy('c.char_id')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        $maxBase = $cfg['max_base_level'];
        $items = [];
        $startRank = (($page - 1) * $perPage) + 1;

        foreach ($rows as $idx => $row) {
            $rank = $startRank + $idx;
            $reachedMax = (int) $row->base_level >= $maxBase;
            $achievement = null;
            if ($reachedMax) {
                $achievement = ($rank === 1) ? 'First to Max Level' : 'Max Level';
            }

            $items[] = $this->formatCharacterEntry($row, $rank, [
                'primary_stat_label' => 'Base Lv.',
                'primary_stat_value' => (int) $row->base_level,
                'secondary_stat' => 'Job Lv. ' . (int) $row->job_level,
                'achievement_label' => $achievement,
                'achieved_at' => null, // Will be populated by Stage 2 achievement event table
            ]);
        }

        return $this->wrapPayload('lvl', $items, $total, $perPage, $page);
    }

    protected function buildJobLeaderboard(int $perPage, int $page, string $search, ?int $classFilter, array $cfg): array
    {
        $conn = DB::connection('ragnarok');
        $charTable = $this->emulator->getTableName('char');
        $guildTable = $this->emulator->getTableName('guild');

        $query = $conn->table("{$charTable} as c")
            ->leftJoin("{$guildTable} as g", 'c.guild_id', '=', 'g.guild_id')
            ->select(
                'c.char_id',
                'c.account_id',
                'c.name',
                'c.class',
                'c.sex',
                'c.base_level',
                'c.job_level',
                'c.job_exp',
                'c.guild_id',
                'c.online',
                'c.last_login',
                'g.name as guild_name',
                'g.emblem_len as guild_emblem_len'
            );

        $this->applyCharacterFilters($query, 'c', $search, $classFilter, $cfg);

        $total = (clone $query)->count();
        $rows = $query->orderByDesc('c.job_level')
            ->orderByDesc('c.job_exp')
            ->orderBy('c.char_id')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        $maxJob = $cfg['max_job_level'];
        $items = [];
        $startRank = (($page - 1) * $perPage) + 1;

        foreach ($rows as $idx => $row) {
            $rank = $startRank + $idx;
            $reachedMax = (int) $row->job_level >= $maxJob;
            $achievement = null;
            if ($reachedMax) {
                $achievement = ($rank === 1) ? 'First to Max Job' : 'Max Job Level';
            }

            $items[] = $this->formatCharacterEntry($row, $rank, [
                'primary_stat_label' => 'Job Lv.',
                'primary_stat_value' => (int) $row->job_level,
                'secondary_stat' => 'Base Lv. ' . (int) $row->base_level,
                'achievement_label' => $achievement,
                'achieved_at' => null,
            ]);
        }

        return $this->wrapPayload('job', $items, $total, $perPage, $page);
    }

    protected function buildPvpLeaderboard(int $perPage, int $page, string $search, ?int $classFilter, array $cfg): array
    {
        // Stage 2 will add verified PvP event tables (`ranking_character_stats`).
        // Until then, check if the table exists so we never fabricate PvP kills.
        if (Schema::hasTable('ranking_character_stats')) {
            // Query verified PvP stats when Stage 2 table is migrated
        }

        return $this->wrapPayload('pvp', [], 0, $perPage, $page);
    }

    protected function buildMvpLeaderboard(int $perPage, int $page, string $search, ?int $classFilter, array $cfg): array
    {
        // Stage 2 will add verified MVP kill event tables (`ranking_character_stats`).
        // Until then, return verified records if table exists, otherwise empty (never count ordinary kills).
        if (Schema::hasTable('ranking_character_stats')) {
            // Query verified MVP stats when Stage 2 table is migrated
        }

        return $this->wrapPayload('mvp', [], 0, $perPage, $page);
    }

    protected function buildGuildLeaderboard(int $perPage, int $page, string $search, array $cfg): array
    {
        $conn = DB::connection('ragnarok');
        $guildTable = $this->emulator->getTableName('guild');
        $castleTable = $this->emulator->getTableName('guild_castle');
        $memberTable = $this->emulator->getTableName('guild_member');

        $castleSub = $conn->table($castleTable)
            ->select('guild_id', DB::raw('COUNT(*) as castles_count'))
            ->where('guild_id', '>', 0)
            ->groupBy('guild_id');

        $memberSub = $conn->table($memberTable)
            ->select('guild_id', DB::raw('COUNT(*) as members_count'))
            ->groupBy('guild_id');

        $method = $cfg['guild_method'];
        $w = $cfg['guild_weights'];
        $wCastles = (int) ($w['castles'] ?? 100);
        $wLevel = (int) ($w['level'] ?? 10);
        $wMembers = (int) ($w['members'] ?? 5);

        $scoreExpr = match ($method) {
            'castles' => "COALESCE(gc.castles_count, 0)",
            'level' => "g.guild_lv",
            'members' => "COALESCE(gm.members_count, 0)",
            default => "(COALESCE(gc.castles_count, 0) * {$wCastles}) + (g.guild_lv * {$wLevel}) + (COALESCE(gm.members_count, 0) * {$wMembers})",
        };

        $query = $conn->table("{$guildTable} as g")
            ->leftJoinSub($castleSub, 'gc', 'g.guild_id', '=', 'gc.guild_id')
            ->leftJoinSub($memberSub, 'gm', 'g.guild_id', '=', 'gm.guild_id')
            ->select(
                'g.guild_id',
                'g.name',
                'g.master',
                'g.guild_lv',
                'g.connect_member',
                'g.max_member',
                'g.average_lv',
                'g.exp',
                'g.emblem_len',
                DB::raw('COALESCE(gc.castles_count, 0) as castles_count'),
                DB::raw('COALESCE(gm.members_count, 0) as members_count'),
                DB::raw("{$scoreExpr} as ranking_score")
            );

        if (!empty($cfg['excluded_guilds'])) {
            $query->whereNotIn('g.guild_id', $cfg['excluded_guilds']);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('g.name', 'like', "%{$search}%")
                  ->orWhere('g.master', 'like', "%{$search}%");
            });
        }

        $total = (clone $query)->count();
        $rows = $query->orderByDesc('ranking_score')
            ->orderByDesc('castles_count')
            ->orderByDesc('g.guild_lv')
            ->orderByDesc('g.exp')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        $items = [];
        $startRank = (($page - 1) * $perPage) + 1;

        foreach ($rows as $idx => $row) {
            $rank = $startRank + $idx;
            $badgeLabel = match (true) {
                $rank === 1 => 'Top 1 Guild',
                $rank === 2 => 'Top 2 Guild',
                $rank === 3 => 'Top 3 Guild',
                $rank <= 10 => 'Top 4–10 Guild',
                default => null,
            };

            $items[] = [
                'rank' => $rank,
                'guild_id' => (int) $row->guild_id,
                'name' => (string) $row->name,
                'master' => (string) $row->master,
                'guild_lv' => (int) $row->guild_lv,
                'members_count' => (int) $row->members_count,
                'connect_member' => (int) $row->connect_member,
                'max_member' => (int) $row->max_member,
                'average_lv' => (int) $row->average_lv,
                'exp' => (int) $row->exp,
                'castles_count' => (int) $row->castles_count,
                'score' => (int) $row->ranking_score,
                'emblem_url' => ((int) $row->emblem_len > 0) ? route('guilds.emblem', $row->guild_id) : null,
                'profile_url' => route('guilds.show', $row->guild_id),
                'badge_label' => $badgeLabel,
                'badges' => [],
            ];
        }

        return $this->wrapPayload('guild', $items, $total, $perPage, $page);
    }

    protected function buildZenyLeaderboard(int $perPage, int $page, string $search, ?int $classFilter, array $cfg): array
    {
        $conn = DB::connection('ragnarok');
        $charTable = $this->emulator->getTableName('char');
        $guildTable = $this->emulator->getTableName('guild');

        $query = $conn->table("{$charTable} as c")
            ->leftJoin("{$guildTable} as g", 'c.guild_id', '=', 'g.guild_id')
            ->select(
                'c.char_id',
                'c.account_id',
                'c.name',
                'c.class',
                'c.sex',
                'c.base_level',
                'c.job_level',
                'c.zeny',
                'c.guild_id',
                'c.online',
                'g.name as guild_name',
                'g.emblem_len as guild_emblem_len'
            );

        $this->applyCharacterFilters($query, 'c', $search, $classFilter, $cfg);

        $total = (clone $query)->count();
        $rows = $query->orderByDesc('c.zeny')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        $items = [];
        $startRank = (($page - 1) * $perPage) + 1;

        foreach ($rows as $idx => $row) {
            $rank = $startRank + $idx;
            $items[] = $this->formatCharacterEntry($row, $rank, [
                'primary_stat_label' => 'Zeny',
                'primary_stat_value' => number_format((int) $row->zeny) . ' Z',
                'secondary_stat' => 'Lv. ' . (int) $row->base_level,
            ]);
        }

        return $this->wrapPayload('zeny', $items, $total, $perPage, $page);
    }

    protected function applyCharacterFilters($query, string $alias, string $search, ?int $classFilter, array $cfg): void
    {
        if ($cfg['hide_gm']) {
            $loginTable = $this->emulator->getTableName('login');
            $gmThreshold = $cfg['gm_threshold'];
            $query->whereNotIn("{$alias}.account_id", function ($sub) use ($loginTable, $gmThreshold) {
                $sub->select('account_id')->from($loginTable)->where('group_id', '>=', $gmThreshold);
            });
        }

        if (!empty($cfg['excluded_chars'])) {
            $query->whereNotIn("{$alias}.char_id", $cfg['excluded_chars']);
        }
        if (!empty($cfg['excluded_accounts'])) {
            $query->whereNotIn("{$alias}.account_id", $cfg['excluded_accounts']);
        }
        if (!empty($cfg['excluded_guilds'])) {
            $query->whereNotIn("{$alias}.guild_id", $cfg['excluded_guilds']);
        }

        if ($search !== '') {
            $query->where("{$alias}.name", 'like', "%{$search}%");
        }

        if ($classFilter !== null) {
            $query->where("{$alias}.class", $classFilter);
        }
    }

    protected function formatCharacterEntry(object $row, int $rank, array $extra = []): array
    {
        $guildId = (int) ($row->guild_id ?? 0);
        $emblemLen = (int) ($row->guild_emblem_len ?? 0);

        return array_merge([
            'rank' => $rank,
            'char_id' => (int) $row->char_id,
            'name' => (string) $row->name,
            'class' => (int) $row->class,
            'job_name' => JobHelper::getName((int) $row->class),
            'job_badge_color' => JobHelper::getBadgeColor((int) $row->class),
            'sex' => (string) ($row->sex ?? 'M'),
            'base_level' => (int) $row->base_level,
            'job_level' => (int) $row->job_level,
            'guild_id' => $guildId,
            'guild_name' => $row->guild_name ?? null,
            'guild_emblem_url' => ($guildId > 0 && $emblemLen > 0) ? route('guilds.emblem', $guildId) : null,
            'online' => (bool) ($row->online ?? false),
            'profile_url' => route('characters.show', $row->name),
            'badges' => [],
        ], $extra);
    }

    protected function wrapPayload(string $category, array $items, int $total, int $perPage, int $page): array
    {
        return [
            'category' => $category,
            'items' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => max(1, (int) ceil($total / max(1, $perPage))),
            'updated_at' => now()->toIso8601String(),
            'updated_human' => now()->format('M d, Y H:i:s'),
        ];
    }

    protected function parseCsvIds(?string $csv): array
    {
        if (empty($csv)) {
            return [];
        }

        return array_values(array_filter(array_map('intval', explode(',', $csv)), fn($v) => $v > 0));
    }
}
