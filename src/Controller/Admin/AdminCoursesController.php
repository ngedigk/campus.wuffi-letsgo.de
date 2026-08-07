<?php

namespace App\Controller\Admin;

use App\Services\AdminContextService;
use App\Services\AdminCourseManagementService;
use App\Services\AuthService;
use App\Services\AssetsService;

use App\Helpers\ViewRenderer;
use App\Helpers\Redirect;

use App\Dto\Course;
use App\Dto\CourseInput;
use App\Dto\Module;
use App\Dto\ModuleInput;
use App\Dto\QuestionChoiceInput;
use App\Dto\QuizQuestion;
use App\Dto\QuizQuestionInput;
use App\Dto\Slide;
use App\Dto\SlideInput;

use Exception;

class AdminCoursesController
{
    public function __construct(
        protected ViewRenderer $viewRenderer,
        protected AuthService $authService,
        protected AssetsService $assetsService,
        protected AdminContextService $adminContextService,
        private AdminCourseManagementService $courseManagementService
    ) {}

    public function renderCourse(string $courseUuid): void
    {
        $viewData = $this->courseManagementService->getCourseEditorData($courseUuid);

        $this->render(
            'admin/courses/course',
            $viewData
        );
    }

    public function renderModule(string $courseUuid, string $moduleId): void
    {
        $viewData = $this->courseManagementService->getModuleEditorData($courseUuid, (int)$moduleId);
        
        $this->render(
            'admin/courses/module',
            $viewData
        );
    }

    public function renderSlide(string $courseUuid, string $moduleId, string $slideId): void
    {
        $viewData = $this->courseManagementService->getSlideEditorData($courseUuid, (int)$moduleId, (int) $slideId);

        $this->render(
            'admin/courses/slide',
            $viewData,
            [],
            [
                'js' => [
                    ['src' => 'https://unpkg.com/grapesjs'],
                    ['src' => 'https://unpkg.com/grapesjs-blocks-basic'],
                    ['src' => '/assets/js/admin/grapesjs/main.js', 'type' => 'module']
                ],
                'css' => [
                    'https://unpkg.com/grapesjs/dist/css/grapes.min.css'
                ]
            ]
        );
    }

    public function createCourse(): void
    {
        try {
            $course = $this->courseManagementService->createCourse(new CourseInput(
                title: trim((string)($_POST['title'] ?? '')),
                description: trim((string)($_POST['description'] ?? '')),
                prerequisiteCourseId: trim((string)($_POST['prerequisite_course_id'] ?? '')) ?: null,
                sortOrder: (int)trim((string)($_POST['sort_order'] ?? 0))
            ));
            
            $_SESSION['admin_success'] = "Kurs \"$course->title\" erstellt.";

            Redirect::to('/admin/courses/' . urlencode($course->uuid));
        } catch (Exception $e) {
            $_SESSION['admin_error'] = $e->getMessage();

            Redirect::to('/admin');
        }
    }

    public function updateCourse(string $courseUuid): void
    {
        try {
            $course = $this->courseManagementService->updateCourse(new Course(
                uuid: $courseUuid,
                title: trim($_POST['title'] ?? ''),
                description: trim($_POST['description'] ?? ''),
                prerequisiteCourseId: trim((string)($_POST['prerequisite_course_id'] ?? '')) ?: null,
                sortOrder: (int) ($_POST['sort_order'] ?? 0),
            ));

            $_SESSION['admin_success'] = 'Kurs aktualisiert.';
        } catch (Exception $e) {
            $_SESSION['admin_error'] = $e->getMessage();
        }

        Redirect::to('/admin/courses/' . urlencode($courseUuid));
    }

    public function deleteCourse(string $courseUuid): void
    {
        $this->courseManagementService->deleteCourse($courseUuid);

        $_SESSION['admin_success'] = 'Kurs gelöscht.';

        Redirect::to('/admin');
    }

    public function createModule(string $courseUuid): void
    {
        try {
            $module = $this->courseManagementService->createModule(new ModuleInput(
                courseId: $courseUuid,
                title: trim((string)($_POST['title'] ?? '')),
                sortOrder: (int)trim((string)($_POST['sort_order'] ?? 0))
            ));

            $_SESSION['admin_success'] = "Module \"$module->title\" created.";

            Redirect::to('/admin/courses/' . urlencode($courseUuid) . '/modules/' . urlencode($module->id));
        } catch (Exception $e) {
            $_SESSION['admin_error'] = $e->getMessage();

            Redirect::to('/admin/courses/' . urlencode($courseUuid));
        }
    }

    public function updateModule(string $courseUuid, string $moduleId): void
    {
        try {
            $module = $this->courseManagementService->updateModule(new Module(
                id: $moduleId,
                title: trim((string)($_POST['title'] ?? '')),
                sortOrder: (int)trim((string)($_POST['sort_order'] ?? 0))
            ));

            $_SESSION['admin_success'] = "Modul \"$module->title\"aktualisiert.";
        } catch (Exception $e) {
            $_SESSION['admin_error'] = $e->getMessage();
        }

        Redirect::to('/admin/courses/' . urlencode($courseUuid) . '/modules/' . urlencode($moduleId));
    }

    public function deleteModule(string $courseUuid, string $moduleId): void
    {
        $this->courseManagementService->deleteModule((int)$moduleId);

        $_SESSION['admin_success'] = 'Modul gelöscht.';

        Redirect::to('/admin/courses/' . urlencode($courseUuid));
    }

    public function createSlide(string $courseUuid, string $moduleId): void
    {
        try {
            $audioUrl = trim((string)($_POST['audio_url'] ?? ''));

            $uploadedAudio = $this->assetsService->handleAudioUpload($_FILES);
            if ($uploadedAudio) {
                $audioUrl = $uploadedAudio;
            }

            $slide = $this->courseManagementService->createSlide(new SlideInput(
                moduleId: $moduleId,
                title: trim((string)($_POST['title'] ?? '')),
                audioUrl: $audioUrl,
                htmlContent: '',
                sortOrder: (int)trim((string)($_POST['sort_order'] ?? 0))
            ));

            $_SESSION['admin_success'] = "Folie \"$slide->title\" erstellt.";

            Redirect::to(
                '/admin/courses/' . urlencode($courseUuid) .
                '/modules/' . urlencode($moduleId) .
                '/slides/' . urlencode($slide->id)
            );   
        } catch (Exception $e) {
            $_SESSION['admin_error'] = $e->getMessage();

            Redirect::to('/admin/courses/' . urlencode($courseUuid) .
                '/modules/' . urlencode($moduleId)
            );
        }
    }

    public function updateSlide(string $courseUuid, string $moduleId, string $slideId): void
    {
        try {
            $audioUrl = trim((string)($_POST['audio_url'] ?? ''));

            $uploadedAudio = $this->assetsService->handleAudioUpload($_FILES);
            if ($uploadedAudio) {
                $audioUrl = $uploadedAudio;
            }

            $slide = $this->courseManagementService->updateSlide(new Slide(
                id: $slideId,
                title: trim((string)($_POST['title'] ?? '')),
                htmlContent: trim((string)($_POST['html_content'] ?? '')),
                audioUrl: $audioUrl,
                sortOrder: (int)trim((string)($_POST['sort_order'] ?? 0))
            ));

            $_SESSION['admin_success'] = "Folie \"$slide->title\" aktualisiert.";   
        } catch (Exception $e) {
            $_SESSION['admin_error'] = $e->getMessage();
        }

        Redirect::to(
            '/admin/courses/' . urlencode($courseUuid) .
            '/modules/' . urlencode($moduleId) .
            '/slides/' . urlencode($slideId)
        );
    }

    public function deleteSlide(string $courseUuid, string $moduleId, string $slideId): void
    {
        $this->courseManagementService->deleteSlide((int)$slideId);

        $_SESSION['admin_success'] = 'Folie gelöscht.';

        Redirect::to(
            '/admin/courses/' . urlencode($courseUuid) .
            '/modules/' . urlencode($moduleId)
        );
    }

    public function createQuestion(string $courseUuid, string $moduleId, string $slideId): void
    {
        try {
            $choices = [];
            
            foreach ($_POST['choices'] ?? [] as $choice) {
                $choices[] = new QuestionChoiceInput(
                    choiceText: trim((string) ($choice['text'] ?? '')),
                    isCorrect: !empty($choice['is_correct'])
                );
            }
            
            $question = $this->courseManagementService->createQuestion(new QuizQuestionInput(
                slideId: (int) $slideId,
                questionText: trim((string) ($_POST['question_text'] ?? '')),
                choices: $choices
            ));
            
            $_SESSION['admin_success'] = "Frage \"{$question->questionText}\" erstellt.";
        } catch (Exception $e) {
            $_SESSION['admin_error'] = $e->getMessage();
        }
        
        Redirect::to(
            '/admin/courses/' . urlencode($courseUuid) .
            '/modules/' . urlencode($moduleId) .
            '/slides/' . urlencode($slideId)
        );
    }

    public function updateQuestion(string $courseUuid, string $moduleId, string $slideId, string $questionId): void
    {
        try {
            $choices = [];
            
            foreach ($_POST['choices'] ?? [] as $choice) {
                $choices[] = new QuestionChoiceInput(
                    choiceText: trim((string) ($choice['text'] ?? '')),
                    isCorrect: !empty($choice['is_correct'])
                );
            }

            $this->courseManagementService->updateQuestion(new QuizQuestion(
                id: $questionId,
                questionText: trim(($_POST['question_text'] ?? '')),
                choices: $choices
            ));

            $_SESSION['admin_success'] = 'Frage aktualisiert.';
        } catch (Exception $e) {
            $_SESSION['admin_error'] = $e->getMessage();
        }

        Redirect::to(
            '/admin/courses/' . urlencode($courseUuid) .
            '/modules/' . urlencode($moduleId) .
            '/slides/' . urlencode($slideId)
        );
    }

    public function deleteQuestion(string $courseUuid, string $moduleId, string $slideId, string $questionId): void
    {
        $this->courseManagementService->deleteQuestion((int)$questionId);

        $_SESSION['admin_success'] = 'Frage gelöscht.';

        Redirect::to(
            '/admin/courses/' . urlencode($courseUuid) .
            '/modules/' . urlencode($moduleId) .
            '/slides/' . urlencode($slideId)
        );
    }

    public function uploadImage(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo $this->courseManagementService->uploadImage();
        exit;
    }

    public function deleteImage(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo $this->courseManagementService->deleteImage();
        exit;
    }

    private function render(string $view, array $data, array $breadcrumb = [], array $assets = [] ): void {
        $context = $this->adminContextService->buildContext(
            $this->authService->currentUser()
        );
        
        $viewData = array_merge(
            $context,
            [
                'activePage' => 'courses',
                'breadcrumb' => $breadcrumb,
            ],
            $data
        );
        
        $viewData['additionalJs'] = array_merge(
            $context['additionalJs'] ?? [],
            $data['additionalJs'] ?? [],
            $assets['js'] ?? [
                [ 'src' => '/assets/js/admin/courses.js', 'type' => 'module' ]
            ]
        );
        
        $viewData['additionalCss'] = array_merge(
            $context['additionalCss'] ?? [],
            $data['additionalCss'] ?? [],
            $assets['css'] ?? []
        );

        unset(
            $viewData['additionalJsFromData'],
            $viewData['additionalCssFromData']
        );
        
        $this->viewRenderer->renderWithAdminTemplate($view, $viewData);
    }
}