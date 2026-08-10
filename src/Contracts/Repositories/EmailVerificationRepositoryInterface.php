<?php

namespace App\Contracts\Repositories;

interface EmailVerificationRepositoryInterface
{
    public function findByToken(string $token): ?array;

    public function deleteByToken(string $token): void;

    public function upsert(string $userId, string $token): void;
}
