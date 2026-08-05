<?php

namespace App\Dto;

final class Slide
{
    public function __construct(
        public int $id,
        public string $title,
        public string $htmlContent,
        public ?string $audioUrl,
        public int $sortOrder
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'htmlContent' => $this->htmlContent,
            'audioUrl' => $this->audioUrl,
            'sortOrder' => $this->sortOrder
        ];
    }
}