<?php

namespace App\Repositories;

use App\Dto\QuestionChoice;
use PDO;

class QuestionChoicesRepository {

    public function __construct(
        private PDO $pdo
    ) {}

    public function getByQuestionId(int $questionId): array {
        $stmt = $this->pdo->prepare("SELECT * FROM question_choices WHERE question_id = ?");
        $stmt->execute([$questionId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => $this->createDto($row), $rows);
    }

    public function create(QuestionChoice $questionChoice): int {
        $stmt = $this->pdo->prepare("
            INSERT INTO question_choices (question_id, choice_text, is_correct)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([
            $questionChoice->questionId,
            $questionChoice->choiceText,
            $questionChoice->isCorrect
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(QuestionChoice $questionChoice): void {
        $stmt = $this->pdo->prepare("
            UPDATE question_choices
            SET choice_text = ?, is_correct = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $questionChoice->choiceText,
            $questionChoice->isCorrect,
            $questionChoice->id
        ]);
    }

    public function delete(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM question_choices WHERE id = ?");
        $stmt->execute([$id]);
    }

    private function createDto(array $row): QuestionChoice {
        return new QuestionChoice(
            id: $row['id'],
            questionId: $row['question_id'],
            choiceText: $row['choice_text'],
            isCorrect: $row['is_correct']
        );
    }
}