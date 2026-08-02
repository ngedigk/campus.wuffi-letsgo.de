<?php

namespace App\Controller\Admin;

use App\Dto\User;
use App\Services\AdminContextService;
use App\Services\AuthService;

abstract class AdminPageController
{
    public function __construct(
        protected AdminContextService $adminContextService,
        protected AuthService $authService
    ) {}

    protected function buildContext(User $user): array
    {
        return $this->adminContextService->buildContext($user);
    }
}