<?php

namespace App\Dto;

final class QuizQuestionInput
{
    public function __construct(
        public int $slideId,
        public string $questionText,
        public ?array $choices = null
    ) {}

    public function toArray(): array
    {
        return [
            'slideId' => $this->slideId,
            'questionText' => $this->questionText
        ];
    }
}