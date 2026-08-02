<?php

namespace App\Services;

use App\Dto\Course;
use App\Dto\Module;

use App\ViewModels\CourseSidebarItem;
use App\ViewModels\CourseSidebarSlideItem;

class CourseSidebarBuilderService
{
    public function __construct(
        private CourseService $courseService
    ) {}

    public function build(
        Course $course,
        ?Module $currentModule,
        array $allowedSlideIds,
        array $visitedSlideIds
    ): array {
        $items = [];

        foreach ($course->modules as $moduleIndex => $module) {

            $firstSlide = $module->slides[0] ?? null;

            $moduleLocked = $firstSlide
                ? !in_array($firstSlide->id, $allowedSlideIds)
                : true;

            $slides = [];

            if ($currentModule !== null && $module->id === $currentModule->id) {

                foreach ($module->slides as $slideIndex => $slide) {

                    $allowed = in_array(
                        $slide->id,
                        $allowedSlideIds
                    );

                    $slides[] = new CourseSidebarSlideItem(
                        title: $slide->title,
                        isLocked: !$allowed,
                        isActive: false,
                        isVisited: in_array(
                            $slide->id,
                            $visitedSlideIds
                        ),
                        url: $allowed
                            ? $this->courseService->buildCourseUrl(
                                $course->uuid,
                                $moduleIndex,
                                $slideIndex
                            )
                            : null
                    );
                }
            }

            $items[] = new CourseSidebarItem(
                title: $module->title,
                isLocked: $moduleLocked,
                url: !$moduleLocked
                    ? $this->courseService->buildCourseUrl(
                        $course->uuid,
                        $moduleIndex,
                        0
                    )
                    : null,
                isActive: $currentModule !== null && $module->id === $currentModule->id,
                slides: $slides
            );
        }

        return $items;
    }
}