<?php

namespace App\Repositories;

use \PDO;

class PasswordResetsRepository
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function getUserUuidByToken(string $token): string
    {
        $stmt = $this->pdo->prepare("
            SELECT user_id
            FROM password_resets
            WHERE token = :token
            AND expires_at > NOW()
        ");
        $stmt->execute([
            'token' => $token
        ]);

        return $stmt->fetchColumn();
    }

    public function recordReset(string $userId, string $token): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO password_resets (user_id, token, expires_at)
            VALUES (:userId, :token, DATE_ADD(NOW(), INTERVAL 1 HOUR))
        ");
        $stmt->execute([
            'userId' => $userId,
            'token' => $token
        ]);
    }

    public function deleteRecordsByUserId(string $userId): void
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM password_resets
            WHERE user_id = :userId
        ");
        $stmt->execute([
            'userId' => $userId
        ]);
    }
}