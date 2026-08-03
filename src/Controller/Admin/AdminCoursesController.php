<?php

namespace App\Controller\Admin;

use App\Services\AdminContextService;
use App\Services\AdminCourseManagementService;
use App\Services\AssetsService;
use App\Services\AuthService;
use App\Services\CourseService;
use App\Services\ModuleService;
use App\Services\QuestionChoiceService;
use App\Services\QuizQuestionService;
use App\Services\SlideService;
use App\Services\UuidService;

use App\Helpers\ViewRenderer;

use Exception;

class AdminCoursesController extends AdminPageController
{
    public function __construct(
        protected CourseService $courseService,
        protected SlideService $slideService,
        protected ModuleService $moduleService,
        protected ViewRenderer $viewRenderer,
        protected AuthService $authService,
        protected UuidService $uuidService,
        protected QuizQuestionService $quizQuestionService,
        protected QuestionChoiceService $questionChoicesService,
        protected AdminContextService $adminContextService,
        private AssetsService $assetsService,
        private AdminCourseManagementService $courseManagementService
    ) {
        parent::__construct($adminContextService, $authService);
    }

    public function render(array $context): void
    {
        $selectedCourse = null;
        $selectedCourseId = filter_input(INPUT_GET, 'course_id');
        if ($selectedCourseId) {
            $selectedCourse = $this->courseService->getWithDetails($selectedCourseId);
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

        $slideAssets = $this->assetsService->getSlideAssets();
        $audioFiles = $this->assetsService->getAudioFiles();

        $context['additionalJs'][] = ['src' => 'https://unpkg.com/grapesjs'];
        $context['additionalJs'][] = ['src' => 'https://unpkg.com/grapesjs-blocks-basic'];

        if ($selectedSlide) {
            $context['additionalJs'][] = [
                'src' => '/assets/js/admin/grapesjs/main.js',
                'type' => 'module'
            ];
        }
        $context['additionalJs'][] = [
            'src' => '/assets/js/admin/courses.js',
            'type' => 'module'
        ];

        $context['additionalCss'][] = 'https://unpkg.com/grapesjs/dist/css/grapes.min.css';

        $quizQuestions = [];
        $quizChoicesByQuestion = [];
        if ($selectedSlide && $this->slideService->hasQuiz($selectedSlide->id)) {
            $questions = $this->quizQuestionService->getBySlideId($selectedSlide->id);
            foreach ($questions as $question) {
                $choices = $this->questionChoicesService->getByQuestionId($question->id);
                $quizChoicesByQuestion[$question->id] = $choices;
            }
            $quizQuestions = $questions;
        }
        $context['quizQuestions'] = $quizQuestions;
        $context['quizChoicesByQuestion'] = $quizChoicesByQuestion;

        $breadcrumb = [];
        $pageTitle = '';
        if ($selectedCourse) {
            $breadcrumb[] = [
                'url' => "?page=courses&course_id={$selectedCourse->uuid}",
                'title' => "Kurs: " . $selectedCourse->title
            ];
            $pageTitle = 'Kurs bearbeiten: ' . $selectedCourse->title;
        }
        if ($selectedModule) {
            $breadcrumb[] = [
                'url' => "?page=courses&course_id={$selectedCourse->uuid}&module_id={$selectedModule->id}",
                'title' => "Modul: " . $selectedModule->title
            ];
            $pageTitle = 'Modul bearbeiten: ' . $selectedModule->title;
        }
        if ($selectedSlide) {
            $breadcrumb[] = [
                'url' => "?page=courses&course_id={$selectedCourse->uuid}&module_id={$selectedModule->id}&slide_id={$selectedSlide->id}",
                'title' => "Folie: " . $selectedSlide->title
            ];
            $pageTitle = 'Folie bearbeiten: ' . $selectedSlide->title;
        }

        $viewData = array_merge(
            $context,
            [
                'activePage' => 'courses',
                'breadcrumb' => $breadcrumb,
                'selectedCourse' => $selectedCourse,
                'selectedCourseId' => $selectedCourseId,
                'selectedModule' => $selectedModule,
                'selectedModuleId' => $selectedModuleId,
                'selectedSlide' => $selectedSlide,
                'selectedSlideId' => $selectedSlideId,
                'slideAssets' => $slideAssets,
                'audioFiles' => $audioFiles,
                'pageTitle' => $pageTitle
            ]
        );

        $this->viewRenderer->renderWithAdminTemplate('admin/courses/index', $viewData);
    }

    public function handlePost(string $action): void
    {
        switch ($action) {
            case 'create_course':
                $this->handleCreateCourse();
                break;
            case 'update_course':
                $this->handleUpdateCourse();
                break;
            case 'create_module':
                $this->handleCreateModule();
                break;
            case 'update_module':
                $this->handleUpdateModule();
                break;
            case 'create_slide':
                $this->handleCreateSlide();
                break;
            case 'update_slide':
                $this->handleUpdateSlide();
                break;
            case 'create_question':
                $this->handleCreateQuestion();
                break;
            case 'update_question':
                $this->handleUpdateQuestion();
                break;
            case 'delete_question':
                $this->handleDeleteQuestion();
                break;
            case 'delete_slide':
                $this->handleDeleteSlide();
                break;
            case 'delete_module':
                $this->handleDeleteModule();
                break;
            case 'delete_course':
                $this->handleDeleteCourse();
                break;
            case 'upload_image':
                header('Content-Type: application/json; charset=utf-8');
                echo $this->courseManagementService->handleUploadImage();
                exit;
            case 'delete_image':
                header('Content-Type: application/json; charset=utf-8');
                echo $this->courseManagementService->handleDeleteImage();
                exit;
            default:
                throw new Exception('Unsupported admin action.');
        }
    }

    private function handleCreateCourse(): void
    {
        $this->courseManagementService->createCourse($_POST);
    }

    private function handleUpdateCourse(): void
    {
        $this->courseManagementService->updateCourse($_POST);
    }

    private function handleCreateModule(): void
    {
        $this->courseManagementService->createModule($_POST);
    }

    private function handleUpdateModule(): void
    {
        $this->courseManagementService->updateModule($_POST);
    }

    private function handleCreateSlide(): void
    {
        $this->courseManagementService->createSlide($_POST, $_FILES);
    }

    private function handleUpdateSlide(): void
    {
        $this->courseManagementService->updateSlide($_POST, $_FILES);
    }

    private function handleCreateQuestion(): void
    {
        $this->courseManagementService->createQuestion($_POST);
    }

    private function handleUpdateQuestion(): void
    {
        $this->courseManagementService->updateQuestion($_POST);
    }

    private function handleDeleteQuestion(): void
    {
        $this->courseManagementService->deleteQuestion($_POST);
    }

    private function handleDeleteSlide(): void
    {
        $this->courseManagementService->deleteSlide($_POST);
    }

    private function handleDeleteModule(): void
    {
        $this->courseManagementService->deleteModule($_POST);
    }

    private function handleDeleteCourse(): void
    {
        $this->courseManagementService->deleteCourse($_POST);
    }
}