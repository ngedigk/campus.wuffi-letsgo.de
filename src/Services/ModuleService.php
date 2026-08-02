<?php

namespace App\Services;

use App\Repositories\ModuleRepository;

use App\Dto\Module;
use App\Dto\ModuleInput;

class ModuleService {
    public function __construct(
        private ModuleRepository $moduleRepository
    ) {}

    public function create(ModuleInput $module): int
    {
        try {
            $moduleId = $this->moduleRepository->create($module);
        } catch (\Exception $e) {
            throw new \Exception("Modul Erstellung fehlgeschlagen: " . $e->getMessage());
        }
        return $moduleId;        
    }

    public function update(Module $module): void
    {
        $this->moduleRepository->update($module);
    }

    public function delete(int $id): void
    {
        $this->moduleRepository->delete($id);
    }
}