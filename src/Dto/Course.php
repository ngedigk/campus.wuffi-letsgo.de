<?php

namespace App\Dto;

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
        public ?array $modules = null,
    ) {}

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'description' => $this->description,
            'prerequisiteCourseId' => $this->prerequisiteCourseId,
            'sortOrder' => $this->sortOrder
        ];
    }
}