<?php

namespace App\Repositories;

use \PDO;

class UserCourseRepository
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function userHasCourse(string $userUuid, string $courseUuid): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT 1
            FROM user_courses
            WHERE user_id = :userUuid
              AND course_id = :courseUuid
        ");

        $stmt->execute([
            'userUuid' => $userUuid,
            'courseUuid' => $courseUuid
        ]);

        return (bool)$stmt->fetch();
    }

    public function addCourse(string $userUuid, string $courseUuid, int $accessCodeId): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO user_courses
                (user_id, course_id, access_code_id)
            VALUES (:userUuid, :courseUuid, :accessCodeId)
        ");

        $stmt->execute([
            'userUuid' => $userUuid,
            'courseUuid' => $courseUuid,
            'accessCodeId' => $accessCodeId
        ]);
    }
}