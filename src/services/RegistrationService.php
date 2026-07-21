<?php

class RegistrationService
{
    public function __construct(
        private PDO $pdo,
        private UserRepository $userRepository,
        private EmailVerificationRepository $emailVerificationRepository,
        private RegistrationCodeRepository $registrationCodeRepository,
        private AccessCodeRepository $accessCodeRepository
    ) {}

    public function register(
        string $email,
        string $password,
        string $registrationCode
    ): array {

        if ($this->userRepository->existsByEmail($email)) {

            throw new Exception(
                "Email already exists."
            );
        }

        $codeData = $this->registrationCodeRepository->findByCodeForUpdate($registrationCode);

        if (!$codeData) {
            throw new Exception("Invalid registration code.");
        }

        if ($this->registrationCodeRepository->isUsed($registrationCode)) {
            throw new Exception("Registration code has already been used.");
        }

        $userId = generateUuid();

        $passwordHash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $token = bin2hex(
            random_bytes(32)
        );

        $this->pdo->beginTransaction();

        try {
            $this->userRepository->create($userId, $email, $passwordHash);
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
