<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\EmailVerificationRepository;
use App\Repositories\RegistrationCodeRepository;
use App\Repositories\AccessCodeRepository;

use \PDO;
use \Exception;
use \Throwable;

class RegistrationService
{
    public function __construct(
        private PDO $pdo,
        private UserRepository $userRepository,
        private EmailVerificationRepository $emailVerificationRepository,
        private RegistrationCodeRepository $registrationCodeRepository,
        private AccessCodeRepository $accessCodeRepository,
        private UuidService $uuidService
    ) {}

    public function register(string $email, string $password, string $registrationCode, string $name): array
    {

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

        $token = bin2hex(
            random_bytes(32)
        );

        $this->pdo->beginTransaction();

        try {
            $this->userRepository->create($userId, $email, $passwordHash, $name);
            $this->emailVerificationRepository->create($userId, $token);
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

            $this->pdo->commit();

        } catch(Throwable $e) {

            if($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }

        return [
            'email' => $email,
            'token' => $token
        ];
    }
}