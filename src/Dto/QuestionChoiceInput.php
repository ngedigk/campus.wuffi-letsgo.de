<?php

namespace App\Dto;

final class QuestionChoiceInput
{
    public function __construct(
        public int $questionId,
        public string $choiceText,
        public bool $isCorrect
    ) {}
}