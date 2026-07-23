<?php

class CourseController
{
    public function __construct(
        private CourseService $courseService,
        private ProgressService $progressService,
        private QuizService $quizService,
        private ViewRenderer $viewRenderer,
        private AuthService $authService
    ) {}

    public function handle(string $id, int $moduleId, int $slideIndex): void
    {
        $userUuid = (string)($_SESSION['user_id'] ?? '');
        $errors = [];

        try {
            $result = $this->processCourse($userUuid, $id, $moduleId, $slideIndex);
            
            $viewData = [
                'pageTitle' => htmlspecialchars($result['course']->title),
                'isLoggedIn' => $this->authService->isLoggedIn(),
                'isAdmin' => $this->authService->isAdmin(),
                'course' => $result['course'],
                'slidesForModule' => $result['slidesForModule'],
                'currentSlide' => $result['currentSlide'],
                'currentSlideIndex' => $result['currentSlideIndex'],
                'currentSlideQuestions' => $result['currentSlideQuestions'],
                'choicesByQuestion' => $result['choicesByQuestion'],
                'answers' => $result['answers'],
                'quizResult' => $result['quizResult'],
                'quizAttempted' => $result['quizAttempted'],
                'quizPassed' => $result['quizPassed'],
                'visitedSlideIds' => $result['visitedSlideIds'],
                'allowedSlideIds' => $result['allowedSlideIds'],
                'currentModule' => $result['currentModule'],
                'allSlides' => $result['allSlides'],
                'prevUrl' => $result['prevUrl'],
                'nextUrl' => $result['nextUrl'],
                'isLastSlide' => $result['isLastSlide'],
                'additionalCss' => ['/assets/css/course.css'],
                'errors' => $errors
            ];

            $this->viewRenderer->renderWithTemplate('course', $viewData);

        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            error_log($e->getMessage());
        }
    }

    private function processCourse(string $userUuid, string $courseUuid, int $moduleId, int $slideIndex): array
    {
        $course = $this->courseService->getWithDetailsForUser($userUuid, $courseUuid);
        
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
        




        // Quiz Handling using QuizService2
        $quizResult = null;
        if ($currentSlide && !empty($currentSlide->isQuiz)) {
            $quizResult = $this->quizService->getQuizData((int)$currentSlide->id);

            // Check if quiz was just submitted
            if (isset($_POST['quiz_submit']) && !empty($_POST['answers'])) {
                $quizResult = $this->quizService->submitQuiz($quizResult, $_POST['answers']);
            }
        }

        $visitedSlideIds = $this->progressService->getVisitedSlideIds($userUuid, $courseUuid);

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
            $redirectUrl = $this->courseService->buildCourseUrl($courseUuid, $nextSlide['moduleIndex'], $nextSlide['slideIndex']);
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
            $this->progressService->recordSlideView($userUuid, $currentSlide->id);
            // Refresh visited IDs in case we just recorded one
            $visitedSlideIds = $this->progressService->getVisitedSlideIds($userUuid, $courseUuid);
        }

        // Build flattened list of all slides for navigation
        $allSlides = $allSlidesGlobal;

        $prevUrl = null;
        $nextUrl = null;
        $currentIndexInAll = $currentGlobalIndex;

        if ($currentIndexInAll >= 0) {
            if ($currentIndexInAll > 0) {
                $prev = $allSlides[$currentIndexInAll - 1];
                $prevUrl = $this->courseService->buildCourseUrl($courseUuid, $prev['moduleIndex'], $prev['slideIndex']);
            }
            if ($currentIndexInAll < count($allSlides) - 1) {
                $next = $allSlides[$currentIndexInAll + 1];
                $nextUrl = $this->courseService->buildCourseUrl($courseUuid, $next['moduleIndex'], $next['slideIndex']);
            }
        }

        $isLastSlide = ($currentIndexInAll === count($allSlides) - 1);

        return [
            'course' => $course,
            'currentModule' => $currentModule,
            'allSlides' => $allSlides,
            'slidesForModule' => $slidesForModule,
            'currentSlide' => $currentSlide,
            'currentSlideIndex' => $currentSlideIndex,
            'quizResult' => $quizResult,
            'quizAttempted' => $quizResult !== null && $quizResult->isSubmitted,
            'quizPassed' => $quizResult !== null && $quizResult->passed,
            'currentSlideQuestions' => $quizResult !== null ? $quizResult->questions : [],
            'choicesByQuestion' => $quizResult !== null ? $quizResult->choicesByQuestion : [],
            'answers' => $quizResult !== null ? ($quizResult->isSubmitted ? $quizResult->results : null) : null,
            'visitedSlideIds' => $visitedSlideIds,
            'allowedSlideIds' => $allowedSlideIds,
            'prevUrl' => $prevUrl,
            'nextUrl'=> $nextUrl,
            'isLastSlide' => $isLastSlide,
            'courseService' => $this->courseService
        ];
    }

}