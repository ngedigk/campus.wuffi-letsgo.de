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

use App\Exceptions\CourseModuleNotFoundException;
use App\Exceptions\CourseNotFoundException;
use App\Exceptions\CourseSlideNotFoundException;

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

    public function index(): void
    {
        $courseUuid = trim($_GET['id'] ?? '');
        $moduleIndex = (int) ($_GET['module'] ?? 0);
        $slideIndex = (int) ($_GET['slide'] ?? 0);

        $user = $this->authService->currentUser();

        try {
            $course = $this->courseService->getWithDetailsForUser(
                $user->id,
                $courseUuid
            );

            $viewData = $this->buildCourseViewData(
                $user->id,
                $courseUuid,
                $course,
                $moduleIndex,
                $slideIndex
            );

            $this->renderCourse($viewData);
        } catch (
            CourseSlideNotFoundException |
            CourseModuleNotFoundException |
            CourseNotFoundException
            $e
        ) {
            $this->renderNotFound($e);
        }
    }

    public function submitQuiz(): void
    {
        $courseUuid = trim($_POST['course'] ?? '');
        $moduleIndex = (int) ($_POST['module'] ?? 0);
        $slideIndex = (int) ($_POST['slide'] ?? 0);
        $answers = $_POST['answers'] ?? [];

        $user = $this->authService->currentUser();
    
        try {
            $course = $this->courseService->getWithDetailsForUser(
                $user->id,
                $courseUuid
            );
            
            $quizResult = $this->submitQuizForCourse(
                $course,
                $moduleIndex,
                $slideIndex,
                $answers
            );

            if ($this->isFragmentRequest()) {
                $viewData = $this->buildCourseViewData(
                    $user->id,
                    $courseUuid,
                    $course,
                    $moduleIndex,
                    $slideIndex,
                    $quizResult
                );

                echo $this->viewRenderer->render(
                    'course/content',
                    $viewData
                );

                return;
            }
            
            $_SESSION['quiz_result'] = $quizResult;
            
            $redirectUrl = $this->courseService->buildCourseUrl($courseUuid, $moduleIndex, $slideIndex);
            header('Location: ' . $redirectUrl);
            exit;
        } catch (
            CourseSlideNotFoundException |
            CourseModuleNotFoundException |
            CourseNotFoundException
            $e
        ) {
            $this->renderNotFound($e);
        }
    }

    private function buildCourseViewData(
        string $userUuid,
        string $courseUuid,
        Course $course,
        int $moduleIndex,
        int $slideIndex,
        ?QuizResult $submittedQuizResult = null
    ): array {
        $currentModule = $this->getCurrentModule(
            $course,
            $moduleIndex
        );

        if (!$currentModule) {
            throw new CourseModuleNotFoundException(
                'Angefordertes Kurs Modul wurde nicht gefunden.'
            );
        }

        $slidesForModule = $currentModule->slides;
        $currentSlide = $slidesForModule[$slideIndex] ?? null;

        if (!$currentSlide) {
            throw new CourseSlideNotFoundException(
                'Die angeforderte Folie wurde nicht gefunden.'
            );
        }

        $this->progressService->recordSlideView(
            $userUuid,
            $currentSlide->id
        );

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

        $hasQuiz = $this->slideService->hasQuiz(
            (int) $currentSlide->id
        );

        $quizResult = $submittedQuizResult
            ?? $this->getQuizResult($currentSlide, $hasQuiz);

        $sidebarItems = $this->courseSidebarBuilderService->build(
            $course,
            $currentModule,
            $navigation
        );

        return [
            'pageTitle' => htmlspecialchars($course->title),
            'isLoggedIn' => $this->authService->isLoggedIn(),
            'isAdmin' => $this->authService->isAdmin(),
            'additionalCss' => [
                '/assets/css/course.css'
            ],
            'additionalJs' => [
                [
                    'src' => '/assets/js/course-nav.js',
                    'type' => 'text/javascript'
                ]
            ],
            'courseUuid' => $course->uuid,
            'courseTitle' => $course->title,
            'courseDescription' => $course->description,
            'moduleSlideCount' => count($slidesForModule),
            'currentSlide' => $currentSlide,
            'currentSlideIndex' => $slideIndex,
            'currentModule' => $currentModule,
            'currentModuleIndex' => $moduleIndex,
            'quizResult' => $quizResult,
            'choicesByQuestion' => $quizResult?->choicesByQuestion ?? [],
            'answers' => $quizResult?->isSubmitted ? $quizResult->results : null,
            'prevUrl' => $navigation->previousUrl,
            'nextUrl' => $navigation->nextUrl,
            'isLastSlide' => $navigation->isLastSlide,
            'courseSidebar' => $sidebarItems,
            'hasQuiz' => $hasQuiz,
            'breadcrumb' => [
                [
                    'url' => '/',
                    'title' => 'Startseite'
                ],
                [
                    'url' => $this->courseService->buildCourseUrl($courseUuid, $moduleIndex, 0),
                    'title' => $currentModule->title
                ],
                [
                    'title' => $currentSlide->title
                ]
            ]
        ];
    }

    private function submitQuizForCourse(
        Course $course,
        int $moduleIndex,
        int $slideIndex,
        array $answers
    ): QuizResult {
        $currentModule = $this->getCurrentModule(
            $course,
            $moduleIndex
        );

        if (!$currentModule) {
            throw new CourseModuleNotFoundException(
                'Angefordertes Kurs Modul wurde nicht gefunden.'
            );
        }

        $currentSlide = $currentModule->slides[$slideIndex] ?? null;

        if (!$currentSlide) {
            throw new CourseSlideNotFoundException(
                'Angeforderte Folie wurde nicht gefunden.'
            );
        }

        $quizResult = $this->quizService->getQuizData(
            (int) $currentSlide->id
        );

        return $this->quizService->submitQuiz(
            $quizResult,
            $answers
        );
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

        $sessionQuizResult = $_SESSION['quiz_result'] ?? null;
        
        if ($sessionQuizResult instanceof QuizResult) {
            unset($_SESSION['quiz_result']);

            if ($sessionQuizResult->slideId === (int)$currentSlide->id) {
                $quizResult = $sessionQuizResult;
            }
        }

        return $quizResult;
    }

    private function renderCourse(array $viewData): void
    {
        if ($this->isFragmentRequest()) {
            echo $this->viewRenderer->render(
                'course/content',
                $viewData
            );

            return;
        }

        $this->viewRenderer->renderWithTemplate(
            'course',
            $viewData
        );
    }

    private function isFragmentRequest(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) ===
            'xmlhttprequest';
    }

    private function renderNotFound(\Throwable $e): void
    {
        http_response_code(404);

        $this->viewRenderer->renderWithTemplate(
            '404',
            [
                'pageTitle' => 'Seite nicht gefunden',
                'isLoggedIn' => $this->authService->isLoggedIn(),
                'isAdmin' => $this->authService->isAdmin(),
                'message' => $e->getMessage(),
                'breadcrumb' => [
                    [
                        'url' => '/',
                        'title' => 'Startseite'
                    ],
                    [
                        'title' => '404'
                    ]
                ]
            ]
        );
    }

}