<?php

class RegistrationService
{
    public function __construct(
        private PDO $pdo,
        private UserRepository $users,
        private EmailVerificationRepository $verifications,
        private RegistrationCodeRepository $codes
    ) {}

    public function register(
        string $email,
        string $password,
        string $registrationCode
    ): array {

        if ($this->users->existsByEmail($email)) {

            throw new Exception(
                "Email already exists."
            );
        }

        $codeData = $this->codes->findByCodeForUpdate($registrationCode);

        if (!$codeData) {
            throw new Exception("Invalid registration code.");
        }

        if ($this->codes->isUsed($registrationCode)) {
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
            $this->users->create($userId, $email, $passwordHash);
            $this->verifications->create($userId, $token);
            $this->codes->markAsUsed($codeData['id'], $userId);
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
