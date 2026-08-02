<?php

namespace App\Repositories;

use App\Dto\Module;
use App\Dto\ModuleInput;

use \PDO;

class ModuleRepository
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function get(int $moduleId) {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM course_modules
            WHERE id = :moduleId
            ORDER BY sort_order
        ");

        $stmt->execute(['moduleId' => $moduleId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $this->createDto($row);
    }

    public function getByCourseId(string $courseId): array {
        $stmt = $this->pdo->prepare("
            SELECT
                cm.*
            FROM course_modules cm
            INNER JOIN courses c
                ON c.id = cm.course_id
            WHERE c.id = :courseId
            ORDER BY
                cm.sort_order
        ");

        $stmt->execute(['courseId' => $courseId]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function($row) {
            return $this->createDto($row);
        }, $rows);
    }

    public function create(ModuleInput $module): int {
        $stmt = $this->pdo->prepare("
            INSERT INTO course_modules
            (course_id, title, sort_order)
            VALUES (:courseId, :title, :sortOrder)
        ");

        $stmt->execute([
            'courseId' => $module->courseId,
            'title' => $module->title,
            'sortOrder' => $module->sortOrder
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(Module $module): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE course_modules
            SET title = :title, sort_order = :sortOrder
            WHERE id = :moduleId
        ");
        $stmt->execute([
            'title' => $module->title,
            'sortOrder' => $module->sortOrder,
            'moduleId' => $module->id
        ]);
    }

    public function delete(int $moduleId): void {
        $stmt = $this->pdo->prepare("DELETE FROM course_modules WHERE id = :moduleId");
        $stmt->execute(['moduleId' => $moduleId]);
    }

    private function createDto(array $row): Module {
        return new Module(
            id: $row['id'],
            title: $row['title'],
            sortOrder: $row['sort_order'],
            slides: null
        );
    }
}