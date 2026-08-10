<?php

namespace App\Contracts\Repositories;

interface AccessCodeRepositoryInterface
{
    public function findByCodeForUpdate(string $code): ?array;

    public function existsByCode(string $code): bool;

    public function create(string $code, string $courseUuid): void;

    public function createForRegistration(string $courseUuid): int;

    public function update(int $id, string $code, string $courseUuid): void;

    public function getAll(): array;

    public function delete(int $accessCodeId): void;
}
