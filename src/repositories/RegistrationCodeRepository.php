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

    public function create(string $code): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO registration_codes (code)
            VALUES (?)
        ");

        $stmt->execute([$code]);
    }
}