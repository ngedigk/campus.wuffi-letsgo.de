<?php

namespace App\Services;

use App\Contracts\Repositories\ModuleRepositoryInterface;

use App\Dto\Module;
use App\Dto\ModuleInput;

class ModuleService {
    public function __construct(
        private ModuleRepositoryInterface $moduleRepository
    ) {}

    public function create(ModuleInput $module): Module
    {
        try {
            $module = $this->moduleRepository->create($module);
        } catch (\Exception $e) {
            throw new \Exception("Modul Erstellung fehlgeschlagen: " . $e->getMessage());
        }
        return $module;        
    }

    public function update(Module $module): Module
    {
        return $this->moduleRepository->update($module);
    }

    public function delete(int $id): void
    {
        $this->moduleRepository->delete($id);
    }
}