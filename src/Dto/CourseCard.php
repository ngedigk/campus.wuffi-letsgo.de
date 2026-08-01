<?php

namespace App\Dto;

final class CourseCard
{
    public function __construct(
        public string $uuid,
        public string $title,
        public string $description,
        public bool $isUnlocked,
        public bool $isCompleted,
        public ?string $prerequisiteCourseId
    ) {}
}