<?php

class DashboardService
{
    public function __construct(
        private CourseService $courseService,
        private ProgressService $progressService
    ) {}

    public function getUserDashboardData(string $userId): array
    {
        $courses = $this->courseService->getAllForUser($userId);

        foreach ($courses as $course) {
            $course->isUnlocked = true;
            if ($course->prerequisiteCourseId) {
                $course->isUnlocked = $this->progressService->isCourseCompleted($userId, $course->prerequisiteCourseId) ? 1 : 0;
            }
            $course->isCompleted = $this->progressService->isCourseCompleted($userId, $course->uuid) ? 1 : 0;
        }

        return $courses;
    }
}