<?php

require_once __DIR__ . '/../dto/QuizQuestion.php';

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

    private function createDto(array $row): QuizQuestion {
        return new QuizQuestion(
            id: $row['id'],
            slideId: $row['slide_id'],
            questionText: $row['question_text']
        );
    }
}

?>