<?php

namespace App\Contracts\Repositories;

use App\Dto\Course;
use App\Dto\CourseInput;

interface CourseRepositoryInterface
{
    public function get(string $courseUuid): ?Course;

    public function getCourseForUser(string $userUuid, string $courseUuid): ?Course;

    public function getAll(): array;

    public function getAllForUser(string $userUuid): array;

    public function exists(string $uuid): bool;

    public function create(string $courseUuid, CourseInput $course): Course;

    public function update(Course $course): Course;

    public function delete(string $uuid): void;
}
