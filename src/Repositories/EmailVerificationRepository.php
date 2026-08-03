<?php

namespace App\Repositories;

use \PDO;

class EmailVerificationRepository
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function findByToken(string $token): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT user_id FROM email_verifications
            WHERE token = :token AND expires_at > NOW()
        ");

        $stmt->execute(['token' => $token]);

        return $stmt->fetch() ?: null;
    }

    public function deleteByToken(string $token): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM email_verifications WHERE token = :token");
        $stmt->execute(['token' => $token]);
    }

    public function upsert(string $userId, string $token): void {
        $stmt = $this->pdo->prepare("
            INSERT INTO email_verifications (user_id, token, expires_at)
            VALUES (:userId, :insertToken, NOW() + INTERVAL 1 HOUR)
            ON DUPLICATE KEY UPDATE token = :updateToken, expires_at = NOW() + INTERVAL 1 HOUR
        ");

        $stmt->execute([
            'userId' => $userId,
            'insertToken' => $token,
            'updateToken' => $token
        ]);
    }
}