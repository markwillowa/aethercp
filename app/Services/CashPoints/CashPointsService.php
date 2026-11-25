<?php

namespace App\Services\CashPoints;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CashPointsService
{
    /**
     * Get account Cash Points balance from rAthena/Hercules acc_reg_num.
     */
    public function getBalance(int $accountId): int
    {
        try {
            $points = DB::connection('ragnarok')->table('acc_reg_num')
                ->where('account_id', $accountId)
                ->where('key', '#CASHPOINTS')
                ->where('index', 0)
                ->value('value');

            return (int) ($points ?? 0);
        } catch (\Throwable $e) {
            Log::error("Failed fetching Cash Points for account {$accountId}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Atomically deduct Cash Points with balance validation and logging.
     * Throws \RuntimeException on insufficient balance.
     */
    public function deduct(int $accountId, int $amount, string $reason, ?string $reference = null): int
    {
        if ($amount <= 0) {
            return $this->getBalance($accountId);
        }

        $conn = DB::connection('ragnarok');

        return $conn->transaction(function () use ($conn, $accountId, $amount, $reason, $reference) {
            $row = $conn->table('acc_reg_num')
                ->where('account_id', $accountId)
                ->where('key', '#CASHPOINTS')
                ->where('index', 0)
                ->lockForUpdate()
                ->first();

            $currentBalance = $row ? (int) $row->value : 0;

            if ($currentBalance < $amount) {
                throw new \RuntimeException("Insufficient Cash Points. Available: " . number_format($currentBalance) . " CP, Required: " . number_format($amount) . " CP.");
            }

            $newBalance = $currentBalance - $amount;

            $conn->table('acc_reg_num')
                ->where('account_id', $accountId)
                ->where('key', '#CASHPOINTS')
                ->where('index', 0)
                ->update(['value' => $newBalance]);

            // Sync global_acc_reg_num if table exists
            try {
                if ($conn->getSchemaBuilder()->hasTable('global_acc_reg_num')) {
                    $conn->table('global_acc_reg_num')
                        ->where('account_id', $accountId)
                        ->where('key', '#CASHPOINTS')
                        ->where('index', 0)
                        ->update(['value' => $newBalance]);
                }
            } catch (\Throwable $e) {}

            ActivityLog::log(
                'cashpoints.deducted',
                "Deducted " . number_format($amount) . " CP ({$reason}). New Balance: " . number_format($newBalance) . " CP. Ref: {$reference}",
                $accountId
            );

            return $newBalance;
        });
    }

    /**
     * Atomically credit Cash Points to account.
     */
    public function credit(int $accountId, int $amount, string $reason, ?string $reference = null): int
    {
        if ($amount <= 0) {
            return $this->getBalance($accountId);
        }

        $conn = DB::connection('ragnarok');

        return $conn->transaction(function () use ($conn, $accountId, $amount, $reason, $reference) {
            $row = $conn->table('acc_reg_num')
                ->where('account_id', $accountId)
                ->where('key', '#CASHPOINTS')
                ->where('index', 0)
                ->lockForUpdate()
                ->first();

            if ($row) {
                $newBalance = ((int) $row->value) + $amount;
                $conn->table('acc_reg_num')
                    ->where('account_id', $accountId)
                    ->where('key', '#CASHPOINTS')
                    ->where('index', 0)
                    ->update(['value' => $newBalance]);
            } else {
                $newBalance = $amount;
                $conn->table('acc_reg_num')->insert([
                    'account_id' => $accountId,
                    'key' => '#CASHPOINTS',
                    'index' => 0,
                    'value' => $newBalance,
                ]);
            }

            // Sync global_acc_reg_num if table exists
            try {
                if ($conn->getSchemaBuilder()->hasTable('global_acc_reg_num')) {
                    $conn->table('global_acc_reg_num')->updateOrInsert(
                        ['account_id' => $accountId, 'key' => '#CASHPOINTS', 'index' => 0],
                        ['value' => $newBalance]
                    );
                }
            } catch (\Throwable $e) {}

            ActivityLog::log(
                'cashpoints.credited',
                "Credited " . number_format($amount) . " CP ({$reason}). New Balance: " . number_format($newBalance) . " CP. Ref: {$reference}",
                $accountId
            );

            return $newBalance;
        });
    }
}
