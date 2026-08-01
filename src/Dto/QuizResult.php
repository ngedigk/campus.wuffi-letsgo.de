<?php

namespace App\Dto;

class QuizResult
{
    public function __construct(
        public readonly bool $isSubmitted = false,
        public readonly bool $passed = false,
        public readonly ?string $feedbackMessage = null,
        public readonly string $feedbackType = 'info',
        public readonly array $questions = [],
        public readonly array $choicesByQuestion = [],
        public readonly array $results = []
    ) {}

    public function getChoiceLabel(array $choice): string
    {
        if ($choice['is_correct'] && $choice['was_chosen']) {
            return ' <strong>(Richtig, Ihre Antwort)</strong>';
        }
        if ($choice['is_correct']) {
            return ' <strong>(Richtig)</strong>';
        }
        if ($choice['was_chosen']) {
            return ' <strong>(Ihre Antwort)</strong>';
        }
        return '';
    }
}