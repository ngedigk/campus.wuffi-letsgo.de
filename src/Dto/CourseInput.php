<?php

namespace App\Dto;

final class CourseInput
{
    public function __construct(
        public string $title,
        public string $description,
        public ?string $prerequisiteCourseId,
        public int $sortOrder
    ) {}

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'prerequisiteCourseId' => $this->prerequisiteCourseId,
            'sortOrder' => $this->sortOrder
        ];
    }
}