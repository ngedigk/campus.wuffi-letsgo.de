<?php

namespace App\Dto;

final class SlideInput
{
    public function __construct(
        public int $moduleId,
        public string $title,
        public ?string $htmlContent,
        public ?string $audioUrl,
        public int $sortOrder
    ) {}

    public function toArray(): array
    {
        return [
            'moduleId' => $this->moduleId,
            'title' => $this->title,
            'htmlContent' => $this->htmlContent,
            'audioUrl' => $this->audioUrl,
            'sortOrder' => $this->sortOrder
        ];
    }
}