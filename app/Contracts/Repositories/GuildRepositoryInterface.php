<?php

namespace App\Contracts\Repositories;

interface GuildRepositoryInterface
{
    public function findById(int $guildId): ?object;

    public function findByName(string $name): ?object;

    public function getMembers(int $guildId): array;

    public function getGuilds(int $limit = 20, int $page = 1): array;

    public function getCastles(): array;

    public function getEmblemRaw(int $guildId): ?string;
}

