<?php

namespace App\Dto;

final class QuestionChoiceInput
{
    public function __construct(
        public string $choiceText,
        public bool $isCorrect
    ) {}

    public function toArray(): array
    {
        return [
            'choiceText' => $this->choiceText,
            'isCorrect' => (int)$this->isCorrect
        ];
    }


}