<?php

namespace App\Dto;

final class Module
{
    /**
     * @param Slide[] $slides
     */
    public function __construct(
        public int $id,
        public string $title,
        public int $sortOrder,
        public ?array $slides = null,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'sortOrder' => $this->sortOrder
        ];
    }
}