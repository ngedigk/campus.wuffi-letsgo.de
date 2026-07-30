<?php

final class Module
{
    /**
     * @param Slide[] $slides
     */
    public function __construct(
        public int $id,
        public string $title,
        public int $sortOrder,
        public ?array $slides,
    ) {}
}