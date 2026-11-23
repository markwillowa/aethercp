<?php

namespace App\Services\Emulator\Adapters;

use App\Contracts\EmulatorInterface;
use Illuminate\Support\Facades\DB;

abstract class AbstractEmulatorAdapter implements EmulatorInterface
{
    protected array $config;

    public function __construct(array $config = [])
    {
        if (!empty($config)) {
            $this->config = $config;
        } elseif (function_exists('config')) {
            try {
                $this->config = config('emulator') ?: [];
            } catch (\Throwable $e) {
                $this->config = [];
            }
        } else {
            $this->config = [];
        }
    }

    public function isRenewal(): bool
    {
        return (bool) ($this->config['renewal'] ?? true);
    }

    public function getTableName(string $key): string
    {
        $tables = $this->config['tables'] ?? [];
        return $tables[$key] ?? $key;
    }

    public function quoteIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }

    public function getOnlinePlayerCount(): int
    {
        $charTable = $this->quoteIdentifier($this->getTableName('char'));
        $gmThreshold = (int) ($this->config['gm_level_threshold'] ?? 1);
        $hideGm = (bool) ($this->config['hide_gm_from_online'] ?? true);

        try {
            $query = DB::connection('ragnarok')
                ->table(DB::raw($charTable))
                ->where('online', '>', 0);

            if ($hideGm) {
                $loginTable = $this->quoteIdentifier($this->getTableName('login'));
                $query->whereNotIn('account_id', function ($sub) use ($loginTable, $gmThreshold) {
                    $sub->select('account_id')
                        ->from(DB::raw($loginTable))
                        ->where('group_id', '>=', $gmThreshold);
                });
            }

            return (int) $query->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    public function isCharNameAvailable(string $name): bool
    {
        $charTable = $this->quoteIdentifier($this->getTableName('char'));
        return !DB::connection('ragnarok')
            ->table(DB::raw($charTable))
            ->where('name', $name)
            ->exists();
    }

    public function checkServerStatus(string $type): array
    {
        $server = $this->config['servers'][$type] ?? null;
        if (!$server) {
            return [
                'online' => false,
                'latency_ms' => null,
                'name' => ucfirst($type) . ' Server',
            ];
        }

        $host = $server['host'] ?? '127.0.0.1';
        $port = (int) ($server['port'] ?? 6900);
        $timeout = (float) ($server['timeout'] ?? 1.5);

        $start = microtime(true);
        $fp = @fsockopen($host, $port, $errno, $errstr, $timeout);

        if ($fp) {
            $latency = round((microtime(true) - $start) * 1000, 2);
            fclose($fp);
            return [
                'online' => true,
                'latency_ms' => $latency,
                'name' => $server['name'] ?? ucfirst($type),
                'host' => $host,
                'port' => $port,
            ];
        }

        return [
            'online' => false,
            'latency_ms' => null,
            'name' => $server['name'] ?? ucfirst($type),
            'host' => $host,
            'port' => $port,
        ];
    }
}
