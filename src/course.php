<?php

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/uuid.php';

require_once __DIR__ . '/repositories/CourseRepository.php';
require_once __DIR__ . '/repositories/ModuleRepository.php';
require_once __DIR__ . '/repositories/SlideRepository.php';
require_once __DIR__ . '/repositories/ProgressRepository.php';
require_once __DIR__ . '/services/QuizService.php';
require_once __DIR__ . '/services/CourseService.php';
require_once __DIR__ . '/services/ProgressService.php';


requireLogin();

$userUuid = (string)($_SESSION['user_id'] ?? '');
$courseRepository = new CourseRepository($pdo);
$moduleRepository = new ModuleRepository($pdo);
$slideRepository = new SlideRepository($pdo);
$progressRepository = new ProgressRepository($pdo);

$quizService = new QuizService($slideRepository);

$progressService = new ProgressService($progressRepository);

$courseService = new CourseService(
    $courseRepository,
    $moduleRepository,
    $slideRepository
);

$errors = [];
$visitedSlideIds = [];
$allowedSlideIds = [];

$courseUuid = trim(($_GET['id'] ?? ''));
$moduleId = (int)($_GET['module'] ?? 0);
$slideIndex = (int)($_GET['slide'] ?? 0);

try {
    $course = $courseService->getWithDetailsForUser($userUuid, $courseUuid);

    // Resolve current module and slides using index instead of DB ID
    $modules = array_values($course->modules);
    $currentModule = $modules[$moduleId] ?? null;
    
    // Fallback to the first module if the index is out of bounds
    if (!$currentModule && !empty($modules)) {
        $currentModule = $modules[0];
        $moduleId = 0;
    }

    $slidesForModule = $currentModule ? $currentModule->slides : [];
    $currentSlide = $slidesForModule[$slideIndex] ?? null;
    $currentSlideIndex = $slideIndex;

    // Quiz handling
    $quizResult = $quizService->handle($currentSlide);
    $quizAttempted = $quizResult->attempted;
    $quizPassed = $quizResult->passed;
    $currentSlideQuestions = $quizResult->currentSlideQuestions;
    $choicesByQuestion = $quizResult->choicesByQuestion;
    $userAnswersByQuestion = $quizResult->answers;
    $submittedAnswers = $quizResult->submittedAnswers;

    $visitedSlideIds = $progressService->getVisitedSlideIds($userUuid, $courseUuid);

    $allSlidesGlobal = [];
    foreach ($course->modules as $moduleIndex => $module) {
        foreach ($module->slides as $sIdx => $slide) {
            $allSlidesGlobal[] = [
                'module' => $module,
                'moduleIndex' => $moduleIndex,
                'slide' => $slide,
                'slideIndex' => $sIdx
            ];
        }
    }

    // Find the index of the furthest visited slide
    $maxVisitedIndex = -1;
    foreach ($allSlidesGlobal as $idx => $item) {
        if (in_array($item['slide']->id, $visitedSlideIds)) {
            $maxVisitedIndex = $idx;
        }
    }

    // Determine the next allowed slide index (next to the furthest visited)
    $nextAllowedIndex = $maxVisitedIndex + 1;
    
    // If the user has visited all slides, they are at the end (allow access to the last one)
    if ($nextAllowedIndex >= count($allSlidesGlobal)) {
        $nextAllowedIndex = count($allSlidesGlobal) - 1;
    }

    // Find the global index of the currently requested slide
    $currentGlobalIndex = -1;
    foreach ($allSlidesGlobal as $gIdx => $item) {
        if ($item['moduleIndex'] == $moduleId && $item['slideIndex'] == $slideIndex) {
            $currentGlobalIndex = $gIdx;
            break;
        }
    }

    // REDIRECT if the user is trying to skip slides
    if ($currentGlobalIndex > $nextAllowedIndex) {
        $nextSlide = $allSlidesGlobal[$nextAllowedIndex];
        $redirectUrl = $courseService->buildCourseUrl($courseUuid, $nextSlide['moduleIndex'], $nextSlide['slideIndex']);
        header("Location: " . $redirectUrl);
        exit;
    }

    // Define allowed slide IDs for the sidebar (Visited + Next)
    $sidebarMaxIndex = $nextAllowedIndex;
    if ($currentGlobalIndex >= 0) {
        $sidebarMaxIndex = max($sidebarMaxIndex, $currentGlobalIndex + 1);
    }
    
    if ($sidebarMaxIndex >= count($allSlidesGlobal)) {
        $sidebarMaxIndex = count($allSlidesGlobal) - 1;
    }

    for ($i = 0; $i <= $sidebarMaxIndex; $i++) {
        $allowedSlideIds[] = $allSlidesGlobal[$i]['slide']->id;
    }

    // Record progress now that we've validated access
    if ($currentSlide) {
        $progressService->recordSlideView($userUuid, $currentSlide->id);
        // Refresh visited IDs in case we just recorded one
        $visitedSlideIds = $progressService->getVisitedSlideIds($userUuid, $courseUuid);
    }

    // Build flattened list of all slides for navigation
    $allSlides = $allSlidesGlobal;

    $prevUrl = null;
    $nextUrl = null;
    $currentIndexInAll = $currentGlobalIndex;

    if ($currentIndexInAll >= 0) {
        if ($currentIndexInAll > 0) {
            $prev = $allSlides[$currentIndexInAll - 1];
            $prevUrl = $courseService->buildCourseUrl($courseUuid, $prev['moduleIndex'], $prev['slideIndex']);
        }
        if ($currentIndexInAll < count($allSlides) - 1) {
            $next = $allSlides[$currentIndexInAll + 1];
            $nextUrl = $courseService->buildCourseUrl($courseUuid, $next['moduleIndex'], $next['slideIndex']);
        }
    }

    $isLastSlide = ($currentIndexInAll === count($allSlides) - 1);
    
} catch (Exception $e) {
    $errors[] = $e->getMessage();
}

$pageTitle = htmlspecialchars($course->title);

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

