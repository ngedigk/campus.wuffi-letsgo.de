<?php

namespace App\Services;

use App\Contracts\Repositories\AccessCodeRepositoryInterface;
use App\Contracts\Repositories\EmailVerificationRepositoryInterface;
use App\Contracts\Database\TransactionManagerInterface;
use App\Contracts\Mail\MailerInterface;
use App\Contracts\Repositories\RegistrationCodeRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;

use App\Exceptions\DuplicateEmailException;
use App\Exceptions\EmailSendException;
use App\Exceptions\InvalidRegistrationCodeException;
use App\Exceptions\RegistrationCodeAlreadyUsedException;
use App\Exceptions\UserNotFoundException;

use Psr\Log\LoggerInterface;

class RegistrationService
{
    public function __construct(
        private TransactionManagerInterface $transactionManager,
        private MailerInterface $mailer,
        private UserRepositoryInterface $userRepository,
        private EmailVerificationRepositoryInterface $emailVerificationRepository,
        private RegistrationCodeRepositoryInterface $registrationCodeRepository,
        private AccessCodeRepositoryInterface $accessCodeRepository,
        private UuidService $uuidService,
        private LoggerInterface $logger
    ) {}

    public function register(string $email, string $password, string $registrationCode, string $name): void
    {
        $token = bin2hex(random_bytes(32));

        $this->transactionManager->run(
            function () use ($email, $password, $registrationCode, $name, $token) {

                if ($this->userRepository->existsByEmail($email)) {
                    throw new DuplicateEmailException("E-Mail existiert bereits.");
                }

                $codeData = $this->registrationCodeRepository->findByCodeForUpdate($registrationCode);

                if (!$codeData) {
                    throw new InvalidRegistrationCodeException("Ungültiger Registrierungscode.");
                }

                if ($this->registrationCodeRepository->isUsed($registrationCode)) {
                    throw new RegistrationCodeAlreadyUsedException("Registrierungscode wurde bereits verwendet.");
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
                    $accessCodeId = $this->accessCodeRepository->createForRegistration($courseId);
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

    public function resendVerificationEmail(string $email): void
    {
        $token = bin2hex(random_bytes(32));

        $user = $this->userRepository->findByEmail($email);
        if ($user === null) {
            throw new UserNotFoundException('Es wurde kein Benutzer mit dieser E-Mail gefunden.');
        }

        $this->emailVerificationRepository->upsert($user->id, $token);

        $this->sendVerificationEmail($email, $token);
    }

    private function sendVerificationEmail(string $email, string $token): void
    {
        $link = SITE_URL . '/register/verify?token=' . urlencode($token);
        $link = htmlspecialchars($link, ENT_QUOTES, 'UTF-8');
        $htmlBody = "
            <h1>Account bestätigen</h1>
            <p>Klicken Sie auf den unteren Link:</p>
            <a href=\"{$link}\">{$link}</a>
        ";
        
        try {
            $this->mailer->send($email, 'Bestätigen Sie Ihre E-Mail', $htmlBody);
        } catch (EmailSendException $e) {
            $this->logger->error('Email verification failed', ['exception' => $e]);
        }
    }
}