<?php

final class QuizResult
{
    public function __construct(
        public array $questions = [],
        public array $currentSlideQuestions = [],
        public array $choicesByQuestion = [],
        public array $answers = [],
        public array $submittedAnswers = [],
        public array $feedback = [],
        public array $errors = [],
        public bool $passed = false,
        public bool $attempted = false
    ) {}


    public function toArray(): array
    {
        return [
            'questions' => $this->questions,
            'currentSlideQuestions' => $this->currentSlideQuestions,
            'choicesByQuestion' => $this->choicesByQuestion,
            'userAnswersByQuestion' => $this->answers,
            'submittedAnswers' => $this->submittedAnswers,
            'quizFeedback' => $this->feedback,
            'errors' => $this->errors,
            'quizPassed' => $this->passed,
            'quizAttempted' => $this->attempted
        ];
    }
}