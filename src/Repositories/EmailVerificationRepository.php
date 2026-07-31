<?php

class EmailVerificationRepository
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function findByToken(string $token): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT user_id FROM email_verifications
            WHERE token = ? AND expires_at > NOW()
        ");

        $stmt->execute([$token]);

        return $stmt->fetch() ?: null;
    }

    public function deleteByToken(string $token): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM email_verifications WHERE token = ?");
        $stmt->execute([$token]);
    }
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