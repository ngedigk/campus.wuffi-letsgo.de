<?php

class EmailVerificationRepository
{
    public function __construct(
        private PDO $pdo
    ) {}


    public function create(
        string $userId,
        string $token
    ): void {

        $stmt = $this->pdo->prepare("
            INSERT INTO email_verifications
            (
                user_id,
                token,
                expires_at
            )
            VALUES
            (
                ?,
                ?,
                DATE_ADD(NOW(), INTERVAL 1 DAY)
            )
        ");

        $stmt->execute([
            $userId,
            $token
        ]);
    }
}