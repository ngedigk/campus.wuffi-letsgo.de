<?php

namespace App\Controller;

use App\Services\CourseService;
use App\Services\ProgressService;
use App\Services\QuizService;
use App\Services\AuthService;
use App\Services\SlideService;
use App\Services\CourseSidebarBuilderService;
use App\Services\CourseNavigationService;

use App\Helpers\ViewRenderer;

use App\Dto\Course;
use App\Dto\Module;
use App\Dto\Slide;
use App\Dto\QuizResult;
use App\Exceptions\CourseSlideNotFoundException;
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
        private SlideService $slideService,
        private CourseNavigationService $courseNavigationService
    ) {}

    public function handle(string $courseUuid, int $moduleIndex, int $slideIndex): void
    {
        $userUuid = (string)($_SESSION['user_id'] ?? '');

        try {

            $result = $this->processCourse($userUuid, $courseUuid, $moduleIndex, $slideIndex);
            
            $viewData = [
                'pageTitle' => htmlspecialchars($result['course']->title),
                'isLoggedIn' => $this->authService->isLoggedIn(),
                'isAdmin' => $this->authService->isAdmin(),
                'additionalCss' => ['/assets/css/course.css'],
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
                'hasQuiz' => $result['hasQuiz']
            ];

            $this->viewRenderer->renderWithTemplate('course', $viewData);
        } catch (CourseSlideNotFoundException $e) {
            http_response_code(404);

            $this->viewRenderer->renderWithTemplate(
                '404',
                [
                    'pageTitle' => 'Seite nicht gefunden',
                    'isLoggedIn' => $this->authService->isLoggedIn(),
                    'isAdmin' => $this->authService->isAdmin(),
                    'message' => 'Die angeforderte Lektion wurde nicht gefunden.'
                ]
            );
        }
    }

    private function processCourse(string $userUuid, string $courseUuid, int $moduleIndex, int $slideIndex): array
    {
        $course = $this->courseService->getWithDetailsForUser($userUuid, $courseUuid);
        $currentModule = $this->getCurrentModule($course, $moduleIndex);

        $slidesForModule = $currentModule
            ? $currentModule->slides
            : [];

        $currentSlide = $slidesForModule[$slideIndex] ?? null;

        $visitedSlideIds = $this->progressService->getVisitedSlideIds(
            $userUuid,
            $courseUuid
        );

        $navigation = $this->courseNavigationService->getNavigation(
            $course,
            $moduleIndex,
            $slideIndex,
            $visitedSlideIds
        );

        if ($navigation->shouldRedirect()) {
            $redirectUrl = $this->courseService->buildCourseUrl(
                $courseUuid,
                $navigation->redirectModuleIndex,
                $navigation->redirectSlideIndex
            );

            header('Location: ' . $redirectUrl);
            exit;
        }

        if ($currentSlide) {
            $this->progressService->recordSlideView(
                $userUuid,
                $currentSlide->id
            );
            if (!in_array($currentSlide->id, $visitedSlideIds, true)) {
                $visitedSlideIds[] = $currentSlide->id;
            }
        }

        $hasQuiz = $currentSlide !== null && $this->slideService->hasQuiz($currentSlide->id);

        $quizResult = $this->getQuizResult($currentSlide, $hasQuiz);

        $sidebarItems = $this->courseSidebarBuilderService->build(
            $course,
            $currentModule,
            $navigation->allowedSlideIds,
            $navigation->visitedSlideIds
        );

        return [
            'course' => $course,
            'currentModule' => $currentModule,
            'slidesForModule' => $slidesForModule,
            'currentSlide' => $currentSlide,
            'currentSlideIndex' => $slideIndex,
            'quizResult' => $quizResult,
            'choicesByQuestion' => $quizResult?->choicesByQuestion ?? [],
            'answers' => $quizResult?->isSubmitted
                ? $quizResult->results
                : null,
            'prevUrl' => $navigation->previousUrl,
            'nextUrl' => $navigation->nextUrl,
            'isLastSlide' => $navigation->isLastSlide,
            'courseSidebar' => $sidebarItems,
            'hasQuiz' => $hasQuiz
        ];
    }

    private function getCurrentModule(Course $course, int $moduleIndex): ?Module
    {
        $modules = array_values($course->modules);

        return $modules[$moduleIndex] ?? null;
    }

    private function getQuizResult(?Slide $currentSlide, bool $hasQuiz): ?QuizResult
    {
        if (!$currentSlide || !$hasQuiz) {
            return null;
        }

        $quizResult = $this->quizService->getQuizData((int)$currentSlide->id);

        if (isset($_POST['quiz_submit']) && !empty($_POST['answers'])) {
            $quizResult = $this->quizService->submitQuiz($quizResult, $_POST['answers']);
        }

        return $quizResult;
    }

}