<?php

namespace App\Contracts\Repositories;

interface RegistrationCodeRepositoryInterface
{
    public function findByCodeForUpdate(string $code): ?array;

    public function isUsed(string $code): bool;

    public function markAsUsed(int $id, string $userId): void;

    public function create(string $code, array $courseIds = []): void;

    public function update(int $registrationCodeId, string $code): void;

    public function getCourseIds(int $registrationCodeId): array;

    public function addCourses(int $registrationCodeId, array $courseIds): void;

    public function removeCourses(int $registrationCodeId, array $courseIds): void;

    public function removeAllCourses(int $registrationCodeId): void;

    public function getAll(): array;

    public function delete(int $id): void;
}