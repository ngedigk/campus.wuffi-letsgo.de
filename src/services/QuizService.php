<?php

require_once __DIR__ . '/../dto/QuizResult.php';

class QuizService
{
    public function __construct(
        private CourseRepository $repository
    ) {}

    public function handle(
        ?Slide $slide
    ): QuizResult {

        if (!$slide || empty($slide->isQuiz)) {
            return new QuizResult();
        }

        $questions = $this->repository->getQuestions([$slide->id]);
        $choicesByQuestion = $this->getRandomizedChoicesByQuestion($questions);

        $errors = [];
        $feedback = [];
        $answers = [];
        $submittedAnswers = [];

        $passed = false;
        $attempted = false;

        $storedSubmission = $this->consumeStoredSubmission($slide->id ?? null);

        if ($storedSubmission !== null) {
            $attempted = true;
            $errors = $storedSubmission['errors'];
            $feedback = $storedSubmission['feedback'];
            $answers = $storedSubmission['answers'];
            $submittedAnswers = $storedSubmission['submittedAnswers'];
            $passed = $storedSubmission['passed'];
        }
        elseif (
            $_SERVER['REQUEST_METHOD'] === 'POST'
            && isset($_POST['quiz_submit'])
        ) {
            $attempted = true;
            $passed = true;
            $submittedAnswers = is_array($_POST['answers'] ?? []) ? $_POST['answers'] : [];

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
                        $correct[] = $choice['id'];
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

            $_SESSION['quiz_results'][$slide->id] = [
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

    private function consumeStoredSubmission(?int $slideId): ?array
    {
        if ($slideId === null) {
            return null;
        }

        $key = (string)$slideId;

        if (!isset($_SESSION['quiz_results'][$key])) {
            return null;
        }

        $submission = $_SESSION['quiz_results'][$key];
        unset($_SESSION['quiz_results'][$key]);

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