<?php

namespace App\Contracts\Repositories;

use App\Dto\Module;
use App\Dto\ModuleInput;

interface ModuleRepositoryInterface
{
    public function get(int $moduleId): ?Module;

    public function getByCourseId(string $courseId): array;

    public function exists(int $moduleId): bool;

    public function create(ModuleInput $module): Module;

    public function update(Module $module): Module;

    public function delete(int $moduleId): void;
}