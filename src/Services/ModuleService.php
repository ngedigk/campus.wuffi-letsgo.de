<?php

namespace App\Services;

use App\Contracts\Repositories\ModuleRepositoryInterface;

use App\Dto\Module;
use App\Dto\ModuleInput;

use App\Exceptions\CourseModuleNotFoundException;

class ModuleService {
    public function __construct(
        private ModuleRepositoryInterface $moduleRepository
    ) {}

    public function create(ModuleInput $module): Module
    {
        return $this->moduleRepository->create($module);
    }

    public function update(Module $module): Module
    {
        return $this->moduleRepository->update($module);
    }

    public function delete(int $id): void
    {
        if (!$this->moduleRepository->exists($id)) {
            throw new CourseModuleNotFoundException("Modul {$id} nicht gefunden.");
        }

        $this->moduleRepository->delete($id);
    }
}