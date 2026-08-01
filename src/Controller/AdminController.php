<?php

class AdminController extends AdminPageController
{
    public function __construct(
        CourseService $courseService,
        UserService $userService,
        AccessCodeRepository $accessCodeRepository,
        SlideService $slideService,
        ModuleService $moduleService,
        ViewRenderer $viewRenderer,
        AuthService $authService,
        CsrfService $csrfService,
        UuidService $uuidService,
        RegistrationCodeService $registrationCodeService,
        private AdminDashboardController $dashboardController,
        private AdminCoursesController $coursesController,
        private AdminAccessCodesController $accessCodesController,
        private AdminUsersController $usersController,
        private AdminRegistrationCodesController $registrationCodesController
    ) {
        parent::__construct(
            $courseService,
            $userService,
            $accessCodeRepository,
            $slideService,
            $moduleService,
            $viewRenderer,
            $authService,
            $csrfService,
            $uuidService,
            $registrationCodeService
        );
    }

    public function handle(string $page): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost();
        }

        $user = $this->authService->currentUser();
        $page = $this->validatePage($page);
        $context = $this->buildContext($user);

        switch ($page) {
            case 'dashboard':
                $this->dashboardController->render($context);
                break;
            case 'courses':
                $this->coursesController->render($context);
                break;
            case 'access-codes':
                $this->accessCodesController->render($context);
                break;
            case 'users':
                $this->usersController->render($context);
                break;
            case 'registration-codes':
                $this->registrationCodesController->render($context);
                break;
            default:
                $this->dashboardController->render($context);
                break;
        }
    }

    private function handlePost(): void
    {
        $action = $_POST['action'] ?? '';

        try {
            switch ($action) {
                case 'grant_admin':
                case 'revoke_admin':
                case 'manually_verify':
                    $this->usersController->handlePost($action);
                    break;
                case 'create_course':
                case 'update_course':
                case 'create_module':
                case 'update_module':
                case 'create_slide':
                case 'update_slide':
                case 'delete_slide':
                case 'delete_module':
                case 'delete_course':
                case 'upload_image':
                    $this->coursesController->handlePost($action);
                    break;
                case 'create_access_code':
                case 'update_access_code':
                case 'delete_access_code':
                    $this->accessCodesController->handlePost($action);
                    break;
                case 'create_registration_code':
                case 'update_courses_to_registration_code_assignment':
                case 'delete_registration_code':
                    $this->registrationCodesController->handlePost($action);
                    break;
                default:
                    throw new Exception('Unsupported admin action.');
            }
        } catch (Throwable $e) {
            $_SESSION['admin_error'] = $e->getMessage();
        }
    }

    private function validatePage(string $page): string
    {
        $validPages = ['dashboard', 'courses', 'access-codes', 'users', 'registration-codes'];
        return in_array($page, $validPages, true) ? $page : 'dashboard';
    }
}

