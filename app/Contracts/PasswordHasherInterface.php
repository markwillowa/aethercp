<?php

namespace App\Contracts;

interface PasswordHasherInterface
{
    /**
     * Hash a raw password using the emulator's configured algorithm.
     */
    public function hash(string $plainPassword): string;

    /**
     * Verify a raw password against an existing stored hash with auto-detection.
     */
    public function verify(string $plainPassword, string $hashedPassword): bool;

    /**
     * Check if a stored hash needs to be rehashed to a newer algorithm.
     */
    public function needsRehash(string $hashedPassword): bool;
}

