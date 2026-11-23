<?php

namespace App\Contracts\Repositories;

interface ItemRepositoryInterface
{
    public function findById(int $itemId): ?object;

    public function search(string $keyword, ?string $type = null, int $limit = 25, int $page = 1): array;

    public function getItemTypes(): array;
}

