<?php

namespace App\Controller;

use App\Services\CourseService;
use App\Services\CourseSidebarBuilderService;
use App\Services\ProgressService;
use App\Services\QuizService;
use App\Services\AuthService;
use App\Services\SlideService;

use App\Helpers\ViewRenderer;

use App\Dto\Course;
use App\Dto\Module;
use App\Dto\Slide;
use App\Dto\QuizResult;

use \Exception;

class CourseController
{
    public function __construct(
        private CourseService $courseService,
        private CourseSidebarBuilderService $courseSidebarBuilderService,
        private ProgressService $progressService,
        private QuizService $quizService,
        private ViewRenderer $viewRenderer,
        private AuthService $authService,
        private SlideService $slideService
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
                'additionalCss' => ['/assets/css/course.css'],
                'errors' => $errors,
                'course' => $result['course'],
                'slidesForModule' => $result['slidesForModule'],
                'currentSlide' => $result['currentSlide'],
                'currentSlideIndex' => $result['currentSlideIndex'],
                'choicesByQuestion' => $result['choicesByQuestion'],
                'answers' => $result['answers'],
                'quizResult' => $result['quizResult'],
                'currentModule' => $result['currentModule'],
                'prevUrl' => $result['prevUrl'],
                'nextUrl' => $result['nextUrl'],
                'isLastSlide' => $result['isLastSlide'],
                'courseSidebar' => $result['courseSidebar'],
                'hasQuiz' => $this->slideService->hasQuiz($result['currentSlide']->id)
            ];

            $this->viewRenderer->renderWithTemplate('course', $viewData);

        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            error_log($e->getMessage());
        }
    }

    private function getCurrentModule(Course $course, int $moduleId): ?Module
    {
        $modules = array_values($course->modules);
        $currentModule = $modules[$moduleId] ?? null;

        if (!$currentModule && !empty($modules)) {
            $currentModule = $modules[0];
            $moduleId = 0;
        }
        return $currentModule;
    }

    private function getQuizResult(?Slide $currentSlide): ?QuizResult
    {
        if (!$currentSlide || !$this->slideService->hasQuiz($currentSlide->id)) {
            return null;
        }

        $quizResult = $this->quizService->getQuizData((int)$currentSlide->id);

        if (isset($_POST['quiz_submit']) && !empty($_POST['answers'])) {
            $quizResult = $this->quizService->submitQuiz($quizResult, $_POST['answers']);
        }

        return $quizResult;
    }

    private function getFlattenedSlides(Course $course): array
    {
        $flattenedSlides = [];
        foreach ($course->modules as $moduleIndex => $module) {
            foreach ($module->slides as $sIdx => $slide) {
                $flattenedSlides[] = [
                    'module' => $module,
                    'moduleIndex' => $moduleIndex,
                    'slide' => $slide,
                    'slideIndex' => $sIdx
                ];
            }
        }
        return $flattenedSlides;
    }

    private function getFurthestVisitedSlideIndex(array $allSlides, array $visitedSlideIds): int
    {
        $maxVisitedIndex = -1;
        foreach ($allSlides as $idx => $item) {
            if (in_array($item['slide']->id, $visitedSlideIds)) {
                $maxVisitedIndex = $idx;
            }
        }
        return $maxVisitedIndex;
    }

    private function getNextAllowedIndex(array $allSlides, int $maxVisitedIndex): int
    {
        $nextAllowedIndex = $maxVisitedIndex + 1;
        
        if ($nextAllowedIndex >= count($allSlides)) {
            $nextAllowedIndex = count($allSlides) - 1;
        }

        return $nextAllowedIndex;
    }

    private function getCurrentFlattenedSlideIndex(array $allSlides, int $moduleId, int $slideIndex): int
    {
        $currentGlobalIndex = -1;
        foreach ($allSlides as $index => $item) {
            if ($item['moduleIndex'] == $moduleId && $item['slideIndex'] == $slideIndex) {
                $currentGlobalIndex = $index;
                break;
            }
        }

        return $currentGlobalIndex;
    }

    private function processCourse(string $userUuid, string $courseUuid, int $moduleId, int $slideIndex): array
    {
        $course = $this->courseService->getWithDetailsForUser($userUuid, $courseUuid);
        $currentModule = $this->getCurrentModule($course, $moduleId);

        $slidesForModule = $currentModule ? $currentModule->slides : [];
        $currentSlide = $slidesForModule[$slideIndex] ?? null;
        $currentSlideIndex = $slideIndex;
        $quizResult = $this->getQuizResult($currentSlide);
        $visitedSlideIds = $this->progressService->getVisitedSlideIds($userUuid, $courseUuid);
        $allSlidesGlobal = $this->getFlattenedSlides($course);
        $maxVisitedIndex = $this->getFurthestVisitedSlideIndex($allSlidesGlobal, $visitedSlideIds);
        $nextAllowedIndex = $this->getNextAllowedIndex($allSlidesGlobal, $maxVisitedIndex);
        $currentGlobalIndex = $this->getCurrentFlattenedSlideIndex($allSlidesGlobal, $moduleId, $slideIndex);

        if ($currentGlobalIndex > $nextAllowedIndex) {
            $nextSlide = $allSlidesGlobal[$nextAllowedIndex];
            $redirectUrl = $this->courseService->buildCourseUrl($courseUuid, $nextSlide['moduleIndex'], $nextSlide['slideIndex']);
            header("Location: " . $redirectUrl);
            exit;
        }

        $sidebarMaxIndex = $nextAllowedIndex;
        if ($currentGlobalIndex >= 0) {
            $sidebarMaxIndex = max($sidebarMaxIndex, $currentGlobalIndex + 1);
        }
        
        $allowedSlideIds = [];
        if ($sidebarMaxIndex >= count($allSlidesGlobal)) {
            $sidebarMaxIndex = count($allSlidesGlobal) - 1;
        }

        for ($i = 0; $i <= $sidebarMaxIndex; $i++) {
            $allowedSlideIds[] = $allSlidesGlobal[$i]['slide']->id;
        }

        if ($currentSlide) {
            $this->progressService->recordSlideView($userUuid, $currentSlide->id);
            $visitedSlideIds = $this->progressService->getVisitedSlideIds($userUuid, $courseUuid);
        }

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

        $sidebarItems = $this->courseSidebarBuilderService->build(
            $course,
            $currentModule,
            $allowedSlideIds,
            $visitedSlideIds
        );

        return [
            'course' => $course,
            'currentModule' => $currentModule,
            'slidesForModule' => $slidesForModule,
            'currentSlide' => $currentSlide,
            'currentSlideIndex' => $currentSlideIndex,
            'quizResult' => $quizResult,
            'choicesByQuestion' => $quizResult !== null ? $quizResult->choicesByQuestion : [],
            'answers' => $quizResult !== null ? ($quizResult->isSubmitted ? $quizResult->results : null) : null,
            'prevUrl' => $prevUrl,
            'nextUrl'=> $nextUrl,
            'isLastSlide' => $isLastSlide,
            'courseSidebar' => $sidebarItems
        ];
    }

}