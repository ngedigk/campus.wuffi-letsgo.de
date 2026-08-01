<?php

namespace App\Dto;

final class ModuleInput
{
    public function __construct(
        public string $courseId,
        public string $title,
        public int $sortOrder,
    ) {}
}