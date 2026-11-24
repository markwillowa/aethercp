<?php

namespace App\Services\Ranking;

use App\Contracts\EmulatorInterface;
use App\Models\EmulatorSyncLog;
use App\Models\RankingCharacterStat;
use App\Models\RankingEvent;
use App\Models\SiteSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class RankingSyncService
{
    public function __construct(
        protected EmulatorInterface $emulator,
        protected RankingEngineService $rankingEngine
    ) {}

    /**
     * Validate, deduplicate, and process a single emulator event.
     */
    public function ingestEvent(array $data): array
    {
        $startedAt = microtime(true);
        $eventUuid = trim((string) ($data['event_uuid'] ?? ''));
        $eventType = trim((string) ($data['event_type'] ?? ''));

        if ($eventUuid === '' || $eventType === '') {
            return ['ok' => false, 'status' => 'rejected', 'reason' => 'Missing event_uuid or event_type.'];
        }

        // 1. Deduplication check
        if (RankingEvent::where('event_uuid', $eventUuid)->exists()) {
            return ['ok' => true, 'status' => 'duplicate', 'reason' => 'Event already processed.'];
        }

        $occurredAt = !empty($data['occurred_at'])
            ? Carbon::parse($data['occurred_at'])
            : now();

        $charId = isset($data['char_id']) ? (int) $data['char_id'] : null;
        $targetCharId = isset($data['target_char_id']) ? (int) $data['target_char_id'] : null;
        $guildId = isset($data['guild_id']) ? (int) $data['guild_id'] : null;
        $mobId = isset($data['mob_id']) ? (int) $data['mob_id'] : null;
        $value = isset($data['value']) ? (int) $data['value'] : 1;

        $event = RankingEvent::create([
            'event_uuid' => $eventUuid,
            'event_type' => $eventType,
            'char_id' => $charId,
            'account_id' => isset($data['account_id']) ? (int) $data['account_id'] : null,
            'target_char_id' => $targetCharId,
            'guild_id' => $guildId,
            'mob_id' => $mobId,
            'value' => $value,
            'payload' => $data['payload'] ?? $data,
            'status' => 'pending',
            'occurred_at' => $occurredAt,
        ]);

        try {
            $result = match ($eventType) {
                'max_base_level' => $this->handleMaxBaseLevel($event),
                'max_job_level' => $this->handleMaxJobLevel($event),
                'pvp_kill' => $this->handlePvpKill($event),
                'mvp_kill' => $this->handleMvpKill($event),
                'guild_level', 'guild_castle', 'guild_member' => $this->handleGuildEvent($event),
                default => ['status' => 'rejected', 'reason' => "Unsupported event type [{$eventType}]."],
            };

            $event->status = $result['status'];
            $event->rejection_reason = $result['reason'] ?? null;
            $event->processed_at = now();
            $event->save();

            if ($result['status'] === 'processed') {
                if ($event->char_id) {
                    app(BadgeService::class)->evaluateCharacterBadges((int) $event->char_id);
                }
                if (in_array($eventType, ['guild_level', 'guild_castle', 'guild_member'], true)) {
                    app(BadgeService::class)->evaluateGuildBadges();
                }
                $this->rankingEngine->invalidateCache();
            }

            return [
                'ok' => $result['status'] === 'processed',
                'status' => $result['status'],
                'reason' => $result['reason'] ?? null,
                'event_id' => $event->id,
                'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
            ];
        } catch (\Throwable $e) {
            $event->status = 'failed';
            $event->rejection_reason = Str::limit($e->getMessage(), 250);
            $event->save();

            Log::error('RankingSyncService event failure', [
                'event_uuid' => $eventUuid,
                'error' => $e->getMessage(),
            ]);

            return [
                'ok' => false,
                'status' => 'failed',
                'reason' => $e->getMessage(),
            ];
        }
    }

    protected function handleMaxBaseLevel(RankingEvent $event): array
    {
        $char = $this->findRagnarokChar($event->char_id);
        if (!$char) {
            return ['status' => 'rejected', 'reason' => 'Character not found in rAthena database.'];
        }

        $cfg = $this->rankingEngine->getConfig();
        $achievedLevel = max((int) $char->base_level, (int) $event->value);

        if ($achievedLevel < $cfg['max_base_level']) {
            return ['status' => 'rejected', 'reason' => "Level {$achievedLevel} is below configured max base level ({$cfg['max_base_level']})."];
        }

        $event->account_id = (int) $char->account_id;
        $stat = $this->getOrCreateCharStat($char);

        // Preserve earliest achievement timestamp — never overwrite an earlier max-level record
        if ($stat->max_base_reached_at === null || $event->occurred_at->lt($stat->max_base_reached_at)) {
            $stat->max_base_reached_at = $event->occurred_at;
            $stat->max_base_level_achieved = $achievedLevel;
        }
        $stat->last_event_at = now();
        $stat->save();

        return ['status' => 'processed'];
    }

    protected function handleMaxJobLevel(RankingEvent $event): array
    {
        $char = $this->findRagnarokChar($event->char_id);
        if (!$char) {
            return ['status' => 'rejected', 'reason' => 'Character not found in rAthena database.'];
        }

        $cfg = $this->rankingEngine->getConfig();
        $achievedLevel = max((int) $char->job_level, (int) $event->value);

        if ($achievedLevel < $cfg['max_job_level']) {
            return ['status' => 'rejected', 'reason' => "Job level {$achievedLevel} is below configured max job level ({$cfg['max_job_level']})."];
        }

        $event->account_id = (int) $char->account_id;
        $stat = $this->getOrCreateCharStat($char);

        if ($stat->max_job_reached_at === null || $event->occurred_at->lt($stat->max_job_reached_at)) {
            $stat->max_job_reached_at = $event->occurred_at;
            $stat->max_job_level_achieved = $achievedLevel;
        }
        $stat->last_event_at = now();
        $stat->save();

        return ['status' => 'processed'];
    }

    protected function handlePvpKill(RankingEvent $event): array
    {
        if (!$event->char_id || !$event->target_char_id) {
            return ['status' => 'rejected', 'reason' => 'Missing killer or victim char_id.'];
        }

        if ($event->char_id === $event->target_char_id) {
            return ['status' => 'rejected', 'reason' => 'Self-kill rejected by anti-abuse policy.'];
        }

        $killer = $this->findRagnarokChar($event->char_id);
        $victim = $this->findRagnarokChar($event->target_char_id);
        if (!$killer || !$victim) {
            return ['status' => 'rejected', 'reason' => 'Killer or victim character not found in rAthena.'];
        }

        // Same-account kill farming check
        if ((int) $killer->account_id === (int) $victim->account_id) {
            return ['status' => 'rejected', 'reason' => 'Same-account kill farming rejected.'];
        }

        // Anti-abuse cooldown between the same killer & victim
        $cooldownSeconds = max(0, (int) SiteSetting::get('ranking_pvp_cooldown_seconds', 300));
        if ($cooldownSeconds > 0) {
            $recentKill = RankingEvent::where('event_type', 'pvp_kill')
                ->where('status', 'processed')
                ->where('char_id', $event->char_id)
                ->where('target_char_id', $event->target_char_id)
                ->where('occurred_at', '>=', $event->occurred_at->copy()->subSeconds($cooldownSeconds))
                ->exists();

            if ($recentKill) {
                return ['status' => 'rejected', 'reason' => "Repeated kill within {$cooldownSeconds}s anti-abuse cooldown."];
            }
        }

        $event->account_id = (int) $killer->account_id;

        DB::transaction(function () use ($killer, $victim) {
            $kStat = $this->getOrCreateCharStat($killer);
            $kStat->increment('pvp_kills');
            $kStat->last_event_at = now();
            $kStat->save();

            $vStat = $this->getOrCreateCharStat($victim);
            $vStat->increment('pvp_deaths');
            $vStat->last_event_at = now();
            $vStat->save();
        });

        return ['status' => 'processed'];
    }

    protected function handleMvpKill(RankingEvent $event): array
    {
        $char = $this->findRagnarokChar($event->char_id);
        if (!$char) {
            return ['status' => 'rejected', 'reason' => 'Character not found in rAthena database.'];
        }

        if (!$event->mob_id || !$this->isVerifiedMvpMob((int) $event->mob_id)) {
            return ['status' => 'rejected', 'reason' => "Mob ID [{$event->mob_id}] is not a verified MVP monster in mob_db."];
        }

        $event->account_id = (int) $char->account_id;
        $stat = $this->getOrCreateCharStat($char);
        $stat->increment('mvp_kills');
        $stat->last_event_at = now();
        $stat->save();

        return ['status' => 'processed'];
    }

    protected function handleGuildEvent(RankingEvent $event): array
    {
        if (!$event->guild_id) {
            return ['status' => 'rejected', 'reason' => 'Missing guild_id.'];
        }

        $exists = DB::connection('ragnarok')
            ->table($this->emulator->getTableName('guild'))
            ->where('guild_id', $event->guild_id)
            ->exists();

        if (!$exists && $event->event_type !== 'guild_castle') {
            return ['status' => 'rejected', 'reason' => 'Guild not found in rAthena database.'];
        }

        return ['status' => 'processed'];
    }

    /**
     * Poll optional SQL bridge table (`cp_ranking_events` in ragnarok DB),
     * reconcile max-level characters, and refresh guild caches.
     */
    public function runFullSync(): array
    {
        $startedAt = microtime(true);
        $processed = 0;
        $rejected = 0;
        $error = null;

        try {
            // 1. Pull pending rows from ragnarok.cp_ranking_events if NPC SQL bridge is active
            $ragnarokConn = DB::connection('ragnarok');
            if (Schema::connection('ragnarok')->hasTable('cp_ranking_events')) {
                $pending = $ragnarokConn->table('cp_ranking_events')
                    ->where('synced', 0)
                    ->orderBy('id')
                    ->limit(250)
                    ->get();

                foreach ($pending as $row) {
                    $res = $this->ingestEvent([
                        'event_uuid' => $row->event_uuid ?? ('sql-' . $row->id),
                        'event_type' => $row->event_type,
                        'char_id' => $row->char_id,
                        'account_id' => $row->account_id ?? null,
                        'target_char_id' => $row->target_char_id ?? null,
                        'guild_id' => $row->guild_id ?? null,
                        'mob_id' => $row->mob_id ?? null,
                        'value' => $row->value ?? 1,
                        'occurred_at' => $row->occurred_at ?? now()->toDateTimeString(),
                    ]);

                    if ($res['status'] === 'processed' || $res['status'] === 'duplicate') {
                        $processed++;
                    } else {
                        $rejected++;
                    }

                    $ragnarokConn->table('cp_ranking_events')
                        ->where('id', $row->id)
                        ->update(['synced' => 1]);
                }
            }

            // 2. Reconcile any max-level characters not yet recorded in ranking_character_stats
            $cfg = $this->rankingEngine->getConfig();
            $charTable = $this->emulator->getTableName('char');

            $maxBaseChars = $ragnarokConn->table($charTable)
                ->select('char_id', 'account_id', 'base_level', 'last_login')
                ->where('base_level', '>=', $cfg['max_base_level'])
                ->get();

            foreach ($maxBaseChars as $c) {
                $stat = RankingCharacterStat::firstOrCreate(
                    ['char_id' => (int) $c->char_id],
                    ['account_id' => (int) $c->account_id]
                );

                if ($stat->max_base_reached_at === null) {
                    $stat->max_base_reached_at = $c->last_login ? Carbon::parse($c->last_login) : now();
                    $stat->max_base_level_achieved = (int) $c->base_level;
                    $stat->save();
                    $processed++;
                }
            }

            $maxJobChars = $ragnarokConn->table($charTable)
                ->select('char_id', 'account_id', 'job_level', 'last_login')
                ->where('job_level', '>=', $cfg['max_job_level'])
                ->get();

            foreach ($maxJobChars as $c) {
                $stat = RankingCharacterStat::firstOrCreate(
                    ['char_id' => (int) $c->char_id],
                    ['account_id' => (int) $c->account_id]
                );

                if ($stat->max_job_reached_at === null) {
                    $stat->max_job_reached_at = $c->last_login ? Carbon::parse($c->last_login) : now();
                    $stat->max_job_level_achieved = (int) $c->job_level;
                    $stat->save();
                    $processed++;
                }
            }

            // 3. Recalculate badges & invalidate leaderboard cache so guilds/castles/levels reflect live DB
            app(BadgeService::class)->recalculateAll();
            $this->rankingEngine->invalidateCache();

            $status = 'success';
        } catch (\Throwable $e) {
            $status = 'failed';
            $error = $e->getMessage();
            Log::error('RankingSyncService::runFullSync failed', ['error' => $error]);
        }

        $durationMs = (int) round((microtime(true) - $startedAt) * 1000);

        EmulatorSyncLog::create([
            'sync_type' => 'reconciliation',
            'status' => $status,
            'events_processed' => $processed,
            'events_rejected' => $rejected,
            'error_message' => $error,
            'duration_ms' => $durationMs,
            'synced_at' => now(),
        ]);

        return [
            'status' => $status,
            'processed' => $processed,
            'rejected' => $rejected,
            'duration_ms' => $durationMs,
            'error' => $error,
        ];
    }

    public function getSyncHealth(): array
    {
        $lastSuccess = EmulatorSyncLog::where('status', 'success')->latest('synced_at')->first();
        $lastLog = EmulatorSyncLog::latest('synced_at')->first();
        $ragnarokConnected = true;

        try {
            DB::connection('ragnarok')->getPdo();
        } catch (\Throwable $e) {
            $ragnarokConnected = false;
        }

        return [
            'ragnarok_connected' => $ragnarokConnected,
            'last_success_at' => $lastSuccess?->synced_at,
            'last_sync_status' => $lastLog?->status ?? 'never',
            'total_events_processed' => RankingEvent::where('status', 'processed')->count(),
            'total_events_rejected' => RankingEvent::where('status', 'rejected')->count(),
            'total_events_failed' => RankingEvent::where('status', 'failed')->count(),
        ];
    }

    protected function findRagnarokChar(?int $charId): ?object
    {
        if (!$charId || $charId <= 0) {
            return null;
        }

        return DB::connection('ragnarok')
            ->table($this->emulator->getTableName('char'))
            ->where('char_id', $charId)
            ->first();
    }

    protected function getOrCreateCharStat(object $char): RankingCharacterStat
    {
        return RankingCharacterStat::firstOrCreate(
            ['char_id' => (int) $char->char_id],
            ['account_id' => (int) $char->account_id]
        );
    }

    protected function isVerifiedMvpMob(int $mobId): bool
    {
        $conn = DB::connection('ragnarok');
        foreach (['mob_db', 'mob_db2'] as $tbl) {
            if (Schema::connection('ragnarok')->hasTable($tbl)) {
                $mob = $conn->table($tbl)->where('id', $mobId)->first();
                if ($mob) {
                    $isMvpMode = !empty($mob->mode_mvp) && (int) $mob->mode_mvp > 0;
                    $hasMvpExp = !empty($mob->mvp_exp) && (int) $mob->mvp_exp > 0;
                    if ($isMvpMode || $hasMvpExp) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
