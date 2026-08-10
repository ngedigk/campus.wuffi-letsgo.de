<?php

namespace App\Contracts\Repositories;

interface ProgressRepositoryInterface
{
    public function getVisitedSlideIds(string $userId, string $courseUuid): array;

    public function recordSlideView(string $userId, int $slideId): void;

    public function isCourseCompleted(string $userId, string $courseUuid): bool;
}