<?php

require_once __DIR__ . "/../dto/QuizQuestion.php";

class QuizRepository {

    public function __construct(
        private PDO $pdo
    ) {}

    public function getBySlideId(int $slideId): array {
        $stmt = $this->pdo->prepare("
            SELECT
                qq.*
            FROM quiz_questions qq
            WHERE qq.slide_id = ?
        ");

        $stmt->execute([$slideId]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function($row) {
            return $this->createDto($row);
        }, $rows);
    }

    public function update(QuizQuestion $quizQuestion): void {
        $stmt = $this->pdo->prepare("
            UPDATE quiz_questions
            SET question_text = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $quizQuestion->questionText,
            $quizQuestion->id
        ]);
    }

    public function delete(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM quiz_questions WHERE id = ?");
        $stmt->execute([$id]);
    }

    /**
     * Fetches all questions and their choices for a given slide.
     * Returns an associative array keyed by question_id with questions and choices.
     */
    public function getQuizDataForSlide(int $slideId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                qq.id,
                qq.question_text,
                c.id as choice_id,
                c.choice_text,
                c.is_correct
            FROM quiz_questions qq
            JOIN question_choices c ON c.question_id = qq.id
            WHERE qq.slide_id = ?
            ORDER BY qq.id, c.id
        ");

        $stmt->execute([$slideId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            return ['questions' => [], 'choices' => []];
        }

        $questions = [];
        $choices = [];

        foreach ($rows as $row) {
            $qId = (int)$row['id'];
            if (!isset($questions[$qId])) {
                $questions[$qId] = [
                    'id' => $qId,
                    'question_text' => $row['question_text'],
                ];
                $choices[$qId] = [];
            }
            $choices[$qId][] = [
                'id' => (int)$row['choice_id'],
                'choice_text' => $row['choice_text'],
                'is_correct' => (bool)$row['is_correct'],
            ];
        }

        return ['questions' => $questions, 'choices' => $choices];
    }

    private function createDto(array $row): QuizQuestion {
        return new QuizQuestion(
            id: $row['id'],
            slideId: $row['slide_id'],
            questionText: $row['question_text']
        );
    }
}

?>