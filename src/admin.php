<?php

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/uuid.php';
require_once __DIR__ . '/Database.php';

require_once __DIR__ . '/repositories/CourseRepository.php';
require_once __DIR__ . '/repositories/ModuleRepository.php';
require_once __DIR__ . '/repositories/SlideRepository.php';
require_once __DIR__ . '/repositories/AccessCodeRepository.php';
require_once __DIR__ . '/repositories/UserRepository.php';

require_once __DIR__ . '/services/SlideService.php';
require_once __DIR__ . '/services/ModuleService.php';
require_once __DIR__ . '/services/CourseService.php';
require_once __DIR__ . '/services/UserService.php';

require_once __DIR__ . '/controller/AdminController.php';

require_once __DIR__ . '/dto/Slide.php';

requireLogin();

$pdo = Database::getInstance();

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

$courseService = new CourseService(
    $courseRepository,
    $moduleRepository,
    $slideRepository
);

$userService = new UserService($userRepository);

$controller = new AdminController(
    $courseService,
    $userService,
    $accessCodeRepository,
    $slideService,
    $moduleService,
    __DIR__
);

//$action = $_POST['action'] ?? '';
$page = $_GET['page'] ?? 'dashboard';

$controller->handle($page);

exit;