<?php

namespace App\ViewModels;

class CourseSidebarItem
{
    public function __construct(
        public string $title,
        public bool $isLocked,
        public ?string $url,
        public bool $isActive,
        public array $slides
    ) {}
}