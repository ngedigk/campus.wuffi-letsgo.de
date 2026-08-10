<?php

namespace App\Contracts\Repositories;

interface PasswordResetsRepositoryInterface
{
    public function getUserUuidByToken(string $token): string;

    public function recordReset(string $userId, string $token): void;

    public function deleteRecordsByUserId(string $userId): void;
}