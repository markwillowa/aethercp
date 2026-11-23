<?php

namespace App\Contracts\Repositories;

interface AccountRepositoryInterface
{
    public function findById(int $accountId): ?object;

    public function findByUsername(string $username): ?object;

    public function findByEmail(string $email): ?object;

    public function usernameExists(string $username): bool;

    public function emailExists(string $email): bool;

    public function create(array $data): int;

    public function updatePassword(int $accountId, string $hashedPassword): bool;

    public function updateEmail(int $accountId, string $email): bool;

    public function updatePincode(int $accountId, string $pincode): bool;

    public function updateLastLogin(int $accountId, string $ip): bool;

    public function getCharacterCount(int $accountId): int;

    public function setAccountState(int $accountId, int $state, int $unbanTime = 0): bool;
}

