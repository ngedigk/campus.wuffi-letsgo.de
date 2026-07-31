<?php

class CourseSidebarSlideItem
{
    public function __construct(
        public string $title,
        public bool $isLocked,
        public bool $isActive,
        public bool $isVisited,
        public ?string $url
    ) {}
}