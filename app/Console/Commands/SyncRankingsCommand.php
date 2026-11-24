<?php

namespace App\Console\Commands;

use App\Services\Ranking\RankingSyncService;
use Illuminate\Console\Command;

class SyncRankingsCommand extends Command
{
    protected $signature = 'aether:sync-rankings';
    protected $description = 'Synchronize rAthena ranking events, guild/castle states, and reconcile max-level timestamps';

    public function handle(RankingSyncService $syncService): int
    {
        $this->info('Running rAthena ranking synchronization...');
        $result = $syncService->runFullSync();

        $this->info("Status: {$result['status']} | Processed: {$result['processed']} | Rejected: {$result['rejected']} | Duration: {$result['duration_ms']}ms");

        if (!empty($result['error'])) {
            $this->error("Error: {$result['error']}");
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
