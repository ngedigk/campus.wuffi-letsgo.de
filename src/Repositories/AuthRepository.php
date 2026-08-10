<?php

namespace App\Repositories;

use App\Contracts\Repositories\AuthRepositoryInterface;

use \PDO;

class AuthRepository implements AuthRepositoryInterface
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function getLoginAttemptAmount(string $ip, int $windowMinutes): int
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM login_attempts
            WHERE ip = :ip
            AND attempted_at > (NOW() - INTERVAL :windowMinutes MINUTE)
        ");

        $stmt->execute([
            'ip' => $ip,
            'windowMinutes' => $windowMinutes
        ]);

        return $stmt->fetchColumn();
    }

    public function recordFailedLogin(string $ip): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO login_attempts (ip)
            VALUES (:ip)
        ");

        $stmt->execute(['ip' => $ip]);
    }

    public function clearOldAttempts(int $windowMinutes): void
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM login_attempts
            WHERE attempted_at < (NOW() - INTERVAL :windowMinutes MINUTE)
        ");

        $stmt->execute(['windowMinutes' => $windowMinutes]);
    }
}