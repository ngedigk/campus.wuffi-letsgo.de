<?php

namespace App\Dto;

class CourseNavigation
{
    public function __construct(
        public int $currentIndex,
        public ?string $previousUrl,
        public ?string $nextUrl,
        public bool $isLastSlide,
        public array $allowedSlideIds,
        public array $visitedSlideIds,
        public ?int $redirectModuleIndex,
        public ?int $redirectSlideIndex
    ) {}

    public function shouldRedirect(): bool
    {
        return $this->redirectModuleIndex !== null
            && $this->redirectSlideIndex !== null;
    }
}