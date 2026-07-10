<?php

require_once __DIR__ . '/../dto/QuizResult.php';

class QuizService
{
    public function __construct(
        private CourseRepository $repository
    ) {}

    public function handle(
        ?array $slide,
        array $post,
        array $server,
        array &$session = []
    ): QuizResult {

        if (!$slide || empty($slide['is_quiz'])) {
            return new QuizResult();
        }

        $questions = $this->repository->getQuestions([$slide['id']]);
        $choicesByQuestion = $this->getRandomizedChoicesByQuestion($questions);

        $errors = [];
        $feedback = [];
        $answers = [];
        $submittedAnswers = [];

        $passed = false;
        $attempted = false;

        $storedSubmission = $this->consumeStoredSubmission($slide['id'] ?? null, $session);

        if ($storedSubmission !== null) {
            $attempted = true;
            $errors = $storedSubmission['errors'];
            $feedback = $storedSubmission['feedback'];
            $answers = $storedSubmission['answers'];
            $submittedAnswers = $storedSubmission['submittedAnswers'];
            $passed = $storedSubmission['passed'];
        }
        elseif (
            $server['REQUEST_METHOD'] === 'POST'
            && isset($post['quiz_submit'])
        ) {
            $attempted = true;
            $passed = true;
            $submittedAnswers = is_array($post['answers'] ?? []) ? $post['answers'] : [];

            foreach ($questions as $question) {
                $id = (string)$question['id'];
                $selected = $submittedAnswers[$id] ?? [];

                $selected = is_array($selected) ? $selected : [$selected];

                $selected = array_values(array_filter(array_map('strval', $selected), static function ($value) {
                    return $value !== '';
                }));

                if (!$selected) {
                    $errors[] = 'Please answer all quiz questions.';
                    $passed = false;
                    continue;
                }

                $correct = [];

                foreach (
                    $choicesByQuestion[$question['id']] ?? []
                    as $choice
                ) {
                    if (!empty($choice['is_correct'])) {
                        $correct[] = (string)$choice['id'];
                    }
                }

                sort($selected);
                sort($correct);

                $answers[$id] = $selected;

                $isCorrect = $selected === $correct;

                $feedback[$id] = [
                    'selected' => $selected,
                    'correct' => $correct,
                    'isCorrect' => $isCorrect
                ];

                $passed = $passed && $isCorrect;
            }

            $session['quiz_results'][(string)$slide['id']] = [
                'errors' => $errors,
                'feedback' => $feedback,
                'answers' => $answers,
                'submittedAnswers' => $submittedAnswers,
                'passed' => $passed && empty($errors),
                'attempted' => true,
            ];
        }

        return new QuizResult(
            questions: $questions,
            currentSlideQuestions: $questions,
            choicesByQuestion: $choicesByQuestion,
            answers: $answers,
            submittedAnswers: $submittedAnswers,
            feedback: $feedback,
            errors: $errors,
            passed: $passed && empty($errors),
            attempted: $attempted
        );
    }

    private function consumeStoredSubmission(?int $slideId, array &$session): ?array
    {
        if ($slideId === null) {
            return null;
        }

        $key = (string)$slideId;

        if (!isset($session['quiz_results'][$key])) {
            return null;
        }

        $submission = $session['quiz_results'][$key];
        unset($session['quiz_results'][$key]);

        return $submission;
    }

    private function getRandomizedChoicesByQuestion(array $questions): array
    {
        $choices = $this->repository->getChoices(array_column($questions, 'id'));

        $choicesByQuestion = [];

        foreach ($choices as $choice) {
            $choicesByQuestion[$choice['question_id']][] = $choice;
        }

        foreach ($choicesByQuestion as &$questionChoices) {
            shuffle($questionChoices);
        }
        unset($questionChoices);

        return $choicesByQuestion;
    }
}