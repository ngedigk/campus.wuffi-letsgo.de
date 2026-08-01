<?php

namespace App\Dto;

final class QuestionChoice
{
    public function __construct(
        public int $id,
        public int $questionId,
        public string $choiceText,
        public bool $isCorrect
    ) {}
}