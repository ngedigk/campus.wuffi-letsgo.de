<?php

namespace App\Repositories;

use App\Dto\QuizQuestion;
use App\Dto\QuizQuestionInput;

use \PDO;

class QuizQuestionRepository {

    public function __construct(
        private PDO $pdo
    ) {}

    public function getById(int $id): ?QuizQuestion {
        $stmt = $this->pdo->prepare("
            SELECT
                qq.*
            FROM quiz_questions qq
            WHERE qq.id = :id
        ");

        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return $this->createDto($row);
    }

    public function getBySlideId(int $slideId): array {
        $stmt = $this->pdo->prepare("
            SELECT
                qq.*
            FROM quiz_questions qq
            WHERE qq.slide_id = :slideId
        ");

        $stmt->execute(['slideId' => $slideId]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function($row) {
            return $this->createDto($row);
        }, $rows);
    }

    public function create(QuizQuestionInput $quizQuestion): int {
        $stmt = $this->pdo->prepare("
            INSERT INTO quiz_questions (slide_id, question_text)
            VALUES (:slideId, :questionText)
        ");
        $stmt->execute([
            'slideId' => $quizQuestion->slideId,
            'questionText' => $quizQuestion->questionText
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(QuizQuestion $quizQuestion): void {
        $stmt = $this->pdo->prepare("
            UPDATE quiz_questions
            SET question_text = :questionText
            WHERE id = :id
        ");
        $stmt->execute([
            'questionText' => $quizQuestion->questionText,
            'id' => $quizQuestion->id
        ]);
    }

    public function delete(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM quiz_questions WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

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
            WHERE qq.slide_id = :slideId
            ORDER BY qq.id, c.id
        ");

        $stmt->execute(['slideId' => $slideId]);
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