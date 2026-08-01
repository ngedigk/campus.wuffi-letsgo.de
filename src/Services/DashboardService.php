<?php

namespace App\Services;

use App\Dto\CourseCard;

class DashboardService
{
    public function __construct(
        private CourseService $courseService,
        private ProgressService $progressService
    ) {}

    public function getUserDashboardData(string $userId): array
    {
        $courses = $this->courseService->getAllForUser($userId);
        
        $cards = [];

        foreach ($courses as $course) {
            $isUnlocked = true;
            $isCompleted = $this->progressService->isCourseCompleted($userId, $course->uuid);

            if ($course->prerequisiteCourseId) {
                $isUnlocked = $this->progressService->isCourseCompleted($userId, $course->prerequisiteCourseId) ? 1 : 0;
            }

            $cards[] = new CourseCard(
                uuid: $course->uuid,
                title: $course->title,
                description: $course->description,
                isUnlocked: $isUnlocked,
                isCompleted: $isCompleted,
                prerequisiteCourseId: $course->prerequisiteCourseId
            );
        }

        return $cards;
    }
}