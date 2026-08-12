<?php

namespace App\Repositories;

use App\Contracts\Repositories\ModuleRepositoryInterface;

use App\Dto\Module;
use App\Dto\ModuleInput;

use \PDO;

class ModuleRepository implements ModuleRepositoryInterface
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function get(int $moduleId): ?Module
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM course_modules
            WHERE id = :moduleId
            ORDER BY sort_order
        ");

        $stmt->execute(['moduleId' => $moduleId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return $this->createDto($row);
    }

    public function getByCourseId(string $courseId): array
    {
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

    public function create(ModuleInput $module): Module
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO course_modules
            (course_id, title, sort_order)
            VALUES (:courseId, :title, :sortOrder)
        ");

        $stmt->execute($module->toArray());

        return $this->createDto([
            'id' => (int)$this->pdo->lastInsertId(),
            'title' => $module->title,
            'sort_order' => $module->sortOrder
        ]);
    }

    public function update(Module $module): Module
    {
        $stmt = $this->pdo->prepare("
            UPDATE course_modules
            SET title = :title, sort_order = :sortOrder
            WHERE id = :id
        ");
        $stmt->execute($module->toArray());

        return $this->createDto([
            'id' => $module->id,
            'title' => $module->title,
            'sort_order' => $module->sortOrder
        ]);
    }

    public function exists(int $moduleId): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM course_modules WHERE id = :moduleId LIMIT 1");
        $stmt->execute(['moduleId' => $moduleId]);
        return (bool)$stmt->fetchColumn();
    }

    public function delete(int $moduleId): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM course_modules WHERE id = :moduleId");
        $stmt->execute(['moduleId' => $moduleId]);
    }

    private function createDto(array $row): Module
    {
        return new Module(
            id: (int)$row['id'],
            title: $row['title'],
            sortOrder: (int)$row['sort_order']
        );
    }
}