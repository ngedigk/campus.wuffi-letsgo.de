<?php

class RegistrationCodeRepository
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function findByCodeForUpdate(string $code): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM registration_codes
            WHERE code = ?
            FOR UPDATE
        ");

        $stmt->execute([$code]);

        return $stmt->fetch() ?: null;
    }

    public function isUsed(string $code): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT 1
            FROM registration_codes
            WHERE code = ?
              AND used_by_user_id IS NOT NULL
        ");

        $stmt->execute([$code]);

        return (bool)$stmt->fetch();
    }

    public function markAsUsed(int $id, string $userId): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE registration_codes
            SET used_by_user_id = ?,
                used_at = NOW()
            WHERE id = ?
        ");

        $stmt->execute([$userId, $id]);
    }

    public function create(string $code, array $courseIds = []): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO registration_codes (code) VALUES (?)"
        );
        $stmt->execute([$code]);
        $id = (int)$this->pdo->lastInsertId();

        if (!empty($courseIds)) {
            $placeholders = [];
            $values = [];
            foreach ($courseIds as $courseId) {
                $placeholders[] = '(?, ?)';
                $values[] = $id;
                $values[] = $courseId;
            }
            
            $stmt = $this->pdo->prepare(
                "INSERT INTO registration_code_courses (registration_code_id, course_id) VALUES " . implode(',', $placeholders)
            );
            $stmt->execute($values);
        }
    }

    public function getCourseIds(int $registrationCodeId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT course_id FROM registration_code_courses WHERE registration_code_id = ?"
        );
        $stmt->execute([$registrationCodeId]);
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'course_id');
    }

    public function addCourses(int $registrationCodeId, array $courseIds): void
    {
        if (empty($courseIds)) {
            return;
        }
        $placeholders = implode(',', array_fill(0, count($courseIds), '(?, ?)'));
        $stmt = $this->pdo->prepare(
            "INSERT IGNORE INTO registration_code_courses (registration_code_id, course_id) VALUES " . $placeholders
        );
        $values = [];
        foreach ($courseIds as $courseId) {
            $values[] = $registrationCodeId;
            $values[] = $courseId;
        }
        $stmt->execute($values);
    }

    public function removeCourses(int $registrationCodeId, array $courseIds): void
    {
        if (empty($courseIds)) {
            return;
        }
        $placeholders = implode(',', array_fill(0, count($courseIds), '?'));
        $stmt = $this->pdo->prepare(
            "DELETE FROM registration_code_courses WHERE registration_code_id = ? AND course_id IN ($placeholders)"
        );
        $stmt->execute(array_merge([$registrationCodeId], $courseIds));
    }

    public function removeAllCourses(int $registrationCodeId): void
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM registration_code_courses WHERE registration_code_id = ?"
        );
        $stmt->execute([$registrationCodeId]);
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query("
            SELECT
                rc.id,
                rc.code,
                rc.used_by_user_id,
                rc.used_at,
                GROUP_CONCAT(rcc.course_id) as course_ids
            FROM registration_codes rc
            LEFT JOIN registration_code_courses rcc
                ON rc.id = rcc.registration_code_id
            GROUP BY rc.id, rc.code, rc.used_by_user_id, rc.used_at
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM registration_codes WHERE id = ?");
        $stmt->execute([$id]);
    }
}

