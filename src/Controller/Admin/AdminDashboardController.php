<?php

namespace App\Controller\Admin;

use App\Services\AccessCodeService;
use App\Services\AdminContextService;
use App\Services\AuthService;
use App\Services\UserService;

use App\Helpers\ViewRenderer;

class AdminDashboardController extends AdminPageController
{
    public function __construct(
        protected AccessCodeService $accessCodeService,
        protected UserService $userService,
        protected ViewRenderer $viewRenderer,
        protected AuthService $authService,
        protected AdminContextService $adminContextService
    ) {
        parent::__construct($adminContextService, $authService);
    }

    public function render(array $context): void
    {
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