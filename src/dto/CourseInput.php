<?php

final class CourseInput
{
    public function __construct(
        public string $uuid,
        public string $title,
        public string $description,
        public ?string $prerequisiteCourseId,
        public int $sortOrder
    ) {}
}