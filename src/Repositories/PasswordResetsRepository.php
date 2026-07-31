<?php

class PasswordResetsRepository
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function recordReset(string $userId, string $token): void {
        $stmt = $this->pdo->prepare("
            INSERT INTO password_resets (user_id, token, expires_at)
            VALUES (:userId, :token, DATE_ADD(NOW(), INTERVAL 1 HOUR))
        ");
        $stmt->execute([
            'userId' => $userId,
            'token' => $token
            ]);
    }

}