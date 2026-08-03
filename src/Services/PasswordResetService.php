<?php

namespace App\Services;

use App\Contracts\Mailer;
use App\Contracts\TransactionManager;

use App\Repositories\UserRepository;
use App\Repositories\PasswordResetsRepository;

class PasswordResetService
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordResetsRepository $passwordResetsRepository,
        private Mailer $mailer,
        private TransactionManager $transactionManager
    ) {}

    public function requestReset(string $email): void
    {
        $user = $this->userRepository->findByEmail($email);
        if (!$user) {
            return;
        }
        
        $token = bin2hex(random_bytes(32));
        
        $this->passwordResetsRepository->recordReset($user->id, $token);
        
        $this->sendResetEmail($email, $token);
    }

    public function getUserUuidByToken(string $token): ?string
    {
        if ($token === '') {
            return null;
        }

        return $this->passwordResetsRepository->getUserUuidByToken($token);
    }
    
    public function resetPassword(string $userUuid, string $password): void
    {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $this->transactionManager->run(
            function () use ($userUuid, $passwordHash) {
                $this->userRepository->setPassword($userUuid, $passwordHash);
                $this->passwordResetsRepository->deleteRecordsByUserId($userUuid);
            }
        );
    }
    
    private function sendResetEmail(string $email, string $token): void
    {
        $link = SITE_URL . '/reset-password.php?token=' . urlencode($token);
        $link = htmlspecialchars($link, ENT_QUOTES, 'UTF-8');
        $htmlBody = "
            <p>Passwort zurücksetzen:</p>
            <a href=\"{$link}\">{$link}</a>
        ";

        try {
            $this->mailer->send($email, 'Passwort zurücksetzen', $htmlBody);
        } catch (\Throwable $e) {
            error_log('Reset email failed: ' . $e->getMessage());
        }
    }
}