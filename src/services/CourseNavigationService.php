<?php

class CourseNavigationService
{

    public function resolve(
        array $modules,
        array $slides,
        array $get,
        array $viewedSlideIds = []
    ): array {

        $slideByModule = [];

        foreach ($slides as $slide) {
            $slideByModule[(string)($slide['module_id'] ?? '')][] = $slide;
        }

        $moduleId = trim((string)($get['module_id'] ?? ''));
        $slideIndex = max(0, (int)($get['slide'] ?? 0));
        $selectedIndex = 0;

        foreach ($modules as $index => $module) {

            if (
                $moduleId !== '' &&
                (string)$module['id'] == $moduleId
            ) {
                $selectedIndex = $index;
                break;
            }
        }

        $currentModule = $modules[$selectedIndex] ?? null;
        $moduleSlides = $currentModule ? ($slideByModule[(string)$currentModule['id']] ?? []) : [];

        if ($slideIndex >= count($moduleSlides)) {
            $slideIndex = max(0, count($moduleSlides)-1);
        }

        $currentSlide = $moduleSlides[$slideIndex] ?? null;
        $slideUnlockState = $this->buildSlideUnlockState($slides, $viewedSlideIds);
        $moduleUnlockState = $this->buildModuleUnlockState($modules, $slideByModule, $slideUnlockState);

        return [
            'slideByModule' => $slideByModule,
            'currentModule' => $currentModule,
            'slidesForModule' => $moduleSlides,
            'currentSlide' => $currentSlide,
            'currentSlideIndex' => $slideIndex,
            'slideUnlockState' => $slideUnlockState,
            'moduleUnlockState' => $moduleUnlockState,
            ...$this->calculatePreviousNext(
                $modules,
                $slideByModule,
                $selectedIndex,
                $currentModule,
                $slideIndex,
                $currentSlide,
                $viewedSlideIds
            )
        ];
    }

    private function buildSlideUnlockState(array $slides, array $viewedSlideIds): array
    {
        $unlockState = [];
        $previousSlideId = null;

        foreach ($slides as $slide) {
            $slideId = (string)($slide['id'] ?? '');

            if ($slideId === '') {
                continue;
            }

            $unlockState[$slideId] = $previousSlideId === null || !empty($viewedSlideIds[$previousSlideId]);
            $previousSlideId = $slideId;
        }

        return $unlockState;
    }

    private function buildModuleUnlockState(array $modules, array $slideByModule, array $slideUnlockState): array
    {
        $moduleUnlockState = [];

        foreach ($modules as $module) {
            $moduleId = (string)($module['id'] ?? '');

            if ($moduleId === '') {
                continue;
            }

            $moduleSlides = $slideByModule[$moduleId] ?? [];
            $firstSlide = $moduleSlides[0] ?? null;
            $firstSlideId = (string)($firstSlide['id'] ?? '');

            $moduleUnlockState[$moduleId] = $firstSlideId === '' || !empty($slideUnlockState[$firstSlideId]);
        }

        return $moduleUnlockState;
    }

    private function calculatePreviousNext(
        array $modules,
        array $slides,
        int $moduleIndex,
        ?array $currentModule,
        int $slideIndex,
        ?array $currentSlide,
        array $viewedSlideIds
    ): array {

        $previousModule = null;
        $previousSlideIndex = null;

        $nextModule = null;
        $nextSlideIndex = null;
        $nextSlideUnlocked = false;

        if ($currentModule) {

            $currentSlides = $slides[(string)$currentModule['id']] ?? [];
            $currentSlideId = (string)($currentSlide['id'] ?? '');
            $nextSlideUnlocked = $currentSlideId !== '' && !empty($viewedSlideIds[$currentSlideId]);

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
            'prevSlideIndex' => $previousSlideIndex,
            'nextSlideUnlocked' => $nextSlideUnlocked
        ];
    }
}