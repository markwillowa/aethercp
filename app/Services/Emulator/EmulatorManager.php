<?php

namespace App\Services\Emulator;

use App\Contracts\EmulatorInterface;
use App\Services\Emulator\Adapters\RAthenaAdapter;
use App\Services\Emulator\Adapters\HerculesAdapter;
use InvalidArgumentException;

class EmulatorManager
{
    protected array $adapters = [];
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config ?: config('emulator', []);
    }

    public function driver(?string $name = null): EmulatorInterface
    {
        $driverName = strtolower($name ?: ($this->config['driver'] ?? 'rathena'));

        if (!isset($this->adapters[$driverName])) {
            $this->adapters[$driverName] = $this->createDriver($driverName);
        }

        return $this->adapters[$driverName];
    }

    protected function createDriver(string $name): EmulatorInterface
    {
        return match ($name) {
            'rathena' => new RAthenaAdapter($this->config),
            'hercules' => new HerculesAdapter($this->config),
            default => throw new InvalidArgumentException("Unsupported emulator driver [{$name}]."),
        };
    }

    public function __call($method, $parameters)
    {
        return $this->driver()->$method(...$parameters);
    }
}

