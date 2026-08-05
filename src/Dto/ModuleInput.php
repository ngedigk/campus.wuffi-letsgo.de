<?php

namespace App\Dto;

final class ModuleInput
{
    public function __construct(
        public string $courseId,
        public string $title,
        public int $sortOrder,
    ) {}

    public function toArray(): array
    {
        return [
            'courseId' => $this->courseId,
            'title' => $this->title,
            'sortOrder' => $this->sortOrder,
        ];
    }
}