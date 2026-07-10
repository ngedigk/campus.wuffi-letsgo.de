<?php

class RegistrationService
{
    public function __construct(
        private PDO $pdo,
        private UserRepository $users,
        private EmailVerificationRepository $verifications
    ) {}

    public function register(
        string $email,
        string $password
    ): array {

        if ($this->users->existsByEmail($email)) {

            throw new Exception(
                "Email already exists."
            );
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

            $this->users->create(
                $userId,
                $email,
                $passwordHash
            );

            $this->verifications->create(
                $userId,
                $token
            );

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