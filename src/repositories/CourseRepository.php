<?php

class CourseRepository
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function getCourseForUser(string $userUuid, string $courseUuid): ?array
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

        $course = $stmt->fetch(PDO::FETCH_ASSOC);

        return $course ?: null;
    }

    public function isCourseUnlocked(string $userUuid, ?string $prerequisiteCourseId): bool
    {
        if ($prerequisiteCourseId === null || $prerequisiteCourseId === '') {
            return true;
        }

        $stmt = $this->pdo->prepare("
            SELECT is_completed
            FROM user_courses
            WHERE user_id = ?
              AND course_id = ?
        ");

        $stmt->execute([$userUuid, $prerequisiteCourseId]);

        return (int)$stmt->fetchColumn() === 1;
    }

    public function getModules(string $courseUuid): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                id,
                title,
                sort_order
            FROM course_modules
            WHERE course_id = ?
            ORDER BY sort_order
        ");

        $stmt->execute([$courseUuid]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSlides(string $courseUuid): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                ms.*,
                cm.title AS module_title
            FROM module_slides ms
            INNER JOIN course_modules cm
                ON cm.id = ms.module_id
            WHERE cm.course_id = ?
            ORDER BY
                cm.sort_order,
                ms.sort_order
        ");

        $stmt->execute([$courseUuid]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCompletedModuleIds(string $userUuid, array $moduleIds): array
    {
        if (empty($moduleIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($moduleIds), '?'));

        $stmt = $this->pdo->prepare("
            SELECT module_id
            FROM user_module_completions
            WHERE
                user_id = ?
                AND module_id IN ($placeholders)
        ");

        $stmt->execute(array_merge([$userUuid], $moduleIds));

        $completed = [];

        foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $moduleId) {
            $completed[(string)$moduleId] = true;
        }

        return $completed;
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

    public function recordSlideView(string $userUuid, string $slideId): void
    {
        $stmt = $this->pdo->prepare("
            INSERT IGNORE INTO user_slide_views
            (
                user_id,
                slide_id,
                viewed_at
            )
            VALUES
            (
                ?,
                ?,
                NOW()
            )
        ");

        $stmt->execute([$userUuid, $slideId]);
    }

    public function getViewedSlideIds(string $userUuid, array $slideIds): array
    {
        if (empty($slideIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($slideIds), '?'));

        $stmt = $this->pdo->prepare("
            SELECT slide_id
            FROM user_slide_views
            WHERE user_id = ?
              AND slide_id IN ($placeholders)
        ");

        $stmt->execute(array_merge([$userUuid], $slideIds));

        $viewed = [];

        foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $slideId) {
            $viewed[(string)$slideId] = true;
        }

        return $viewed;
    }

    public function completeModule(string $userUuid, string $moduleId): void
    {
        $stmt = $this->pdo->prepare("
            INSERT IGNORE INTO user_module_completions
            (
                user_id,
                module_id,
                completed_at
            )
            VALUES
            (
                ?,
                ?,
                NOW()
            )
        ");

        $stmt->execute([
            $userUuid,
            $moduleId
        ]);
    }

    public function completeCourse(string $userUuid, string $courseUuid): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE user_courses
            SET
                is_completed = 1,
                completed_at = NOW()
            WHERE
                user_id = ?
                AND course_id = ?
        ");

        $stmt->execute([
            $userUuid,
            $courseUuid
        ]);
    }

    public function create(
        string $id,
        string $title,
        string $description,
        ?string $prerequisiteCourseId
    ): void {
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
            $id,
            $title,
            $description,
            $prerequisiteCourseId
        ]);
    }

    public function listAll(): array
    {
        $stmt = $this->pdo->prepare("
            SELECT id, title, description, prerequisite_course_id
            FROM courses
            ORDER BY title
        ");

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }

    public function rollback(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }
}