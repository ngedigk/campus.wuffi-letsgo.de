<?php

namespace App\Services;

use App\Repositories\EmailVerificationRepository;
use App\Repositories\UserRepository;

use \PDO;
use \Throwable;

class EmailVerificationService
{
    public function __construct(
        private PDO $pdo,
        private EmailVerificationRepository $emailVerificationRepository,
        private UserRepository $userRepository
    ) {}

    public function verify(string $token): array
    {
        $row = $this->emailVerificationRepository->findByToken($token);

        if (!$row) {
            return ['success' => false, 'message' => 'Ungültiger oder abgelaufener Token.'];
        }

        $this->pdo->beginTransaction();
        try {
            $this->userRepository->verify($row['user_id']);
            $this->emailVerificationRepository->deleteByToken($token);
            $this->pdo->commit();
            return ['success' => true];
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'message' => 'E-Mail konnte nicht verifiziert werden.'];
        }
    }
}