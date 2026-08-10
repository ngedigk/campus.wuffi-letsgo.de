<?php

namespace App\Contracts\Repositories;

interface UserCourseRepositoryInterface
{
    public function userHasCourse(string $userUuid, string $courseUuid): bool;

    public function addCourse(string $userUuid, string $courseUuid, int $accessCodeId): void;
}