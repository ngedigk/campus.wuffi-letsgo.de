<?php

class AccessCodeRepository
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function findByCodeForUpdate(string $code): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT id, course_id
            FROM access_codes
            WHERE code = ?
            FOR UPDATE
        ");

        $stmt->execute([$code]);

        return $stmt->fetch() ?: null;
    }

    public function existsByCode(string $code): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT 1
            FROM access_codes
            WHERE code = ?
        ");

        $stmt->execute([$code]);

        return (bool)$stmt->fetch();
    }

    public function create(string $code, string $courseUuid): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO access_codes
            (
                code,
                course_id
            )
            VALUES (?, ?)
        ");

        $stmt->execute([$code, $courseUuid]);
    }

    public function createForRegistration(int $registrationCodeId, string $userId, string $courseId): int
    {
        $accessCode = bin2hex(random_bytes(16));
        $stmt = $this->pdo->prepare("
            INSERT INTO access_codes (code, course_id)
            VALUES (?, ?)
        ");
        $stmt->execute([$accessCode, $courseId]);
        return (int)$this->pdo->lastInsertId();
    }

    public function list(): array
    {
        $stmt = $this->pdo->prepare("
            SELECT id, code, course_id
            FROM access_codes
        ");

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
