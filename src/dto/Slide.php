<?php

final class Slide
{
    public function __construct(
        public int $id,
        public string $title,
        public string $htmlContent,
        public ?string $audioUrl,
        public int $sortOrder,
        public bool $isQuiz,
    ) {}
}