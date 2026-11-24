<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmulatorSyncLog;
use App\Models\SiteSetting;
use App\Services\Ranking\RankingSyncService;
use Illuminate\Http\Request;

class EmulatorEventController extends Controller
{
    public function __construct(
        protected RankingSyncService $syncService
    ) {}

    public function store(Request $request)
    {
        if (!$this->isAuthorized($request)) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized sync token.'], 401);
        }

        $validated = $request->validate([
            'event_uuid' => 'required|string|max:64',
            'event_type' => 'required|string|max:40',
            'char_id' => 'nullable|integer',
            'account_id' => 'nullable|integer',
            'target_char_id' => 'nullable|integer',
            'guild_id' => 'nullable|integer',
            'mob_id' => 'nullable|integer',
            'value' => 'nullable|integer',
            'occurred_at' => 'nullable|date',
            'payload' => 'nullable|array',
        ]);

        $res = $this->syncService->ingestEvent($validated);

        EmulatorSyncLog::create([
            'sync_type' => 'event_ingest',
            'status' => $res['ok'] || $res['status'] === 'duplicate' ? 'success' : 'failed',
            'events_processed' => $res['status'] === 'processed' ? 1 : 0,
            'events_rejected' => $res['status'] === 'rejected' ? 1 : 0,
            'error_message' => $res['status'] === 'failed' ? ($res['reason'] ?? null) : null,
            'duration_ms' => $res['duration_ms'] ?? 0,
            'synced_at' => now(),
        ]);

        return response()->json($res, $res['status'] === 'failed' ? 500 : 200);
    }

    public function batch(Request $request)
    {
        if (!$this->isAuthorized($request)) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized sync token.'], 401);
        }

        $validated = $request->validate([
            'events' => 'required|array|max:200',
            'events.*.event_uuid' => 'required|string|max:64',
            'events.*.event_type' => 'required|string|max:40',
        ]);

        $startedAt = microtime(true);
        $processed = 0;
        $rejected = 0;
        $results = [];

        foreach ($validated['events'] as $evt) {
            $res = $this->syncService->ingestEvent($evt);
            $results[] = $res;
            if ($res['status'] === 'processed') {
                $processed++;
            } elseif ($res['status'] === 'rejected') {
                $rejected++;
            }
        }

        EmulatorSyncLog::create([
            'sync_type' => 'event_ingest',
            'status' => 'success',
            'events_processed' => $processed,
            'events_rejected' => $rejected,
            'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
            'synced_at' => now(),
        ]);

        return response()->json([
            'ok' => true,
            'processed' => $processed,
            'rejected' => $rejected,
            'results' => $results,
        ]);
    }

    protected function isAuthorized(Request $request): bool
    {
        $expected = (string) SiteSetting::get('ranking_sync_token', env('AETHER_SYNC_TOKEN', ''));
        if ($expected === '') {
            return false;
        }

        $provided = (string) ($request->header('X-Aether-Sync-Token') ?: $request->input('token', ''));
        return hash_equals($expected, $provided);
    }
}
