<?php

namespace App\Dto;

final class QuizQuestion
{
    public function __construct(
        public int $id,
        public string $questionText,
        public ?array $choices = null
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'questionText' => $this->questionText
        ];
    }
}