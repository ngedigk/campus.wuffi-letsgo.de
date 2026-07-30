<?php

class DashboardController
{
    public function __construct(
        private DashboardService $dashboardService,
        private ViewRenderer $viewRenderer
    ) {}

    public function index(array $context): void
    {
        $courses = $this->dashboardService->getUserDashboardData($context['user']['id']);

        $viewData = [
            'pageTitle' => 'Dashboard',
            ...$context,
            'courses' => $courses,
            'additionalCss' => [...$context['additionalCss'], '/assets/css/dashboard.css']
        ];

        $this->viewRenderer->renderWithTemplate('dashboard', $viewData);
    }
}