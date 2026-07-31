<?php

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
            return ['success' => false, 'message' => 'Invalid or expired token.'];
        }

        $this->pdo->beginTransaction();
        try {
            $this->userRepository->verify($row['user_id']);
            $this->emailVerificationRepository->deleteByToken($token);
            $this->pdo->commit();
            return ['success' => true];
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'message' => 'Could not verify email.'];
        }
    }
}
