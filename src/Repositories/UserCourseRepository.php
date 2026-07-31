<?php

class UserCourseRepository
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function userHasCourse(
        string $userUuid,
        string $courseUuid
    ): bool {

        $stmt = $this->pdo->prepare("
            SELECT 1
            FROM user_courses
            WHERE user_id = ?
              AND course_id = ?
        ");

        $stmt->execute([
            $userUuid,
            $courseUuid
        ]);

        return (bool)$stmt->fetch();
    }


    public function addCourse(
        string $userUuid,
        string $courseUuid,
        int $accessCodeId
    ): void {

        $stmt = $this->pdo->prepare("
            INSERT INTO user_courses
                (user_id, course_id, access_code_id)
            VALUES (?, ?, ?)
        ");

        $stmt->execute([
            $userUuid,
            $courseUuid,
            $accessCodeId
        ]);
    }
}