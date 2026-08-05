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

            $result = $this->processCourse($user->id, $courseUuid, $moduleIndex, $slideIndex);
            
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
                'hasQuiz' => $result['hasQuiz'],
                'breadcrumb' => [
                    [
                        'url' => "/",
                        'title' => "Startseite"
                    ], [
                        'url' => $this->courseService->buildCourseUrl(
                            $courseUuid,
                            $moduleIndex,
                            0
                        ),
                        'title' => $result['currentModule']->title
                    ], [
                        'title' => $result['currentSlide']->title
                    ]
                ]
            ];

            $this->viewRenderer->renderWithTemplate('course', $viewData);
        } catch (
            CourseSlideNotFoundException |
            CourseModuleNotFoundException |
            CourseNotFoundException
            $e
        ) {
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
                            'url' => "/",
                            'title' => "Startseite"
                        ], [
                            'title' => '404'
                        ]
                    ]
                ]
            );
        }
    }

    private function processCourse(string $userUuid, string $courseUuid, int $moduleIndex, int $slideIndex): array
    {
        $course = $this->courseService->getWithDetailsForUser($userUuid, $courseUuid);
        $currentModule = $this->getCurrentModule($course, $moduleIndex);

        if (!$currentModule) {
            throw new CourseModuleNotFoundException('Angeforderters Kurs Modul wurde nicht gefunden.');
        }

        $slidesForModule = $currentModule
            ? $currentModule->slides
            : [];

        $currentSlide = $slidesForModule[$slideIndex] ?? null;

        if (!$currentSlide) {
            throw new CourseSlideNotFoundException('Die angeforderte Folie wurde nicht gefunden.');
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

    public function submitQuiz(): void
    {
        $courseUuid = trim($_POST['course'] ?? '');
        $moduleIndex = (int) ($_POST['module'] ?? 0);
        $slideIndex = (int) ($_POST['slide'] ?? 0);
        $answers = $_POST['answers'] ?? [];

        $user = $this->authService->currentUser();
        
        try {
            $course = $this->courseService->getWithDetailsForUser($user->id, $courseUuid);
            $currentModule = $this->getCurrentModule($course, $moduleIndex);
            $currentSlide = $currentModule?->slides[$slideIndex] ?? null;
            
            if (!$currentModule) {
                throw new CourseModuleNotFoundException('Angeforderters Kurs Modul wurde nicht gefunden.');
            }
            
            if (!$currentSlide) {
                throw new CourseSlideNotFoundException('Angeforderte Folie wurde nicht gefunden.');
            }
            
            $quizResult = $this->quizService->getQuizData((int) $currentSlide->id);
            $quizResult = $this->quizService->submitQuiz( $quizResult, $answers );
            
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
            http_response_code(404);
            $this->viewRenderer->renderWithTemplate('404', [
                'pageTitle' => 'Seite nicht gefunden',
                'isLoggedIn' => $this->authService->isLoggedIn(),
                'isAdmin' => $this->authService->isAdmin(),
                'message' => $e->getMessage()
            ]);
        }
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

}