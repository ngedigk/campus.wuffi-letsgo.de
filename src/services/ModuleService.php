<?php

require_once __DIR__ . '/../dto/Module.php';

class ModuleService {
    public function __construct(
        private ModuleRepository $moduleRepository
    ) {}

    public function create(
        CreateModule $module
    ): int {
        try {
            $moduleId = $this->moduleRepository->create($module);
        } catch (\Exception $e) {
            throw new \Exception("Failed to create module: " . $e->getMessage());
        }
        return $moduleId;        
    }

    public function update(
        Module $module
    ): void {
        $this->moduleRepository->update($module);
    }

    public function delete(
        int $id
    ): void {
        $this->moduleRepository->delete($id);
    }
}