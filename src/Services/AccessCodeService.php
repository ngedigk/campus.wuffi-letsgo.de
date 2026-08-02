<?php

namespace App\Services;

use App\Repositories\AccessCodeRepository;

class AccessCodeService
{
    public function __construct(
        private AccessCodeRepository $accessCodeRepository
    ) {}

    public function getAll(): array
    {
        return $this->accessCodeRepository->getAll();
    }

    public function existsByCode(string $code): bool
    {
        return $this->accessCodeRepository->existsByCode($code);
    }

    public function create(string $code, string $courseId): void
    {
        $this->accessCodeRepository->create($code, $courseId);
    }

    public function update(int $accessCodeId, string $code, string $courseId): void
    {
        $this->accessCodeRepository->update($accessCodeId, $code, $courseId);
    }

    public function delete(int $accessCodeId): void
    {
        $this->accessCodeRepository->delete($accessCodeId);
    }


}