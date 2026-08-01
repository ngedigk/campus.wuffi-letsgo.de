<?php

class AdminDashboardController extends AdminPageController
{
    public function render(array $context): void
    {
        $viewData = [
            ...$context,
            'activePage' => 'dashboard',
            'breadcrumb' => [],
            'accessCodes' => $this->accessCodeRepository->getAll(),
            'allUsers' => $this->userService->getAll(),
            'pageTitle' => 'Dashboard'
        ];

        $this->viewRenderer->renderWithAdminTemplate('admin/dashboard', $viewData);
    }
}
