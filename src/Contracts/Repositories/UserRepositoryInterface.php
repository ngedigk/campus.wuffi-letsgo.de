<?php

namespace App\Contracts\Repositories;

use App\Dto\User;

interface UserRepositoryInterface
{
    public function findById(string $id): ?User;

    public function getAll(): array;

    public function existsByEmail(string $email): bool;

    public function findByEmail(string $email): ?User;

    public function setPassword(string $id, string $passwordHash): void;

    public function setAdmin(string $id, bool $isAdmin): void;

    public function hasAnyAdmin(): bool;

    public function verify(string $id): void;

    public function update(string $id, string $email, string $name): void;

    public function create(string $id, string $email, string $passwordHash, string $name, bool $isAdmin = false): void;

    public function enrollInCourses(string $userId, array $courseAccessPairs): void;
}