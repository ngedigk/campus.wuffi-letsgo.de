<?php

namespace App\Repositories;

use App\Dto\Slide;
use App\Dto\SlideInput;
use PDO;

class SlideRepository
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function get(int $slideId) {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM module_slides
            WHERE id = :slideId
            ORDER BY sort_order
        ");

        $stmt->execute(['slideId' => $slideId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $this->createDto($row);
    }

    public function getByModuleId(int $moduleId): array {
        $stmt = $this->pdo->prepare("
            SELECT
                ms.*
            FROM module_slides ms
            INNER JOIN course_modules cm
                ON cm.id = ms.module_id
            WHERE cm.id = :moduleId
            ORDER BY
                cm.sort_order,
                ms.sort_order
        ");

        $stmt->execute(['moduleId' => $moduleId]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function($row) {
            return $this->createDto($row);
        }, $rows);
    }

    public function create(SlideInput $slide): int {
        $stmt = $this->pdo->prepare("
            INSERT INTO module_slides
            (module_id, title, html_content, audio_url, sort_order, is_quiz)
            VALUES (:moduleId, :title, :htmlContent, :audioUrl, :sortOrder, :isQuiz)
        ");

        $stmt->execute([
            'moduleId' => $slide->moduleId,
            'title' => $slide->title,
            'htmlContent' => $slide->htmlContent,
            'audioUrl' => $slide->audioUrl,
            'sortOrder' => $slide->sortOrder,
            'isQuiz' => (int) $slide->isQuiz
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(Slide $slide): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE module_slides
            SET title = :title, html_content = :htmlContent, audio_url = :audioUrl, sort_order = :sortOrder, is_quiz = :isQuiz
            WHERE id = :slideId
        ");
        $stmt->execute([
            'title' => $slide->title,
            'htmlContent' => $slide->htmlContent,
            'audioUrl' => $slide->audioUrl,
            'sortOrder' => $slide->sortOrder,
            'isQuiz' => (int) $slide->isQuiz,
            'slideId' => $slide->id
        ]);
    }

    public function delete(int $slideId): void {
        $stmt = $this->pdo->prepare("DELETE FROM module_slides WHERE id = :slideId");
        $stmt->execute(['slideId' => $slideId]);
    }

    private function createDto(array $row): Slide {
        return new Slide(
            id: $row['id'],
            title: $row['title'],
            htmlContent: $row['html_content'],
            audioUrl: $row['audio_url'],
            sortOrder: $row['sort_order'],
            isQuiz: (bool) $row['is_quiz']
        );
    }
}