<?php

namespace App\Services;

use App\Contracts\TransactionManager;
use App\Contracts\Mailer;

use App\Repositories\UserRepository;
use App\Repositories\EmailVerificationRepository;
use App\Repositories\RegistrationCodeRepository;
use App\Repositories\AccessCodeRepository;

use \Exception;
use \Throwable;

class RegistrationService
{
    public function __construct(
        private TransactionManager $transactionManager,
        private Mailer $mailer,
        private UserRepository $userRepository,
        private EmailVerificationRepository $emailVerificationRepository,
        private RegistrationCodeRepository $registrationCodeRepository,
        private AccessCodeRepository $accessCodeRepository,
        private UuidService $uuidService
    ) {}

    public function register(string $email, string $password, string $registrationCode, string $name): void
    {

        $token = bin2hex(random_bytes(32));

        $this->transactionManager->run(
            function () use ( $email, $password, $registrationCode, $name, $token ) {

                if ($this->userRepository->existsByEmail($email)) {
                    throw new Exception("E-Mail existiert bereits.");
                }

                $codeData = $this->registrationCodeRepository->findByCodeForUpdate($registrationCode);

                if (!$codeData) {
                    throw new Exception("Ungültiger Registrierungscode.");
                }

                if ($this->registrationCodeRepository->isUsed($registrationCode)) {
                    throw new Exception("Registrierungscode wurde bereits verwendet.");
                }

                $userId = $this->uuidService->generate();

                $passwordHash = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );
                
                $this->userRepository->create($userId, $email, $passwordHash, $name);
                $this->emailVerificationRepository->upsert($userId, $token);
                $this->registrationCodeRepository->markAsUsed($codeData['id'], $userId);

                $courseIds = $this->registrationCodeRepository->getCourseIds($codeData['id']);
                $courseAccessPairs = [];
                foreach ($courseIds as $courseId) {
                    $accessCodeId = $this->accessCodeRepository->createForRegistration($codeData['id'], $userId, $courseId);
                    $courseAccessPairs[] = [
                        'course_id' => $courseId,
                        'access_code_id' => $accessCodeId
                    ];
                }

                if (!empty($courseAccessPairs)) {
                    $this->userRepository->enrollInCourses($userId, $courseAccessPairs);
                }

            }
        );
        
        $this->sendVerificationEmail($email, $token);
    }

    private function sendVerificationEmail(string $email, string $token): void
    {
        $link = SITE_URL . '/register.php?action=verify&token=' . urlencode($token);
        $link = htmlspecialchars($link, ENT_QUOTES, 'UTF-8');
        $htmlBody = "
            <h1>Account bestätigen</h1>
            <p>Klicken Sie auf den unteren Link:</p>
            <a href=\"{$link}\">{$link}</a>
        ";
        
        try {
            $this->mailer->send($email, 'Bestätigen Sie Ihre E-Mail', $htmlBody);
        } catch (Throwable $e) {
            error_log('Email verification failed: ' . $e->getMessage());
        }
    }
}