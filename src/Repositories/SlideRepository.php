<?php

namespace App\Repositories;

use App\Contracts\Repositories\SlideRepositoryInterface;

use App\Dto\Slide;
use App\Dto\SlideInput;

use \PDO;

class SlideRepository implements SlideRepositoryInterface
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function get(int $slideId): Slide
    {
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

    public function getByModuleId(int $moduleId): array
    {
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

    public function getSlidesByAudioUrl(string $audioUrl): array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM module_slides ms
            WHERE audio_url = :audioUrl
        ");

        $stmt->execute(['audioUrl' => $audioUrl]);

        return array_map(function($row) {
            return $this->createDto($row);
        }, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function create(SlideInput $slide): Slide
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO module_slides
            (module_id, title, html_content, audio_url, sort_order)
            VALUES (:moduleId, :title, :htmlContent, :audioUrl, :sortOrder)
        ");

        $stmt->execute($slide->toArray());

        return $this->createDto([
            'id' => (int)$this->pdo->lastInsertId(),
            'title' => $slide->title,
            'html_content' => $slide->htmlContent,
            'audio_url' => $slide->audioUrl,
            'sort_order' => $slide->sortOrder
        ]);
    }

    public function update(Slide $slide): Slide
    {
        $stmt = $this->pdo->prepare("
            UPDATE module_slides
            SET title = :title, html_content = :htmlContent, audio_url = :audioUrl, sort_order = :sortOrder
            WHERE id = :id
        ");
        $stmt->execute($slide->toArray());

        return $this->createDto([
            'id' => $slide->id,
            'title' => $slide->title,
            'html_content' => $slide->htmlContent,
            'audio_url' => $slide->audioUrl,
            'sort_order' => $slide->sortOrder
        ]);
    }

    public function exists(int $slideId): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM module_slides WHERE id = :slideId LIMIT 1");
        $stmt->execute(['slideId' => $slideId]);
        return (bool)$stmt->fetchColumn();
    }

    public function delete(int $slideId): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM module_slides WHERE id = :slideId");
        $stmt->execute(['slideId' => $slideId]);
    }

    private function createDto(array $row): Slide
    {
        return new Slide(
            id: (int)$row['id'],
            title: $row['title'],
            htmlContent: $row['html_content'],
            audioUrl: $row['audio_url'],
            sortOrder: (int)$row['sort_order']
        );
    }
}