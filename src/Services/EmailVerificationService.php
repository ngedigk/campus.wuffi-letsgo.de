<?php

namespace App\Services;

use App\Contracts\Database\TransactionManagerInterface;
use App\Contracts\Repositories\EmailVerificationRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;

use \Throwable;

class EmailVerificationService
{
    public function __construct(
        private TransactionManagerInterface $transactionManager,
        private EmailVerificationRepositoryInterface $emailVerificationRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    public function verify(string $token): array
    {
        $row = $this->emailVerificationRepository->findByToken($token);

        if (!$row) {
            return ['success' => false, 'error' => 'Ungültiger oder abgelaufener Token.'];
        }

        try {
            $this->transactionManager->run(
                function () use ($row, $token) {
                    $this->userRepository->verify($row['user_id']);
                    $this->emailVerificationRepository->deleteByToken($token);
                }
            );
            return ['success' => true];
        } catch (Throwable $e) {
            return ['success' => false, 'error' => 'E-Mail konnte nicht verifiziert werden.'];
        }
    }
}