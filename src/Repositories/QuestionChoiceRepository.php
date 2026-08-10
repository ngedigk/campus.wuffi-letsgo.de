<?php

namespace App\Repositories;

use App\Contracts\Repositories\QuestionChoiceRepositoryInterface;

use App\Dto\QuestionChoice;
use App\Dto\QuestionChoiceInput;

use \PDO;

class QuestionChoiceRepository implements QuestionChoiceRepositoryInterface
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function getByQuestionId(int $questionId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM question_choices WHERE question_id = :questionId");
        $stmt->execute(['questionId' => $questionId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => $this->createDto($row), $rows);
    }

    public function create(int $questionId, QuestionChoiceInput $questionChoice): QuestionChoice
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO question_choices (question_id, choice_text, is_correct)
            VALUES (:questionId, :choiceText, :isCorrect)
        ");
        $stmt->execute(array_merge(
            ['questionId' => $questionId],
            $questionChoice->toArray()
        ));

        return $this->createDto([
            'id' => (int)$this->pdo->lastInsertId(),
            'question_id' => $questionId,
            'choice_text' => $questionChoice->choiceText,
            'is_correct' => (int)$questionChoice->isCorrect
        ]);

    }

    public function update(QuestionChoice $questionChoice): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE question_choices
            SET choice_text = :choiceText, is_correct = :isCorrect
            WHERE id = :id
        ");
        $stmt->execute([
            'choiceText' => $questionChoice->choiceText,
            'isCorrect' => $questionChoice->isCorrect,
            'id' => $questionChoice->id
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM question_choices WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    public function deleteByQuestionId(int $questionId): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM question_choices WHERE question_id = :questionId");
        $stmt->execute(['questionId' => $questionId]);
    }

    private function createDto(array $row): QuestionChoice
    {
        return new QuestionChoice(
            id: $row['id'],
            questionId: $row['question_id'],
            choiceText: $row['choice_text'],
            isCorrect: $row['is_correct']
        );
    }
}