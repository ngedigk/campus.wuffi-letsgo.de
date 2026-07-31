<?php

class ProgressService
{
    public function __construct(
        private ProgressRepository $progressRepo
    ) { }

    public function recordSlideView(string $userId, int $slideId): void
    {
        $this->progressRepo->recordSlideView($userId, $slideId);
    }

    public function getVisitedSlideIds(string $userId, string $courseUuid): array
    {
        return $this->progressRepo->getVisitedSlideIds($userId, $courseUuid);
    }

    public function isCourseCompleted(string $userId, string $courseUuid): bool
    {
        return $this->progressRepo->isCourseCompleted($userId, $courseUuid);
    }
}
