<?php

namespace App\Controller\Admin;

use App\Dto\CourseInput;
use App\Dto\ModuleInput;
use App\Dto\Module;
use App\Dto\SlideInput;
use App\Dto\Slide;
use App\Dto\QuizQuestionInput;
use App\Dto\QuizQuestion;
use App\Dto\QuestionChoiceInput;

use App\Services\CourseService;
use App\Services\UserService;
use App\Services\SlideService;
use App\Services\ModuleService;
use App\Services\AuthService;
use App\Services\CsrfService;
use App\Services\UuidService;
use App\Services\RegistrationCodeService;
use App\Services\AssetsService;
use App\Services\QuizService;
use App\Services\QuizQuestionService;

use App\Helpers\ViewRenderer;

use App\Repositories\AccessCodeRepository;
use App\Repositories\QuizQuestionRepository;
use App\Repositories\QuestionChoiceRepository;

use Exception;

class AdminCoursesController extends AdminPageController
{
    public function __construct(
        CourseService $courseService,
        UserService $userService,
        AccessCodeRepository $accessCodeRepository,
        SlideService $slideService,
        ModuleService $moduleService,
        ViewRenderer $viewRenderer,
        AuthService $authService,
        CsrfService $csrfService,
        UuidService $uuidService,
        RegistrationCodeService $registrationCodeService,
        QuizQuestionRepository $quizQuestionRepository,
        QuestionChoiceRepository $questionChoicesRepository,
        QuizService $quizService,
        QuizQuestionService $quizQuestionService,
        private AssetsService $assetsService
    ) {
        return parent::__construct(
            $courseService,
            $userService,
            $accessCodeRepository,
            $slideService,
            $moduleService,
            $viewRenderer,
            $authService,
            $csrfService,
            $uuidService,
            $registrationCodeService,
            $quizQuestionRepository,
            $questionChoicesRepository,
            $quizService,
            $quizQuestionService
        );
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
            $questions = $this->quizQuestionRepository->getBySlideId($selectedSlide->id);
            foreach ($questions as $question) {
                $choices = $this->questionChoicesRepository->getByQuestionId($question->id);
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

        $viewData = [
            ...$context,
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
        ];

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
                echo $this->assetsService->handleUploadImage();
                exit;
            case 'delete_image':
                header('Content-Type: application/json; charset=utf-8');
                echo $this->assetsService->handleDeleteImage();
                exit;
            default:
                throw new Exception('Unsupported admin action.');
        }
    }

    private function handleCreateCourse(): void
    {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $prerequisiteCourseId = trim($_POST['prerequisite_course_id'] ?? '');
        $sortOrder = (int)trim($_POST['sort_order'] ?? 0);

        if ($title === '') {
            throw new Exception('Bitte geben Sie einen Kursnamen an.');
        }
        $prerequisiteCourseId = $prerequisiteCourseId !== '' ? $prerequisiteCourseId : null;

        $this->courseService->create(new CourseInput(
            uuid: $this->uuidService->generate(),
            title: $title,
            description: $description,
            prerequisiteCourseId: $prerequisiteCourseId,
            sortOrder: $sortOrder
        ));
        $_SESSION['admin_success'] = 'Kurs erstellt.';
    }

    private function handleUpdateCourse(): void
    {
        $courseId = trim($_POST['course_id'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $prerequisiteCourseId = trim($_POST['prerequisite_course_id'] ?? '') ?: null;
        $sortOrder = (int)trim($_POST['sort_order'] ?? 0);

        if ($title === '') {
            throw new Exception('Bitte geben Sie einen gültigen Kursnamen an.');
        }

        $this->courseService->update(new CourseInput(
            uuid: $courseId,
            title: $title,
            description: $description,
            prerequisiteCourseId: $prerequisiteCourseId,
            sortOrder: $sortOrder
        ));
        $_SESSION['admin_success'] = 'Kurs aktualisiert.';
    }

    private function handleCreateModule(): void
    {
        $courseId = trim($_POST['course_id'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $sortOrder = (int)trim($_POST['sort_order'] ?? 0);

        if ($title === '') {
            throw new Exception('Bitte geben Sie einen Modulnamen an.');
        }

        $moduleId = $this->moduleService->create(new ModuleInput(
            courseId: $courseId,
            title: $title,
            sortOrder: $sortOrder
        ));
        $_SESSION['admin_success'] = "Module $moduleId created.";
    }

    private function handleUpdateModule(): void
    {
        $moduleId = (int)trim($_POST['module_id'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $sortOrder = (int)trim($_POST['sort_order'] ?? 0);

        if ($title === '') {
            throw new Exception('Bitte geben Sie einen Modulnamen an.');
        }

        $this->moduleService->update(new Module(
            id: $moduleId,
            title: $title,
            sortOrder: $sortOrder,
            slides: null
        ));
        $_SESSION['admin_success'] = 'Modul aktualisiert.';
    }

    private function handleCreateSlide(): void
    {
        $moduleId = (int)trim($_POST['module_id'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $audioUrl = trim($_POST['audio_url'] ?? '');
        $sortOrder = (int)trim($_POST['sort_order'] ?? 0);

        if ($title === '') {
            throw new Exception('Bitte geben Sie einen Folientitel an.');
        }

        $uploadedAudio = $this->assetsService->handleAudioUpload($_FILES);
        if ($uploadedAudio) {
            $audioUrl = $uploadedAudio;
        }

        $slideId = $this->slideService->create(new SlideInput(
            moduleId: $moduleId,
            title: $title,
            audioUrl: $audioUrl,
            htmlContent: '',
            sortOrder: $sortOrder
        ));
        $_SESSION['admin_success'] = "Folie $slideId erstellt.";
    }

    private function handleUpdateSlide(): void
    {
        $slideId = (int)trim($_POST['slide_id'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $htmlContent = trim($_POST['html_content'] ?? '');
        $audioUrl = trim($_POST['audio_url'] ?? '');
        $sortOrder = (int)trim($_POST['sort_order'] ?? 0);

        if ($title === '') {
            throw new Exception('Bitte geben Sie einen Folientitel an.');
        }

        $uploadedAudio = $this->assetsService->handleAudioUpload($_FILES);
        if ($uploadedAudio) {
            $audioUrl = $uploadedAudio;
        }

        $this->slideService->update(new Slide(
            id: $slideId,
            title: $title,
            htmlContent: $htmlContent,
            audioUrl: $audioUrl,
            sortOrder: $sortOrder
        ));
        $_SESSION['admin_success'] = 'Folie aktualisiert.';
    }
    
    private function handleCreateQuestion(): void
    {
        $slideId = (int)trim($_POST['slide_id'] ?? '');
        $questionText = trim($_POST['question_text'] ?? '');
        
        if ($questionText === '') {
            throw new Exception('Bitte geben Sie einen Fragen-Text an.');
        }

        $choices = $_POST['choices'] ?? [];
        
        if (empty($choices)) {
            throw new Exception('Bitte geben Sie mindestens eine Antwort ein.');
        }
        
        $hasCorrect = false;
        foreach ($choices as $choice) {
            if (trim($choice['text'] ?? '') === '') {
                throw new Exception('Antwort Text darf nicht leer sein.');
            }
            if (!empty($choice['is_correct'])) {
                $hasCorrect = true;
            }
        }
        
        if (!$hasCorrect) {
            throw new Exception('Bitte markieren Sie mindestens eine korrekte Antwort.');
        }

        $questionId = $this->quizQuestionService->create(new QuizQuestionInput(
            slideId: $slideId,
            questionText: $questionText
        ));

        foreach ($choices as $choiceData) {
            $this->questionChoicesRepository->create(new QuestionChoiceInput(
                questionId: $questionId,
                choiceText: trim($choiceData['text']),
                isCorrect: !empty($choiceData['is_correct'])
            ));
        }

        $_SESSION['admin_success'] = "Frage $questionId erstellt.";
    }

    private function handleUpdateQuestion(): void
    {
        $questionId = (int)trim($_POST['question_id'] ?? '');
        $slideId = (int)trim($_POST['slide_id'] ?? '');
        $questionText = trim($_POST['question_text'] ?? '');

        if ($questionId === 0 || $questionText === '') {
            throw new Exception('Bitte geben Sie einen gültigen Fragen-Text an.');
        }

        $choices = $_POST['choices'] ?? [];
        
        if (empty($choices)) {
            throw new Exception('Bitte geben Sie mindestens eine Antwort ein.');
        }
        
        $hasCorrect = false;
        foreach ($choices as $choice) {
            if (trim($choice['text'] ?? '') === '') {
                throw new Exception('Antwort Text darf nicht leer sein.');
            }
            if (!empty($choice['is_correct'])) {
                $hasCorrect = true;
            }
        }
        
        if (!$hasCorrect) {
            throw new Exception('Bitte markieren Sie mindestens eine korrekte Antwort.');
        }

        $this->quizQuestionService->update(new QuizQuestion(
            id: $questionId,
            slideId: $slideId,
            questionText: $questionText
        ));

        try {
            $this->questionChoicesRepository->deleteByQuestionId($questionId);
            foreach ($choices as $choiceData) {
                $this->questionChoicesRepository->create(new QuestionChoiceInput(
                    questionId: $questionId,
                    choiceText: trim($choiceData['text']),
                    isCorrect: !empty($choiceData['is_correct'])
                ));
            }
        } catch (\Exception $e) {
             throw new Exception("Fehler beim Aktualisieren der Antworten: " . $e->getMessage());
        }

        $_SESSION['admin_success'] = 'Frage aktualisiert.';
    }

    private function handleDeleteQuestion(): void
    {
        $questionId = (int)trim($_POST['question_id'] ?? '');
        $this->quizQuestionService->delete($questionId);
        $_SESSION['admin_success'] = 'Frage gelöscht.';
    }

    private function handleDeleteSlide(): void
    {
        $slideId = (int)trim($_POST['slide_id'] ?? '');
        $this->slideService->delete($slideId);
        $_SESSION['admin_success'] = 'Folie gelöscht.';
    }

    private function handleDeleteModule(): void
    {
        $moduleId = (int)trim($_POST['module_id'] ?? '');
        $this->moduleService->delete($moduleId);
        $_SESSION['admin_success'] = 'Modul gelöscht.';
    }

    private function handleDeleteCourse(): void
    {
        $courseId = trim($_POST['course_id'] ?? '');
        $this->courseService->delete($courseId);
        $_SESSION['admin_success'] = 'Kurs gelöscht.';
    }
}