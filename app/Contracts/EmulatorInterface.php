<?php

namespace App\Contracts;

interface EmulatorInterface
{
    /**
     * Get the emulator name identifier (e.g., 'rathena', 'hercules').
     */
    public function getName(): string;

    /**
     * Check if the current emulator is running in Renewal mode.
     */
    public function isRenewal(): bool;

    /**
     * Get the actual table name (handling config overrides and prefixes).
     */
    public function getTableName(string $key): string;

    /**
     * Check server port status.
     */
    public function checkServerStatus(string $type): array;

    /**
     * Get the count of online players across maps.
     */
    public function getOnlinePlayerCount(): int;

    /**
     * Get the character stat column keys according to renewal setting.
     */
    public function getStatColumns(): array;

    /**
     * Check if character name is available.
     */
    public function isCharNameAvailable(string $name): bool;

    /**
     * Escape an SQL identifier (such as `char`).
     */
    public function quoteIdentifier(string $identifier): string;
}

