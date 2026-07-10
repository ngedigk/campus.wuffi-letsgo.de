<?php

class CourseProgressService
{

    public function __construct(
        private CourseRepository $repository
    ){}

    public function recordSlideView(string $userUuid, ?array $slide): void
    {
        if (!$slide) {
            return;
        }

        $slideId = (string)($slide['id'] ?? '');

        if ($slideId === '') {
            return;
        }

        $this->repository->recordSlideView($userUuid, $slideId);
    }

    public function completeModuleIfNeededBySlideViews(
        string $userUuid,
        ?array $module,
        array $slidesForModule,
        array &$completed
    ): void {
        if (!$module) {
            return;
        }

        $id = (string)$module['id'];

        if (isset($completed[$id])) {
            return;
        }

        $slideIds = array_values(array_unique(array_map(
            static fn(array $slide): string => (string)($slide['id'] ?? ''),
            $slidesForModule
        )));

        $slideIds = array_values(array_filter($slideIds, static fn(string $slideId): bool => $slideId !== ''));

        if ($slideIds === []) {
            return;
        }

        $viewedSlideIds = $this->repository->getViewedSlideIds($userUuid, $slideIds);

        if (count($viewedSlideIds) < count($slideIds)) {
            return;
        }

        $this->repository->completeModule($userUuid, $module['id']);
        $completed[$id] = true;
    }

    public function completeCourseIfNeeded(
        string $userUuid,
        string $courseUuid,
        array &$course,
        array $modules,
        array $completed
    ): void {

        if (
            count($modules) !== count($completed) ||
            !empty($course['is_completed'])
        ) {
            return;
        }

        $this->repository->completeCourse($userUuid, $courseUuid);

        $course['is_completed'] = 1;
        $course['completed_at'] = date('Y-m-d H:i:s');
    }
}