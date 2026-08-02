<?php

namespace App\Services;

use App\Dto\User;

class AdminContextService
{
    public function __construct(
        private CourseService $courseService,
        private AuthService $authService,
        private CsrfService $csrfService
    ) {}

    public function buildContext(User $user): array
    {
        $context = [
            'csrfToken' => $this->csrfService->generateToken(),
            'user' => $user,
            'isAdmin' => $this->authService->isAdmin(),
            'adminError' => $_SESSION['admin_error'] ?? null,
            'adminSuccess' => $_SESSION['admin_success'] ?? null,
            'additionalCss' => ['/assets/css/admin.css'],
            'additionalJs' => [['src' => '/assets/js/admin/general.js']],
            'allCourses' => $this->courseService->getAll(),
        ];

        unset(
            $_SESSION['admin_error'],
            $_SESSION['admin_success']
        );

        return $context;
    }
}
