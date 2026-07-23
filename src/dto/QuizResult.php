<?php

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

    /**
     * Helper to generate the feedback label for a choice in the view.
     */
    public function getChoiceLabel(array $choice): string
    {
        if ($choice['is_correct'] && $choice['was_chosen']) {
            return ' <strong>(Correct, Your answer)</strong>';
        }
        if ($choice['is_correct']) {
            return ' <strong>(Correct)</strong>';
        }
        if ($choice['was_chosen']) {
            return ' <strong>(Your answer)</strong>';
        }
        return '';
    }
}