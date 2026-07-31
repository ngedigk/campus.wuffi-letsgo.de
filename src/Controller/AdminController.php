<?php

class AdminController
{
    public function __construct(
        private CourseService $courseService,
        private UserService $userService,
        private AccessCodeRepository $accessCodeRepository,
        private SlideService $slideService,
        private ModuleService $moduleService,
        private ViewRenderer $viewRenderer,
        private AuthService $authService,
        private CsrfService $csrfService,
        private UuidService $uuidService
    ) {}

    private function buildContext(User $user): array
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
                $this->renderDashboard($context);
                break;
            case 'courses':
                $this->renderCourses($context);
                break;
            case 'access-codes':
                $this->renderAccessCodes($context);
                break;
            case 'users':
                $this->renderUsers($context);
                break;
            default:
                $this->renderDashboard($context);
                break;
        }
    }

    private function handlePost(): void
    {
        $action = $_POST['action'] ?? '';

        try {
            switch ($action) {
                case 'grant_admin':
                    $this->handleGrantAdmin();
                    break;
                case 'revoke_admin':
                    $this->handleRevokeAdmin();
                    break;
                case 'manually_verify':
                    $this->handleManuallyVerify();
                    break;
                case 'create_course':
                    $this->handleCreateCourse();
                    break;
                case 'update_course':
                    $this->handleUpdateCourse();
                    break;
                case 'create_access_code':
                    $this->handleCreateAccessCode();
                    break;
                case 'delete_access_code':
                    $this->handleDeleteAccessCode();
                    break;
                case 'create_module':
                    $this->handleCreateModule();
                    break;
                case 'update_module':
                    $this->handleUpdateModule();
                    break;
                case 'create_slide':
                    $this->handleCreateSlide();
                    break;
                case 'update_slide':
                    $this->handleUpdateSlide();
                    break;
                case 'delete_slide':
                    $this->handleDeleteSlide();
                    break;
                case 'delete_module':
                    $this->handleDeleteModule();
                    break;
                case 'delete_course':
                    $this->handleDeleteCourse();
                    break;
                case 'upload_image':
                    $this->handleUploadImage();
                    break;
                default:
                    throw new Exception('Unsupported admin action.');
            }
        } catch (Throwable $e) {
            $_SESSION['admin_error'] = $e->getMessage();
        }
    }

    private function handleGrantAdmin(): void
    {
        $email = strtolower(trim($_POST['email'] ?? ''));
        if ($email === '') throw new Exception('Please provide an email address.');

        $this->userService->grantAdmin($email);
        $_SESSION['admin_success'] = 'Admin permissions granted.';
    }

    private function handleRevokeAdmin(): void
    {
        $email = strtolower(trim($_POST['email'] ?? ''));
        if ($email === '') throw new Exception('Please provide an email address.');

        $pdo = Database::getInstance();
        if ($email === strtolower($this->authService->currentUser()->email)) {
            throw new Exception("Can't remove your own admin.");
        }
        $this->userService->removeAdmin($email);
        $_SESSION['admin_success'] = 'Admin permissions removed.';
    }

    private function handleManuallyVerify(): void
    {
        $email = trim($_POST['email'] ?? '');
        if ($email === '') throw new Exception('Please provide an email address.');
        $this->userService->verify($email);
        $_SESSION['admin_success'] = 'User manually verified.';
    }

    private function handleCreateCourse(): void
    {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $prerequisiteCourseId = trim($_POST['prerequisite_course_id'] ?? '');
        $sortOrder = (int)trim($_POST['sort_order'] ?? 0);

        if ($title === '') throw new Exception('Please provide a course title.');
        $prerequisiteCourseId = $prerequisiteCourseId !== '' ? $prerequisiteCourseId : null;

        $this->courseService->create(new CourseInput(
            uuid: $this->uuidService->generate(),
            title: $title,
            description: $description,
            prerequisiteCourseId: $prerequisiteCourseId,
            sortOrder: $sortOrder
        ));
        $_SESSION['admin_success'] = 'Course created.';
    }

    private function handleUpdateCourse(): void
    {
        $courseId = trim($_POST['course_id'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $prerequisiteCourseId = trim($_POST['prerequisite_course_id'] ?? '') ?: null;
        $sortOrder = (int)trim($_POST['sort_order'] ?? 0);

        if ($title === '') throw new Exception('Please provide a valid title.');

        $this->courseService->update(new CourseInput(
            uuid: $courseId,
            title: $title,
            description: $description,
            prerequisiteCourseId: $prerequisiteCourseId,
            sortOrder: $sortOrder
        ));
        $_SESSION['admin_success'] = 'Course updated.';
    }

    private function handleCreateAccessCode(): void
    {
        $code = trim($_POST['code'] ?? '');
        $courseId = trim($_POST['course_id'] ?? '');

        if ($code === '' || $courseId === '') throw new Exception('Please provide both an access code and a course.');
        if ($this->accessCodeRepository->existsByCode($code)) throw new Exception('That access code already exists.');

        $this->accessCodeRepository->create($code, $courseId);
        $_SESSION['admin_success'] = 'Access code created.';
    }

    private function handleDeleteAccessCode(): void
    {
        $accessCodeId = trim($_POST['access_code_id'] ?? '');
        $this->accessCodeRepository->delete($accessCodeId);
        $_SESSION['admin_success'] = 'Access code deleted and removed user access.';
    }

    private function handleCreateModule(): void
    {
        $courseId = trim($_POST['course_id'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $sortOrder = (int)trim($_POST['sort_order'] ?? 0);

        if ($courseId === '' || $title === '') throw new Exception('Please provide a course and module title.');

        $moduleId = $this->moduleService->create(new ModuleInput(
            courseId: $courseId,
            title: $title,
            sortOrder: $sortOrder
        ));
        $_SESSION['admin_success'] = "Module $moduleId created.";
    }

    private function handleUpdateModule(): void
    {
        $moduleId = (int)trim($_POST['module_id'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $sortOrder = (int)trim($_POST['sort_order'] ?? 0);

        if ($moduleId === 0 || $title === '') throw new Exception('Please provide a valid module ID and title.');

        $this->moduleService->update(new Module(
            id: $moduleId,
            title: $title,
            sortOrder: $sortOrder,
            slides: null
        ));
        $_SESSION['admin_success'] = 'Module updated.';
    }

    private function handleCreateSlide(): void
    {
        $moduleId = (int)trim($_POST['module_id'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $audioUrl = trim($_POST['audio_url'] ?? '');
        $sortOrder = (int)trim($_POST['sort_order'] ?? 0);

        if ($title === '') throw new Exception('Please provide a slide title.');

        $slideId = $this->slideService->create(new SlideInput(
            moduleId: $moduleId,
            title: $title,
            audioUrl: $audioUrl,
            htmlContent: '',
            sortOrder: $sortOrder,
            isQuiz: false
        ));
        $_SESSION['admin_success'] = "Slide $slideId created.";
    }

    private function handleUpdateSlide(): void
    {
        $slideId = (int)trim($_POST['slide_id'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $htmlContent = trim($_POST['html_content'] ?? '');
        $audioUrl = trim($_POST['audio_url'] ?? '');
        $sortOrder = (int)trim($_POST['sort_order'] ?? 0);
        $isQuiz = filter_var($_POST['is_quiz'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if ($slideId === 0 || $title === '') throw new Exception('Please provide a valid slide ID and title.');

        $this->slideService->update(new Slide(
            id: $slideId,
            title: $title,
            htmlContent: $htmlContent,
            audioUrl: $audioUrl,
            sortOrder: $sortOrder,
            isQuiz: $isQuiz
        ));
        $_SESSION['admin_success'] = 'Slide updated.';
    }

    private function handleDeleteSlide(): void
    {
        $slideId = (int)trim($_POST['slide_id'] ?? '');
        $this->slideService->delete($slideId);
        $_SESSION['admin_success'] = 'Slide deleted.';
    }

    private function handleDeleteModule(): void
    {
        $moduleId = (int)trim($_POST['module_id'] ?? '');
        $this->moduleService->delete($moduleId);
        $_SESSION['admin_success'] = 'Module deleted.';
    }

    private function handleDeleteCourse(): void
    {
        $courseId = trim($_POST['course_id'] ?? '');
        $this->courseService->delete($courseId);
        $_SESSION['admin_success'] = 'Course deleted.';
    }
    
    private function handleUploadImage(): void
    {
        if (!$this->authService->isAdmin()) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        try {
            $this->csrfService->validateToken($_POST['csrf_token']);
        } catch (\Exception $e) {
            http_response_code(403);
            echo json_encode(['error' => 'CSRF token invalid']);
            exit;
        }

        if (!isset($_FILES['files'])) {
            http_response_code(400);
            echo json_encode(['error' => 'No file uploaded']);
            exit;
        }

        $uploadDir = __DIR__ . '/../assets/images/slides/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $response = [];

        foreach ($_FILES['files']['name'] as $index => $originalName) {
            $tmpName = $_FILES['files']['tmp_name'][$index];

            $filename = uniqid() . '-' . basename($originalName);

            $target = $uploadDir . $filename;

            if (move_uploaded_file($tmpName, $target)) {
                $response[] = [
                    'src' => '/assets/images/slides/' . $filename
                ];
            }
        }

        echo json_encode([
            'data' => $response
        ]);
        exit;
    }

    private function validatePage(string $page): string
    {
        $validPages = ['dashboard', 'courses', 'access-codes', 'users'];
        return in_array($page, $validPages, true) ? $page : 'dashboard';
    }

    private function renderDashboard(array $context): void
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

    private function renderCourses(array $context): void
    {
        $selectedCourse = null;
        $selectedCourseId = filter_input(INPUT_GET, 'course_id');
        if ($selectedCourseId) {
            $selectedCourse = $this->courseService->getWithDetails($selectedCourseId);
        }

        $selectedModule = null;
        $selectedModuleId = filter_input(INPUT_GET, 'module_id', FILTER_VALIDATE_INT);
        if ($selectedCourse && $selectedModuleId) {
            foreach ($selectedCourse->modules as $module) {
                if ($module->id === $selectedModuleId) {
                    $selectedModule = $module;
                    break;
                }
            }
        }

        $selectedSlide = null;
        $selectedSlideId = filter_input(INPUT_GET, 'slide_id', FILTER_VALIDATE_INT);
        if ($selectedModule && $selectedSlideId) {
            foreach ($selectedModule->slides as $slide) {
                if ($slide->id === $selectedSlideId) {
                    $selectedSlide = $slide;
                    break;
                }
            }
        }

        $assetDir = __DIR__ . '/../assets/images/slides/';
        $assetUrl = '/assets/images/slides/';
        $slideAssets = [];

        if (is_dir($assetDir)) {
            foreach (scandir($assetDir) as $file) {
                if ($file === '.' || $file === '..') continue;
                $path = $assetDir . $file;
                if (is_file($path)) {
                    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $slideAssets[] = ['src' => $assetUrl . $file];
                    }
                }
            }
        }

        $context['additionalJs'][] = 'https://unpkg.com/grapesjs';
        $context['additionalJs'][] = 'https://unpkg.com/grapesjs-blocks-basic';

        if ($selectedSlide) {
            $context['additionalJs'][] = '/assets/js/grapes-init.js';
        }
        $context['additionalJs'][] = '/assets/js/admin/courses.js';

        $context['additionalCss'][] = 'https://unpkg.com/grapesjs/dist/css/grapes.min.css';

        $viewData = [
            ...$context,
            'activePage' => 'courses',
            'breadcrumb' => [
                [
                    'url' => '',
                    'title'=> 'Courses'
                ],
            ],
            'selectedCourse' => $selectedCourse,
            'selectedCourseId' => $selectedCourseId,
            'selectedModule' => $selectedModule,
            'selectedModuleId' => $selectedModuleId,
            'selectedSlide' => $selectedSlide,
            'selectedSlideId' => $selectedSlideId,
            'slideAssets' => $slideAssets,
            'pageTitle' => 'Courses'
        ];
        
        $this->viewRenderer->renderWithAdminTemplate('admin/courses/index', $viewData);
    }

    private function renderAccessCodes(array $context): void
    {
        $context['additionalJs'][] = '/assets/js/admin/access-codes.js';

        $viewData = [
            ...$context,
            'activePage' => 'access-codes',
            'breadcrumb' => [
                [
                    'url' => '',
                    'title'=> 'Access Codes'
                ],
            ],
            'accessCodes' => $this->accessCodeRepository->getAll(),
            'pageTitle' => 'Access Codes'
        ];

        $this->viewRenderer->renderWithAdminTemplate('admin/access-codes', $viewData);
    }

    private function renderUsers(array $context): void
    {
        $viewData = [
            ...$context,
            'activePage' => 'users',
            'breadcrumb' => [
                [
                    'url' => '',
                    'title'=> 'Users'
                ],
            ],
            'allUsers' => $this->userService->getAll(),
            'pageTitle' => 'Users'
        ];

        $this->viewRenderer->renderWithAdminTemplate('admin/users', $viewData);
    }
}