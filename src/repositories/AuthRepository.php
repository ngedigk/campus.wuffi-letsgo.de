<?php

class AuthRepository
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function getLoginAttemptAmount(string $ip, int $windowMinutes): int
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM login_attempts
            WHERE ip = ?
            AND attempted_at > (NOW() - INTERVAL ? MINUTE)
        ");

        $stmt->execute([$ip, $windowMinutes]);

        return $stmt->fetchColumn();
    }

    public function recordFailedLogin(string $ip): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO login_attempts (ip)
            VALUES (?)
        ");

        $stmt->execute([$ip]);
    }

    public function clearOldAttempts(int $windowMinutes): void
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM login_attempts
            WHERE attempted_at < (NOW() - INTERVAL ? MINUTE)
        ");

        $stmt->execute([$windowMinutes]);
    }
}