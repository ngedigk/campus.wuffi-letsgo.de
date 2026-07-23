<?php

require_once __DIR__ . "/../dto/Slide.php";

class SlideRepository
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function get(int $slideId) {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM module_slides
            WHERE id = ?
            ORDER BY sort_order
        ");

        $stmt->execute([$slideId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $this->createDto($row);
    }

    public function getByModule(int $moduleId): array {
        $stmt = $this->pdo->prepare("
            SELECT
                ms.*
            FROM module_slides ms
            INNER JOIN course_modules cm
                ON cm.id = ms.module_id
            WHERE cm.id = ?
            ORDER BY
                cm.sort_order,
                ms.sort_order
        ");

        $stmt->execute([$moduleId]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function($row) {
            return $this->createDto($row);
        }, $rows);
    }

    public function create(CreateSlide $slide): int {
        $stmt = $this->pdo->prepare("
            INSERT INTO module_slides
            (module_id, title, html_content, audio_url, sort_order, is_quiz)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $slide->moduleId,
            $slide->title,
            $slide->htmlContent,
            $slide->audioUrl,
            $slide->sortOrder,
            (int) $slide->isQuiz
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(Slide $slide): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE module_slides
            SET title = ?, html_content = ?, audio_url = ?, sort_order = ?, is_quiz = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $slide->title,
            $slide->htmlContent,
            $slide->audioUrl,
            $slide->sortOrder,
            (int) $slide->isQuiz,
            $slide->id
        ]);
    }

    public function delete(int $slideId): void {
        $stmt = $this->pdo->prepare("DELETE FROM module_slides WHERE id = ?");
        $stmt->execute([$slideId]);
    }

    private function createDto(array $row): Slide {
        return new Slide(
            $row['id'],
            $row['title'],
            $row['html_content'],
            $row['audio_url'],
            $row['sort_order'],
            $row['is_quiz']
        );
    }
}

