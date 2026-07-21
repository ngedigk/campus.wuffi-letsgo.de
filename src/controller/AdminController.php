<?php

require_once __DIR__ . '/../dto/Slide.php';
require_once __DIR__ . '/../dto/Course.php';
require_once __DIR__ . '/../dto/Module.php';
require_once __DIR__ . '/../repositories/SlideRepository.php';
require_once __DIR__ . '/../repositories/ModuleRepository.php';


class AdminController
{
    private CourseService $courseService;
    private UserService $userService;
    private AccessCodeRepository $accessCodeRepository;
    private SlideService $slideService;
    private ModuleService $moduleService;
    private string $basePath;
    private string $baseCss;

    public function __construct(
        CourseService $courseService,
        UserService $userService,
        AccessCodeRepository $accessCodeRepository,
        SlideService $slideService,
        ModuleService $moduleService,
        string $basePath
    ) {
        $this->courseService = $courseService;
        $this->userService = $userService;
        $this->accessCodeRepository = $accessCodeRepository;
        $this->slideService = $slideService;
        $this->moduleService = $moduleService;
        $this->basePath = $basePath;
        $this->baseCss = '/assets/css/admin.css';
    }

    public function handle(string $page): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost();
        }

        $page = $this->validatePage($page);
        $allCourses = $this->courseService->getAll();

        $activePage = $page;
        $user = currentUser($this->getPdo());
        $isAdmin = isAdmin($this->getPdo());

        $adminError = $_SESSION['admin_error'] ?? null;
        $adminSuccess = $_SESSION['admin_success'] ?? null;
        
        unset($_SESSION['admin_error']);
        unset($_SESSION['admin_success']);

        $breadcrumb = [];
        if ($page !== 'dashboard') {
            $breadcrumb[] = ['title' => ucfirst(str_replace('-', ' ', $page))];
        }

        $context = [
            'user' => $user,
            'isAdmin' => $isAdmin,
            'activePage' => $activePage,
            'adminError' => $adminError,
            'adminSuccess' => $adminSuccess,
            'breadcrumb' => $breadcrumb,
            'additionalCss' => [$this->baseCss],
            'additionalJs' => [],
            'allCourses' => $allCourses
        ];

        ob_start();
        switch ($page) {
            case 'dashboard':
                $this->renderDashboard($context);
                break;
            case 'courses':
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
                $context['selectedCourse'] = $selectedCourse;
                $context['selectedCourseId'] = $selectedCourseId;
                $context['selectedModule'] = $selectedModule;
                $context['selectedModuleId'] = $selectedModuleId;
                $context['selectedSlide'] = $selectedSlide;
                $context['selectedSlideId'] = $selectedSlideId;

                $context['additionalCss'][] = 'https://unpkg.com/grapesjs/dist/css/grapes.min.css';
                $context['additionalJs'][] = 'https://unpkg.com/grapesjs';
                $context['additionalJs'][] = 'https://unpkg.com/grapesjs-blocks-basic';

                if ($selectedSlide) {
                    $context['additionalJs'][] = '/assets/js/grapes-init.js';
                }

                $context['additionalJs'][] = '/assets/js/admin/courses.js';
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
        $content = ob_get_clean();

        extract($context);

        require_once $this->basePath . '/views/admin/layout.php';
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
        if ($email === strtolower(currentUser($this->getPdo())['email'])) {
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

        if ($title === '') throw new Exception('Please provide a course title.');
        $prerequisiteCourseId = $prerequisiteCourseId !== '' ? $prerequisiteCourseId : null;

        $this->courseService->create(new CreateCourse(
            generateUuid(),
            $title,
            $description,
            $prerequisiteCourseId
        ));
        $_SESSION['admin_success'] = 'Course created.';
    }

    private function handleUpdateCourse(): void
    {
        $courseId = trim($_POST['course_id'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $prerequisiteCourseId = trim($_POST['prerequisite_course_id'] ?? '') ?: null;

        if ($title === '') throw new Exception('Please provide a valid title.');

        $this->courseService->update(new CreateCourse(
            $courseId,
            $title,
            $description,
            $prerequisiteCourseId
        ));
        $_SESSION['admin_success'] = 'Course updated.';
    }

    private function handleCreateAccessCode(): void
    {
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $courseId = trim($_POST['course_id'] ?? '');

        if ($code === '' || $courseId === '') throw new Exception('Please provide both an access code and a course.');
        if ($this->accessCodeRepository->existsByCode($code)) throw new Exception('That access code already exists.');

        $this->accessCodeRepository->create($code, $courseId);
        $_SESSION['admin_success'] = 'Access code created.';
    }

    private function handleCreateModule(): void
    {
        $courseId = trim($_POST['course_id'] ?? '');
        $title = trim($_POST['title'] ?? '');

        if ($courseId === '' || $title === '') throw new Exception('Please provide a course and module title.');

        $moduleId = $this->moduleService->create(new CreateModule($courseId, $title, 0));
        $_SESSION['admin_success'] = "Module $moduleId created.";
    }

    private function handleUpdateModule(): void
    {
        $moduleId = (int)trim($_POST['module_id'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $sortOrder = (int)trim($_POST['sort_order'] ?? 0);

        if ($moduleId === 0 || $title === '') throw new Exception('Please provide a valid module ID and title.');

        $this->moduleService->update(new Module($moduleId, $title, $sortOrder, null));
        $_SESSION['admin_success'] = 'Module updated.';
    }

    private function handleCreateSlide(): void
    {
        $moduleId = (int)trim($_POST['module_id'] ?? '');
        $title = trim($_POST['title'] ?? '');

        if ($title === '') throw new Exception('Please provide a slide title.');

        $slideId = $this->slideService->create(new CreateSlide($moduleId, $title, '', '', 0, false));
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

        $this->slideService->update(new Slide($slideId, $title, $htmlContent, $audioUrl, $sortOrder, $isQuiz));
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

    private function validatePage(string $page): string
    {
        $validPages = ['dashboard', 'courses', 'access-codes', 'users'];
        return in_array($page, $validPages, true) ? $page : 'dashboard';
    }

    private function getPdo(): PDO
    {
        return $GLOBALS['pdo'] ?? new PDO('sqlite::memory:');
    }

    private function renderDashboard(array $context): void
    {
        $pageTitle = 'Dashboard';
        extract([
            ...$context,
            'pageTitle' => $pageTitle
        ]);
        $accessCodes = $this->accessCodeRepository->list();
        $allUsers = $this->userService->getAll();
        require_once $this->basePath . '/views/admin/dashboard.php';
    }

    private function renderCourses(array $context): void
    {
        $pageTitle = 'Courses';

        $assetDir = $this->basePath . '/assets/images/slides/';
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

        extract([
            ...$context,
            'pageTitle' => $pageTitle,
            'slideAssets' => $slideAssets
        ]);

        require_once $this->basePath . '/views/admin/courses/index.php';
    }

    private function renderAccessCodes(array $context): void
    {
        $pageTitle = 'Access Codes';
        $accessCodes = $this->accessCodeRepository->list();

        extract([
            'pageTitle' => $pageTitle,
            'accessCodes' => $accessCodes,
        ]);

        require_once $this->basePath . '/views/admin/access-codes.php';
    }

    private function renderUsers(array $context): void
    {
        $pageTitle = 'Users';
        $allUsers = $this->userService->getAll();

        extract([
            'pageTitle' => $pageTitle,
            'allUsers' => $allUsers,
        ]);

        require_once $this->basePath . '/views/admin/users.php';
    }
}