<?php

class CourseNavigationService
{

    public function resolve(
        array $modules,
        array $slides,
        array $get
    ): array {

        $slideByModule = [];

        foreach ($slides as $slide) {
            $slideByModule[$slide['module_id']][] = $slide;
        }

        $moduleId = trim((string)($get['module_id'] ?? ''));
        $slideIndex = max(0, (int)($get['slide'] ?? 0));
        $selectedIndex = 0;

        foreach ($modules as $index => $module) {

            if (
                $moduleId !== '' &&
                $module['id'] == $moduleId
            ) {
                $selectedIndex = $index;
                break;
            }
        }

        $currentModule = $modules[$selectedIndex] ?? null;
        $moduleSlides = $currentModule ? ($slideByModule[$currentModule['id']] ?? []) : [];

        if ($slideIndex >= count($moduleSlides)) {
            $slideIndex = max(0, count($moduleSlides)-1);
        }

        $currentSlide = $moduleSlides[$slideIndex] ?? null;

        return [
            'slideByModule' => $slideByModule,
            'currentModule' => $currentModule,
            'slidesForModule' => $moduleSlides,
            'currentSlide' => $currentSlide,
            'currentSlideIndex' => $slideIndex,
            ...$this->calculatePreviousNext(
                $modules,
                $slideByModule,
                $selectedIndex,
                $currentModule,
                $slideIndex
            )
        ];
    }

    private function calculatePreviousNext(
        array $modules,
        array $slides,
        int $moduleIndex,
        ?array $currentModule,
        int $slideIndex
    ): array {

        $previousModule = null;
        $previousSlideIndex = null;

        $nextModule = null;
        $nextSlideIndex = null;

        if ($currentModule) {

            $currentSlides = $slides[$currentModule['id']] ?? [];

            if ($slideIndex > 0) {
                $previousModule = $currentModule;
                $previousSlideIndex = $slideIndex - 1;

            }
            elseif ($moduleIndex > 0) {
                $previousModule = $modules[$moduleIndex - 1];
                $previousSlides = $slides[$previousModule['id']] ?? [];
                $previousSlideIndex = max(0, count($previousSlides) - 1);
            }

            if ($slideIndex+1 < count($currentSlides)) {
                $nextModule = $currentModule;
                $nextSlideIndex = $slideIndex+1;
            }
            elseif ($moduleIndex+1 < count($modules)) {
                $nextModule = $modules[$moduleIndex + 1];
                $nextSlideIndex = 0;
            }
        }

        return [
            'nextModule' => $nextModule,
            'nextSlideIndex' => $nextSlideIndex,
            'prevModule' => $previousModule,
            'prevSlideIndex' => $previousSlideIndex
        ];
    }
}