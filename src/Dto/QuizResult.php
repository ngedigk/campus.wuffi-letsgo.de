<?php

namespace App\Dto;

class QuizResult
{
    public function __construct(
        public bool $isSubmitted = false,
        public bool $passed = false,
        public ?string $feedbackMessage = null,
        public string $feedbackType = 'info',
        public array $questions = [],
        public array $choicesByQuestion = [],
        public array $results = [],
        public ?int $slideId = null
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