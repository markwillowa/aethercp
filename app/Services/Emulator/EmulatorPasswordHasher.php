<?php

namespace App\Services\Emulator;

use App\Contracts\PasswordHasherInterface;

class EmulatorPasswordHasher implements PasswordHasherInterface
{
    protected string $defaultAlgo;

    public function __construct(?string $defaultAlgo = null)
    {
        $this->defaultAlgo = strtolower($defaultAlgo ?: config('emulator.password_hash', 'md5'));
    }

    /**
     * Hash plain password based on configured algorithm.
     */
    public function hash(string $plainPassword): string
    {
        return match ($this->defaultAlgo) {
            'bcrypt' => password_hash($plainPassword, PASSWORD_BCRYPT),
            'sha256' => hash('sha256', $plainPassword),
            'plain' => $plainPassword,
            default => md5($plainPassword),
        };
    }

    /**
     * Verify plain password against stored hash with auto-detection.
     * Prevents lockouts even when hashes transition from MD5 to Bcrypt/SHA256.
     */
    public function verify(string $plainPassword, string $hashedPassword): bool
    {
        // 1. Bcrypt hash check (starts with $2y$ or $2a$ or $2b$)
        if (str_starts_with($hashedPassword, '$2y$') ||
            str_starts_with($hashedPassword, '$2a$') ||
            str_starts_with($hashedPassword, '$2b$')) {
            return password_verify($plainPassword, $hashedPassword);
        }

        // 2. SHA256 hex hash check (length 64)
        if (strlen($hashedPassword) === 64 && ctype_xdigit($hashedPassword)) {
            return hash_equals(strtolower($hashedPassword), hash('sha256', $plainPassword));
        }

        // 3. MD5 hex hash check (length 32)
        if (strlen($hashedPassword) === 32 && ctype_xdigit($hashedPassword)) {
            return hash_equals(strtolower($hashedPassword), md5($plainPassword));
        }

        // 4. Plain text fallback comparison
        return hash_equals($hashedPassword, $plainPassword);
    }

    /**
     * Check if stored hash does not match current configured target algorithm.
     */
    public function needsRehash(string $hashedPassword): bool
    {
        return match ($this->defaultAlgo) {
            'bcrypt' => !password_get_info($hashedPassword)['algo'],
            'sha256' => strlen($hashedPassword) !== 64,
            'md5' => strlen($hashedPassword) !== 32,
            default => false,
        };
    }
}

