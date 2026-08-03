<?php

namespace App\Services;

use App\Contracts\TransactionManager;

use App\Repositories\EmailVerificationRepository;
use App\Repositories\UserRepository;

use \Throwable;

class EmailVerificationService
{
    public function __construct(
        private TransactionManager $transactionManager,
        private EmailVerificationRepository $emailVerificationRepository,
        private UserRepository $userRepository
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