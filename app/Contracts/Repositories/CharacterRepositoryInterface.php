<?php

namespace App\Contracts\Repositories;

interface CharacterRepositoryInterface
{
    public function getCharactersByAccount(int $accountId): array;

    public function findById(int $charId): ?object;

    public function findByName(string $name): ?object;

    public function getOnlineCharacters(int $limit = 50): array;

    public function resetPosition(int $charId, string $map = 'prontera', int $x = 156, int $y = 191): bool;

    public function deleteCharacter(int $charId, int $accountId): bool;

    public function getInventory(int $charId): array;

    public function getCartInventory(int $charId): array;

    public function getStorage(int $accountId): array;
}

