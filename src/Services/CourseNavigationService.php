<?php

namespace App\Services;

use App\Dto\Course;
use App\Dto\CourseNavigation;

use App\Exceptions\CourseSlideNotFoundException;

class CourseNavigationService
{
    public function __construct(
        private CourseService $courseService
    ) {}

    public function getNavigation(
        Course $course,
        int $moduleIndex,
        int $slideIndex,
        array $visitedSlideIds
    ): CourseNavigation
    {
        $allSlides = $this->flattenSlides($course);

        if (empty($allSlides)) {
            throw new CourseSlideNotFoundException(
                'Der Kurs enthält keine Folien.'
            );
        }

        $currentIndex = $this->getCurrentIndex(
            $allSlides,
            $moduleIndex,
            $slideIndex
        );

        if ($currentIndex === -1) {
            throw new CourseSlideNotFoundException(
                'Die angeforderte Folie wurde nicht gefunden.'
            );
        }

        $furthestVisitedIndex = $this->getFurthestVisitedIndex(
            $allSlides,
            $visitedSlideIds
        );

        $nextAllowedIndex = $this->getNextAllowedIndex(
            $allSlides,
            $furthestVisitedIndex
        );

        if ($currentIndex > $nextAllowedIndex) {
            $nextAllowedSlide = $allSlides[$nextAllowedIndex];

            return new CourseNavigation(
                currentIndex: $currentIndex,
                currentModuleIndex: $moduleIndex,
                currentSlideIndex: $slideIndex,
                previousUrl: null,
                nextUrl: null,
                isLastSlide: false,
                allowedSlideIds: [],
                visitedSlideIds: $visitedSlideIds,
                redirectModuleIndex: $nextAllowedSlide['moduleIndex'],
                redirectSlideIndex: $nextAllowedSlide['slideIndex']
            );
        }

        $allowedSlideIds = $this->getAllowedSlideIds(
            $allSlides,
            $nextAllowedIndex,
            $currentIndex
        );

        return new CourseNavigation(
            currentIndex: $currentIndex,
            currentModuleIndex: $moduleIndex,
            currentSlideIndex: $slideIndex,
            previousUrl: $this->getPreviousUrl(
                $allSlides,
                $currentIndex,
                $course->uuid
            ),
            nextUrl: $this->getNextUrl(
                $allSlides,
                $currentIndex,
                $course->uuid
            ),
            isLastSlide: $currentIndex === count($allSlides) - 1,
            allowedSlideIds: $allowedSlideIds,
            visitedSlideIds: $visitedSlideIds,
            redirectModuleIndex: null,
            redirectSlideIndex: null
        );
    }

    private function flattenSlides(Course $course): array
    {
        $slides = [];

        foreach ($course->modules as $moduleIndex => $module) {
            foreach ($module->slides as $slideIndex => $slide) {
                $slides[] = [
                    'module' => $module,
                    'moduleIndex' => $moduleIndex,
                    'slide' => $slide,
                    'slideIndex' => $slideIndex,
                ];
            }
        }

        return $slides;
    }

    private function getFurthestVisitedIndex(
        array $allSlides,
        array $visitedSlideIds
    ): int {
        $visited = array_fill_keys($visitedSlideIds, true);

        $maxIndex = -1;

        foreach ($allSlides as $index => $item) {
            if (isset($visited[$item['slide']->id])) {
                $maxIndex = $index;
            }
        }

        return $maxIndex;
    }

    private function getNextAllowedIndex(
        array $allSlides,
        int $furthestVisitedIndex
    ): int {
        if (empty($allSlides)) {
            return -1;
        }

        return min(
            $furthestVisitedIndex + 1,
            count($allSlides) - 1
        );
    }

    private function getCurrentIndex(
        array $allSlides,
        int $moduleIndex,
        int $slideIndex
    ): int {
        foreach ($allSlides as $index => $item) {
            if (
                $item['moduleIndex'] === $moduleIndex &&
                $item['slideIndex'] === $slideIndex
            ) {
                return $index;
            }
        }

        return -1;
    }

    private function getAllowedSlideIds(
        array $allSlides,
        int $nextAllowedIndex,
        int $currentIndex
    ): array {
        if (empty($allSlides)) {
            return [];
        }

        $maxIndex = max(
            $nextAllowedIndex,
            $currentIndex + 1
        );

        $maxIndex = min(
            $maxIndex,
            count($allSlides) - 1
        );

        $allowedSlideIds = [];

        for ($i = 0; $i <= $maxIndex; $i++) {
            $allowedSlideIds[] = $allSlides[$i]['slide']->id;
        }

        return $allowedSlideIds;
    }

    private function getPreviousUrl(
        array $allSlides,
        int $currentIndex,
        string $courseUuid
    ): ?string {
        if ($currentIndex <= 0) {
            return null;
        }

        $slide = $allSlides[$currentIndex - 1];

        return $this->courseService->buildCourseUrl(
            $courseUuid,
            $slide['moduleIndex'],
            $slide['slideIndex']
        );
    }

    private function getNextUrl(
        array $allSlides,
        int $currentIndex,
        string $courseUuid
    ): ?string {
        if (
            $currentIndex < 0 ||
            $currentIndex >= count($allSlides) - 1
        ) {
            return null;
        }

        $slide = $allSlides[$currentIndex + 1];

        return $this->courseService->buildCourseUrl(
            $courseUuid,
            $slide['moduleIndex'],
            $slide['slideIndex']
        );
    }
}