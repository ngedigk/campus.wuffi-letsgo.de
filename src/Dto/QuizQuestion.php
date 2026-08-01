<?php

namespace App\Dto;

final class QuizQuestion
{
    public function __construct(
        public int $id,
        public int $slideId,
        public string $questionText,
        public ?array $choices = []
    ) {}
}