<?php

namespace App\Services;

use App\Contracts\UserServiceInterface;
use App\Repositories\UserRepository;

use App\Dto\User;

use App\Exceptions\UserNotFoundException;

class UserService implements UserServiceInterface
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function get(string $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function getAll(): array
    {
        return $this->userRepository->getAll();
    }

    public function findByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }

    public function update(string $id, string $email, string $name): void
    {
        $this->userRepository->update($id, $email, $name);
    }


    public function create(string $id, string $email, string $passwordHash): void
    {
        $hasAdmin = $this->userRepository->hasAnyAdmin();
        $isAdmin = !$hasAdmin;

        $this->userRepository->create($id, $email, $passwordHash, $isAdmin);
    }

    public function setPassword(string $userUuid, string $passwordHash): void
    {
        $this->userRepository->setPassword($userUuid, $passwordHash);
    }

    public function grantAdmin(string $email): void
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            throw new UserNotFoundException('Benutzer nicht gefunden.');
        }

        $this->userRepository->setAdmin($user->id, true);
    }

    public function removeAdmin(string $email): void
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            throw new UserNotFoundException('Benutzer nicht gefunden.');
        }

        $this->userRepository->setAdmin($user->id, false);
    }

    public function verify(string $email): void
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            throw new UserNotFoundException('Benutzer nicht gefunden.');
        }

        $this->userRepository->verify($user->id);
    }
}