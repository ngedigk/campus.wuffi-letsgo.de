<?php

require_once __DIR__ . '/../dto/Module.php';

class ModuleRepository
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function get(int $moduleId) {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM course_modules
            WHERE id = ?
            ORDER BY sort_order
        ");

        $stmt->execute([$moduleId]);

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
            WHERE c.id = ?
            ORDER BY
                cm.sort_order
        ");

        $stmt->execute([$courseId]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function($row) {
            return $this->createDto($row);
        }, $rows);
    }

    public function create(CreateModule $module): int {
        $stmt = $this->pdo->prepare("
            INSERT INTO course_modules
            (course_id, title, sort_order)
            VALUES (?, ?, ?)
        ");

        $stmt->execute([
            $module->courseId,
            $module->title,
            $module->sortOrder
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(Module $module): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE course_modules
            SET title = ?, sort_order = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $module->title,
            $module->sortOrder,
            $module->id
        ]);
    }

    public function delete(int $moduleId): void {
        $stmt = $this->pdo->prepare("DELETE FROM course_modules WHERE id = ?");
        $stmt->execute([$moduleId]);
    }

    private function createDto(array $row): Module {
        return new Module(
            $row['id'],
            $row['title'],
            $row['sort_order'],
            null
        );
    }
}