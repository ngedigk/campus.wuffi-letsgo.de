<?php

namespace App\Contracts;

interface AuthRepositoryInterface
{
    public function getLoginAttemptAmount(string $ip, int $windowMinutes): int;

    public function recordFailedLogin(string $ip): void;

    public function clearOldAttempts(int $windowMinutes): void;
}
