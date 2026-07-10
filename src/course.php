<?php

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/uuid.php';

require_once __DIR__ . '/repositories/CourseRepository.php';
require_once __DIR__ . '/services/CourseNavigationService.php';
require_once __DIR__ . '/services/QuizService.php';
require_once __DIR__ . '/services/CourseProgressService.php';
require_once __DIR__ . '/services/CourseService.php';


requireLogin();

$userUuid = (string)($_SESSION['user_id'] ?? '');
$courseRepository = new CourseRepository($pdo);
$courseNavigationService = new CourseNavigationService();
$quizService = new QuizService($courseRepository);
$courseProgressService = new CourseProgressService($courseRepository);

$courseService = new CourseService(
    $courseRepository,
    $courseNavigationService,
    $quizService,
    $courseProgressService
);
$courseContext = $courseService->buildCourseContext($userUuid, $_GET, $_POST, $_SERVER, $_SESSION);

if (!empty($courseContext['redirectUrl'])) {
    header('Location: ' . $courseContext['redirectUrl']);
    exit;
}

extract($courseContext, EXTR_OVERWRITE);

$pageTitle = htmlspecialchars($course['title']);
$additionalCss = [
    '/assets/css/course.css'
];

ob_start();
?>
<?php require_once __DIR__ . '/views/course.php'; ?>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/template.php';
?>