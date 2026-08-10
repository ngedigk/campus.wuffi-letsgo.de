<?php

namespace App\Contracts;

use App\Dto\User;

interface UserServiceInterface
{
    public function get(string $id): ?User;

    public function getAll(): array;

    public function findByEmail(string $email): ?User;

    public function update(string $id, string $email, string $name): void;

    public function create(string $id, string $email, string $passwordHash): void;

    public function setPassword(string $userUuid, string $passwordHash): void;

    public function grantAdmin(string $email): void;

    public function removeAdmin(string $email): void;

    public function verify(string $email): void;
}
