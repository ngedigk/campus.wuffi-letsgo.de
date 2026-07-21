<?php

require_once __DIR__ . '/../dto/Course.php';

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
                uc.is_completed,
                uc.completed_at
            FROM courses c
            INNER JOIN user_courses uc
                ON uc.course_id = c.id
            WHERE
                uc.user_id = ?
                AND c.id = ?
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
                uc.is_completed,
                uc.completed_at
            FROM courses c
            INNER JOIN user_courses uc
                ON uc.course_id = c.id
            WHERE
                uc.user_id = ?
        ");

        $stmt->execute([$userUuid]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function($row) {
            return $this->createDto($row);
        }, $rows);
    }

    public function getQuestions(array $slideIds): array
    {
        if (empty($slideIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($slideIds), '?'));

        $stmt = $this->pdo->prepare("
            SELECT *
            FROM quiz_questions
            WHERE slide_id IN ($placeholders)
            ORDER BY id
        ");

        $stmt->execute($slideIds);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getChoices(array $questionIds): array
    {
        if (empty($questionIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($questionIds), '?'));

        $stmt = $this->pdo->prepare("
            SELECT *
            FROM question_choices
            WHERE question_id IN ($placeholders)
            ORDER BY sort_order
        ");

        $stmt->execute($questionIds);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(CreateCourse $course): string {
        $stmt = $this->pdo->prepare("
            INSERT INTO courses
            (
                id,
                title,
                description,
                prerequisite_course_id
            )
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $course->uuid,
            $course->title,
            $course->description,
            $course->prerequisiteCourseId
        ]);

        return $course->uuid;
    }

    public function update(CreateCourse $course): void {
        $stmt = $this->pdo->prepare("
            UPDATE courses
            SET title = ?, description = ?, prerequisite_course_id = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $course->title,
            $course->description,
            $course->prerequisiteCourseId,
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
            false,
            false,
            null
        );
    }
}

