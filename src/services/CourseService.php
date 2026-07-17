<?php

require_once __DIR__ . '/../uuid.php';

class CourseService
{
    public function __construct(
        private CourseRepository $courseRepository,
        private CourseNavigationService $navigationService,
        private QuizService $quizService,
        private CourseProgressService $progressService
    ) {}

    public function buildCourseUrl(
        string $courseUuid,
        string $moduleId,
        int $slideIndex
    ): string {
        return sprintf(
            'course.php?id=%s&module_id=%s&slide=%d',
            urlencode($courseUuid),
            urlencode($moduleId),
            $slideIndex
        );
    }

    public function buildCourseContext(
        string $userUuid,
        array $get,
        array $post,
        array $server,
        array &$session
    ): array {

        $courseUuid = trim((string)($get['id'] ?? ''));

        if (!isValidUuid($courseUuid)) {
            throw new InvalidArgumentException('Invalid course id.');
        }

        $course = $this->courseRepository->getCourseForUser($userUuid, $courseUuid);
        if (!$course) {
            throw new RuntimeException('Access denied.');
        }

        if (!$this->courseRepository->isCourseUnlocked($userUuid, $course['prerequisite_course_id'] ?? null)) {
            return [
                'userUuid' => $userUuid,
                'courseUuid' => $courseUuid,
                'course' => $course,
                'modules' => [],
                'slides' => [],
                'completedModuleIds' => [],
                'currentModule' => null,
                'slidesForModule' => [],
                'currentSlide' => null,
                'currentSlideIndex' => 0,
                'prevModule' => null,
                'prevSlideIndex' => null,
                'nextModule' => null,
                'nextSlideIndex' => null,
                'isCourseLocked' => true,
                'questions' => [],
                'currentSlideQuestions' => [],
                'choicesByQuestion' => [],
                'userAnswersByQuestion' => [],
                'submittedAnswers' => [],
                'quizFeedback' => [],
                'errors' => [],
                'quizPassed' => false,
                'quizAttempted' => false,
            ];
        }

        $modules = $this->courseRepository->getModules($courseUuid);

        $slides = $this->courseRepository->getSlides($courseUuid);

        $completedModules = $this->courseRepository->getCompletedModuleIds($userUuid, array_column($modules, 'id'));

        $navigation = $this->navigationService->resolve(
            $modules,
            $slides,
            $get
        );

        $this->progressService->recordSlideView($userUuid, $navigation['currentSlide']);

        $slideIds = array_values(array_unique(array_map(
            static fn(array $slide): string => (string)($slide['id'] ?? ''),
            $slides
        )));

        $slideIds = array_values(array_filter($slideIds, static fn(string $slideId): bool => $slideId !== ''));

        $viewedSlideIds = $slideIds === []
            ? []
            : $this->courseRepository->getViewedSlideIds($userUuid, $slideIds);

        $navigation = $this->navigationService->resolve(
            $modules,
            $slides,
            $get,
            $viewedSlideIds
        );

        $quiz = $this->quizService->handle(
            $navigation['currentSlide'],
            $post,
            $server,
            $session
        );

        $this->progressService->completeModuleIfNeededBySlideViews(
            $userUuid,
            $navigation['currentModule'],
            $navigation['slidesForModule'],
            $completedModules
        );

        $this->progressService->completeCourseIfNeeded(
            $userUuid,
            $courseUuid,
            $course,
            $modules,
            $completedModules
        );

        $redirectUrl = null;

        if (
            $server['REQUEST_METHOD'] === 'POST'
            && isset($post['quiz_submit'])
            && $navigation['currentSlide']
            && !empty($navigation['currentSlide']['is_quiz'])
        ) {
            $redirectUrl = $this->buildCourseUrl(
                $courseUuid,
                (string)($navigation['currentModule']['id'] ?? ''),
                (int)($navigation['currentSlideIndex'] ?? 0)
            );
        }

        return array_merge(
            [
                'userUuid' => $userUuid,
                'courseUuid' => $courseUuid,
                'course' => $course,
                'modules' => $modules,
                'slides' => $slides,
                'completedModuleIds' => $completedModules,
                'viewedSlideIds' => $viewedSlideIds,
                'isCourseLocked' => false,
                'redirectUrl' => $redirectUrl,
            ],
            $navigation,
            $quiz->toArray()
        );
    }
}
