<?php

namespace App\Controller\Admin;

use App\Services\AccessCodeService;
use App\Services\AdminContextService;
use App\Services\AuthService;
use App\Services\UserService;

use App\Helpers\ViewRenderer;

class AdminDashboardController
{
    public function __construct(
        protected AccessCodeService $accessCodeService,
        protected UserService $userService,
        protected ViewRenderer $viewRenderer,
        protected AuthService $authService,
        protected AdminContextService $adminContextService
    ) {}

    public function render(): void
    {
        $context = $this->adminContextService->buildContext(
            $this->authService->currentUser()
        );

        $viewData = array_merge(
            $context,
            [
                'activePage' => 'dashboard',
                'breadcrumb' => [],
                'accessCodes' => $this->accessCodeService->getAll(),
                'allUsers' => $this->userService->getAll(),
                'pageTitle' => 'Dashboard'
            ]
        );

        $this->viewRenderer->renderWithAdminTemplate('admin/dashboard', $viewData);
    }
}