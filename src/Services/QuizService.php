<?php

namespace App\Services;

use App\Repositories\QuizQuestionRepository;
use App\Dto\QuizResult;

class QuizService
{
    public function __construct(private readonly QuizQuestionRepository $quizQuestionRepository) {}

    public function getQuizData(int $slideId): QuizResult
    {
        $data = $this->quizQuestionRepository->getQuizDataForSlide($slideId);

        if (empty($data['questions'])) {
            return new QuizResult();
        }

        $questions = $data['questions'];
        $choices = $data['choices'];

        foreach ($choices as $qId => $choiceList) {
            shuffle($choiceList);
            $choices[$qId] = $choiceList;
        }

        return new QuizResult(
            questions: $questions,
            choicesByQuestion: $choices
        );
    }

    public function submitQuiz(QuizResult $baseQuiz, array $userAnswers): QuizResult
    {
        $results = [];
        $correctQuestions = 0;
        $totalQuestions = count($baseQuiz->questions);

        foreach ($baseQuiz->questions as $question) {
            $qId = $question['id'];
            $submitted = array_map('strval', $userAnswers[$qId] ?? []);

            $correctChoices = array_filter(
                $baseQuiz->choicesByQuestion[$qId],
                fn($choice) => $choice['is_correct']
            );

            $correctAnswers = array_map(
                fn($choice) => (string)$choice['id'],
                $correctChoices
            );

            sort($submitted);
            sort($correctAnswers);

            $isCorrect = $submitted === $correctAnswers;
            if ($isCorrect) {
                $correctQuestions++;
            }

            $choicesWithFlags = array_map(function($choice) use ($submitted) {
                $choice['was_chosen'] = in_array((string)$choice['id'], $submitted, true);
                return $choice;
            }, $baseQuiz->choicesByQuestion[$qId]);

            $results[$qId] = [
                'question_id' => $qId,
                'question_text' => $question['question_text'],
                'is_correct' => $isCorrect,
                'submitted' => $submitted,
                'correct' => $correctAnswers,
                'choices' => $choicesWithFlags,
            ];
        }

        $allPassed = $correctQuestions === $totalQuestions && $totalQuestions > 0;
        $feedbackMessage = $allPassed
            ? 'Sie haben dieses Quiz korrekt beantwortet!'
            : 'Einige Antworten waren falsch oder unvollständig. Überprüfen Sie das Feedback unten.';
        $feedbackType = $allPassed ? 'success' : 'error';

        return new QuizResult(
            isSubmitted: true,
            passed: $allPassed,
            feedbackMessage: $feedbackMessage,
            feedbackType: $feedbackType,
            questions: $baseQuiz->questions,
            choicesByQuestion: $baseQuiz->choicesByQuestion,
            results: $results
        );
    }
}