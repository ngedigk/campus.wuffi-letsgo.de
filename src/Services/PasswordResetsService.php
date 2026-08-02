<?php

namespace App\Services;

use App\Repositories\PasswordResetsRepository;

class PasswordResetsService
{
    public function __construct(
        private PasswordResetsRepository $passwordResetsRepository
    ) {}

    public function getUserUuidByToken(string $token): string
    {
        return $this->passwordResetsRepository->getUserUuidByToken($token);
    }

    public function recordReset(string $userId, string $token): void
    {
        $this->passwordResetsRepository->recordReset($userId, $token);
    }

    public function deleteRecordsByUserId(string $userId): void
    {
        $this->passwordResetsRepository->deleteRecordsByUserId($userId);
    }
}