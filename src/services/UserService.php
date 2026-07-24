<?php

class UserService
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function get(string $id): ?array
    {
        return $this->userRepository->findById($id);
    }

    public function getAll(): array
    {
        return $this->userRepository->getAll();
    }

    public function create(string $id, string $email, string $passwordHash): void
    {
        $hasAdmin = $this->userRepository->hasAnyAdmin();
        $isAdmin = !$hasAdmin;

        $this->userRepository->create($id, $email, $passwordHash, $isAdmin);
    }

    public function grantAdmin(string $email): void
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            throw new \Exception('User not found.');
        }

        $this->userRepository->setAdmin($user['id'], true);
    }

    public function removeAdmin(string $email): void
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            throw new \Exception('User not found.');
        }

        $this->userRepository->setAdmin($user['id'], false);
    }

    public function verify(string $email): void
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            throw new \Exception('User not found.');
        }

        $this->userRepository->verify($user['id']);
    }
}