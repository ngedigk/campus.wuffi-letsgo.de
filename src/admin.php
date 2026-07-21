<?php

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/uuid.php';

require_once __DIR__ . '/repositories/CourseRepository.php';
require_once __DIR__ . '/repositories/ModuleRepository.php';
require_once __DIR__ . '/repositories/SlideRepository.php';
require_once __DIR__ . '/repositories/AccessCodeRepository.php';
require_once __DIR__ . '/repositories/UserRepository.php';

require_once __DIR__ . '/services/SlideService.php';
require_once __DIR__ . '/services/ModuleService.php';
require_once __DIR__ . '/services/QuizService.php';
require_once __DIR__ . '/services/CourseService.php';

require_once __DIR__ . '/dto/Slide.php';

requireLogin();

if (!isAdmin($pdo)) {
    $_SESSION['admin_error'] = 'You do not have permission to manage admin features.';
    header('Location: index.php');
    exit;
}

$userRepository = new UserRepository($pdo);
$courseRepository = new CourseRepository($pdo);
$moduleRepository = new ModuleRepository($pdo);
$slideRepository = new SlideRepository($pdo);
$accessCodeRepository = new AccessCodeRepository($pdo);

$slideService = new SlideService($slideRepository);
$moduleService = new ModuleService($moduleRepository);

$quizService = new QuizService($courseRepository);
$courseService = new CourseService(
    $courseRepository,
    $moduleRepository,
    $slideRepository,
    $quizService
);

$action = $_POST['action'] ?? '';
$page = $_GET['page'] ?? 'dashboard';

// Validate page early so session error is set before rendering
switch ($page) {
    case 'dashboard':
    case 'courses':
    case 'access-codes':
    case 'users':
        break;
    default:
        $_SESSION['admin_error'] = 'Invalid page requested.';
        $page = 'dashboard';
        break;
}

// Handle POST actions regardless of page
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        switch ($action) {
            case 'grant_admin':
                $email = strtolower(trim($_POST['email'] ?? ''));

                if ($email === '') {
                    throw new Exception('Please provide an email address.');
                }

                $user = $userRepository->findByEmail($email);

                if (!$user) {
                    throw new Exception('No user with that email exists.');
                }

                $userRepository->setAdmin($user['id'], true);
                $_SESSION['admin_success'] = 'Admin permissions granted.';
                break;

            case 'create_course':
                $title = trim($_POST['title'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $prerequisiteCourseId = trim($_POST['prerequisite_course_id'] ?? '');


                if ($title === '') {
                    throw new Exception('Please provide a course title.');
                }

                $prerequisiteCourseId = $prerequisiteCourseId !== '' ? $prerequisiteCourseId : null;

                $courseId = $courseService->create(new CreateCourse(
                    generateUuid(),
                    $title,
                    $description,
                    $prerequisiteCourseId
                ));

                $_SESSION['admin_success'] = 'Course created.';
                break;

            case 'update_course':
                $courseId = trim($_POST['course_id'] ?? '');
                $title = trim($_POST['title'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $prerequisiteCourseId = trim($_POST['prerequisite_course_id'] ?? '') ?: null;
                                
                if ($title === '') {
                    throw new Exception('Please provide a valid title.');
                }
                
                $courseService->update(new CreateCourse(
                    $courseId,
                    $title,
                    $description,
                    $prerequisiteCourseId
                ));
                $_SESSION['admin_success'] = 'Course updated.';
                break;

            case 'create_access_code':
                $code = strtoupper(trim($_POST['code'] ?? ''));
                $courseId = trim($_POST['course_id'] ?? '');

                if ($code === '' || $courseId === '') {
                    throw new Exception('Please provide both an access code and a course.');
                }

                if ($accessCodeRepository->existsByCode($code)) {
                    throw new Exception('That access code already exists.');
                }

                $accessCodeRepository->create($code, $courseId);
                $_SESSION['admin_success'] = 'Access code created.';
                break;

            case 'create_module':
                $courseId = trim($_POST['course_id'] ?? '');
                $title = trim($_POST['title'] ?? '');

                if ($courseId === '' || $title === '') {
                    throw new Exception('Please provide a course and module title.');
                }

                $moduleId = $moduleService->create(new CreateModule(
                    $courseId,
                    $title,
                    count($course->modules ?? [])
                ));

                $_SESSION['admin_success'] = `Module {$moduleId} created.`;
                break;
            
            case 'update_module':
                $moduleId = (int)trim($_POST['module_id'] ?? '');
                $title = trim($_POST['title'] ?? '');
                $sortOrder = (int)trim($_POST['sort_order'] ?? 0);
                
                if ($moduleId === 0 || $title === '') {
                    throw new Exception('Please provide a valid module ID and title.');
                }
                
                $moduleService->update(new Module(
                    $moduleId,
                    $title,
                    $sortOrder,
                    null
                ));
                
                $_SESSION['admin_success'] = 'Module updated.';
                break;

            case 'create_slide':
                $moduleId = (int)trim($_POST['module_id'] ?? '');
                $title = trim($_POST['title'] ?? '');

                if ($title === '') {
                    throw new Exception('Please provide a slide title.');
                }
                $slideId = $slideService->create(new CreateSlide(
                    $moduleId,
                    $title,
                    '',
                    '',
                    0,
                    false
                ));
                $_SESSION['admin_success'] = `Slide {$slideId} created.`;
                break;

            case 'update_slide':
                $slideId = (int)trim($_POST['slide_id'] ?? '');
                $title = trim($_POST['title'] ?? '');
                $htmlContent = trim($_POST['html_content'] ?? '');
                $audioUrl = trim($_POST['audio_url'] ?? '');
                $sortOrder = (int)trim($_POST['sort_order'] ?? 0);
                $isQuiz = filter_var($_POST['is_quiz'] ?? false, FILTER_VALIDATE_BOOLEAN);
                
                if ($slideId === 0 || $title === '') {
                    throw new Exception('Please provide a valid slide ID and title.');
                }
                
                $slideService->update(new Slide(
                    $slideId,
                    $title,
                    $htmlContent,
                    $audioUrl,
                    $sortOrder,
                    $isQuiz
                ));
                $_SESSION['admin_success'] = 'Slide updated.';
                break;

            case 'delete_slide':
                $slideId = (int)trim($_POST['slide_id'] ?? '');
                $slideService->delete($slideId);
                $_SESSION['admin_success'] = 'Slide deleted.';
                break;

            case 'delete_module':
                $moduleId = (int)trim($_POST['module_id'] ?? '');
                $moduleService->delete($moduleId);
                $_SESSION['admin_success'] = 'Module deleted.';
                break;

            case 'delete_course':
                $courseId = trim($_POST['course_id'] ?? '');
                $courseService->delete($courseId);
                $_SESSION['admin_success'] = 'Course deleted.';
                break;

            default:
                throw new Exception('Unsupported admin action.');
        }
    } catch (Throwable $e) {
        $_SESSION['admin_error'] = $e->getMessage();
    }
}

$user = currentUser($pdo);
$isAdmin = isAdmin($pdo);

$adminError = $_SESSION['admin_error'] ?? null;
$adminSuccess = $_SESSION['admin_success'] ?? null;

unset($_SESSION['admin_error']);
unset($_SESSION['admin_success']);

$accessCodes = $accessCodeRepository->list();
$allCourses = $courseService->getAll();
$allUsers = $userRepository->listAll();

// Selected course for details view
$selectedCourse = null;
$selectedCourseId = filter_input(INPUT_GET, 'course_id');

if ($selectedCourseId) {
    $selectedCourse = $courseService->getWithDetails($selectedCourseId);
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

// Active page for sidebar highlighting
$activePage = $page;

// Additional CSS for admin pages
$additionalCss = ['/assets/css/admin.css'];
$additionalJs = [];

// Breadcrumb for subpages
$breadcrumb = [];
if ($page !== 'dashboard') {
    $breadcrumb = [
        ['title' => ucfirst(str_replace('-', ' ', $page))]
    ];
}

// Page-specific data
switch ($page) {
    case 'dashboard':
        $pageTitle = 'Dashboard';
        ob_start();
        require_once __DIR__ . '/views/admin/dashboard.php';
        $content = ob_get_clean();
        break;

    case 'courses':
        $pageTitle = 'Courses';

        $assetDir = __DIR__ . '/assets/images/slides/';
        $assetUrl = '/assets/images/slides/';

        $slideAssets = [];

        if (is_dir($assetDir)) {

            foreach (scandir($assetDir) as $file) {

                if ($file === '.' || $file === '..') {
                    continue;
                }

                $path = $assetDir . $file;

                if (is_file($path)) {

                    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

                    if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $slideAssets[] = [
                            'src' => $assetUrl . $file
                        ];
                    }
                }
            }
        }

        array_push($additionalCss, 'https://unpkg.com/grapesjs/dist/css/grapes.min.css');
        array_push($additionalJs, 'https://unpkg.com/grapesjs');
        array_push($additionalJs, 'https://unpkg.com/grapesjs-blocks-basic');
        if($selectedSlide) {
            array_push($additionalJs, '/assets/js/grapes-init.js');
        }
        array_push($additionalJs, '/assets/js/admin/courses.js');
        ob_start();
        extract([
            'slideAssets' => $slideAssets
        ]);
        require_once __DIR__ . '/views/admin/courses/index.php';
        $content = ob_get_clean();
        break;

    case 'access-codes':
        $pageTitle = 'Access Codes';
        $breadcrumb[] = ['title' => 'All Access Codes'];
        ob_start();
        require_once __DIR__ . '/views/admin/access-codes.php';
        $content = ob_get_clean();
        break;

    case 'users':
        $pageTitle = 'Users';
        ob_start();
        require_once __DIR__ . '/views/admin/users.php';
        $content = ob_get_clean();
        break;

    default:
        $_SESSION['admin_error'] = 'Invalid page requested.';
        $page = 'dashboard';
        $activePage = 'dashboard';
        $pageTitle = 'Dashboard';
        ob_start();
        require_once __DIR__ . '/views/admin/dashboard.php';
        $content = ob_get_clean();
        break;
}

?>
<?php require_once __DIR__ . '/views/admin/layout.php'; ?>
