<?php

final class Course
{
    /**
     * @param Module[] $modules
     */
    public function __construct(
        public string $uuid,
        public string $title,
        public string $description,
        public ?string $prerequisiteCourseId,
        public int $sortOrder,
        public ?array $modules,
    ) {}
}