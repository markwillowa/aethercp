<?php

namespace App\Contracts\Repositories;

interface RankingRepositoryInterface
{
    public function getBaseLevelRanking(int $limit = 25): array;

    public function getJobLevelRanking(int $limit = 25): array;

    public function getZenyRanking(int $limit = 25): array;

    public function getMvpRanking(int $limit = 25): array;

    public function getGuildRanking(int $limit = 25): array;
}

