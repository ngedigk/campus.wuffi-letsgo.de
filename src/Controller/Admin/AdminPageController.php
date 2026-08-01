<?php

abstract class AdminPageController
{
    public function __construct(
        protected CourseService $courseService,
        protected UserService $userService,
        protected AccessCodeRepository $accessCodeRepository,
        protected SlideService $slideService,
        protected ModuleService $moduleService,
        protected ViewRenderer $viewRenderer,
        protected AuthService $authService,
        protected CsrfService $csrfService,
        protected UuidService $uuidService,
        protected RegistrationCodeService $registrationCodeService
    ) {}

    protected function buildContext(User $user): array
    {
        $context = [
            'csrfToken' => $this->csrfService->generateToken(),
            'user' => $user,
            'isAdmin' => $this->authService->isAdmin(),
            'adminError' => $_SESSION['admin_error'] ?? null,
            'adminSuccess' => $_SESSION['admin_success'] ?? null,
            'additionalCss' => ['/assets/css/admin.css'],
            'additionalJs' => ['/assets/js/admin/general.js'],
            'allCourses' => $this->courseService->getAll(),
        ];

        unset(
            $_SESSION['admin_error'],
            $_SESSION['admin_success']
        );

        return $context;
    }
}
