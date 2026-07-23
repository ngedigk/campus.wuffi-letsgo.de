<?php

require_once __DIR__ . "/../dto/Course.php";

class CourseRepository
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function get(string $courseUuid): Course
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM courses
            WHERE id = ?
            ORDER BY sort_order
        ");

        $stmt->execute([$courseUuid]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $this->createDto($row);
    }

    public function getCourseForUser(string $userUuid, string $courseUuid): ?Course
    {
        $stmt = $this->pdo->prepare("
            SELECT
                c.id,
                c.title,
                c.description,
                c.prerequisite_course_id,
                c.sort_order
            FROM courses c
            INNER JOIN user_courses uc
                ON uc.course_id = c.id
            WHERE
                uc.user_id = ?
                AND c.id = ?
            ORDER BY c.sort_order
        ");

        $stmt->execute([$userUuid, $courseUuid]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return $this->createDto($row);
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM courses
            ORDER BY sort_order
        ");

        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function($row) {
            return $this->createDto($row);
        }, $rows);
    }

    public function getAllForUser(string $userUuid): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                c.id,
                c.title,
                c.description,
                c.prerequisite_course_id,
                c.sort_order
            FROM courses c
            INNER JOIN user_courses uc
                ON uc.course_id = c.id
            WHERE
                uc.user_id = ?
            ORDER BY c.sort_order
        ");

        $stmt->execute([$userUuid]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function($row) {
            return $this->createDto($row);
        }, $rows);
    }

    public function create(CreateCourse $course): string {
        $stmt = $this->pdo->prepare("
            INSERT INTO courses
            (
                id,
                title,
                description,
                prerequisite_course_id,
                sort_order
            )
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $course->uuid,
            $course->title,
            $course->description,
            $course->prerequisiteCourseId,
            $course->sortOrder
        ]);

        return $course->uuid;
    }

    public function update(CreateCourse $course): void {
        $stmt = $this->pdo->prepare("
            UPDATE courses
            SET title = ?, description = ?, prerequisite_course_id = ?, sort_order = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $course->title,
            $course->description,
            $course->prerequisiteCourseId,
            $course->sortOrder,
            $course->uuid
        ]);
    }

    public function delete(string $uuid): void {
        $stmt = $this->pdo->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->execute([$uuid]);
    }

    private function createDto(array $row): Course {
        return new Course(
            $row['id'],
            $row['title'],
            $row['description'],
            $row['prerequisite_course_id'],
            $row['sort_order'],
            false,
            false,
            null
        );
    }
}


