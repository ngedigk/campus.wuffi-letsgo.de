<?php

namespace App\Controller\Admin;

use App\Dto\CourseInput;
use App\Dto\ModuleInput;
use App\Dto\Module;
use App\Dto\SlideInput;
use App\Dto\Slide;
use Exception;

class AdminCoursesController extends AdminPageController
{
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

        $assetDir = __DIR__ . '/../../assets/images/slides/';
        $assetUrl = '/assets/images/slides/';
        $slideAssets = [];

        if (is_dir($assetDir)) {
            foreach (scandir($assetDir) as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                $path = $assetDir . $file;
                if (is_file($path)) {
                    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $slideAssets[] = ['src' => $assetUrl . $file];
                    }
                }
            }
        }

        $context['additionalJs'][] = 'https://unpkg.com/grapesjs';
        $context['additionalJs'][] = 'https://unpkg.com/grapesjs-blocks-basic';

        if ($selectedSlide) {
            $context['additionalJs'][] = '/assets/js/grapes-init.js';
        }
        $context['additionalJs'][] = '/assets/js/admin/courses.js';

        $context['additionalCss'][] = 'https://unpkg.com/grapesjs/dist/css/grapes.min.css';

        $viewData = [
            ...$context,
            'activePage' => 'courses',
            'breadcrumb' => [
                [
                    'url' => '',
                    'title' => $selectedCourse->title ?? 'Courses'
                ],
            ],
            'selectedCourse' => $selectedCourse,
            'selectedCourseId' => $selectedCourseId,
            'selectedModule' => $selectedModule,
            'selectedModuleId' => $selectedModuleId,
            'selectedSlide' => $selectedSlide,
            'selectedSlideId' => $selectedSlideId,
            'slideAssets' => $slideAssets,
            'pageTitle' => 'Courses'
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
                $this->handleUploadImage();
                break;
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
            throw new Exception('Please provide a course title.');
        }
        $prerequisiteCourseId = $prerequisiteCourseId !== '' ? $prerequisiteCourseId : null;

        $this->courseService->create(new CourseInput(
            uuid: $this->uuidService->generate(),
            title: $title,
            description: $description,
            prerequisiteCourseId: $prerequisiteCourseId,
            sortOrder: $sortOrder
        ));
        $_SESSION['admin_success'] = 'Course created.';
    }

    private function handleUpdateCourse(): void
    {
        $courseId = trim($_POST['course_id'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $prerequisiteCourseId = trim($_POST['prerequisite_course_id'] ?? '') ?: null;
        $sortOrder = (int)trim($_POST['sort_order'] ?? 0);

        if ($title === '') {
            throw new Exception('Please provide a valid title.');
        }

        $this->courseService->update(new CourseInput(
            uuid: $courseId,
            title: $title,
            description: $description,
            prerequisiteCourseId: $prerequisiteCourseId,
            sortOrder: $sortOrder
        ));
        $_SESSION['admin_success'] = 'Course updated.';
    }

    private function handleCreateModule(): void
    {
        $courseId = trim($_POST['course_id'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $sortOrder = (int)trim($_POST['sort_order'] ?? 0);

        if ($courseId === '' || $title === '') {
            throw new Exception('Please provide a course and module title.');
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

        if ($moduleId === 0 || $title === '') {
            throw new Exception('Please provide a valid module ID and title.');
        }

        $this->moduleService->update(new Module(
            id: $moduleId,
            title: $title,
            sortOrder: $sortOrder,
            slides: null
        ));
        $_SESSION['admin_success'] = 'Module updated.';
    }

    private function handleCreateSlide(): void
    {
        $moduleId = (int)trim($_POST['module_id'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $audioUrl = trim($_POST['audio_url'] ?? '');
        $sortOrder = (int)trim($_POST['sort_order'] ?? 0);

        if ($title === '') {
            throw new Exception('Please provide a slide title.');
        }

        $slideId = $this->slideService->create(new SlideInput(
            moduleId: $moduleId,
            title: $title,
            audioUrl: $audioUrl,
            htmlContent: '',
            sortOrder: $sortOrder,
            isQuiz: false
        ));
        $_SESSION['admin_success'] = "Slide $slideId created.";
    }

    private function handleUpdateSlide(): void
    {
        $slideId = (int)trim($_POST['slide_id'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $htmlContent = trim($_POST['html_content'] ?? '');
        $audioUrl = trim($_POST['audio_url'] ?? '');
        $sortOrder = (int)trim($_POST['sort_order'] ?? 0);
        $isQuiz = filter_var($_POST['is_quiz'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if ($slideId === 0 || $title === '') {
            throw new Exception('Please provide a valid slide ID and title.');
        }

        $this->slideService->update(new Slide(
            id: $slideId,
            title: $title,
            htmlContent: $htmlContent,
            audioUrl: $audioUrl,
            sortOrder: $sortOrder,
            isQuiz: $isQuiz
        ));
        $_SESSION['admin_success'] = 'Slide updated.';
    }

    private function handleDeleteSlide(): void
    {
        $slideId = (int)trim($_POST['slide_id'] ?? '');
        $this->slideService->delete($slideId);
        $_SESSION['admin_success'] = 'Slide deleted.';
    }

    private function handleDeleteModule(): void
    {
        $moduleId = (int)trim($_POST['module_id'] ?? '');
        $this->moduleService->delete($moduleId);
        $_SESSION['admin_success'] = 'Module deleted.';
    }

    private function handleDeleteCourse(): void
    {
        $courseId = trim($_POST['course_id'] ?? '');
        $this->courseService->delete($courseId);
        $_SESSION['admin_success'] = 'Course deleted.';
    }

    private function handleUploadImage(): void
    {
        if (!$this->authService->isAdmin()) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        try {
            $this->csrfService->validateToken($_POST['csrf_token']);
        } catch (\Exception $e) {
            http_response_code(403);
            echo json_encode(['error' => 'CSRF token invalid']);
            exit;
        }

        if (!isset($_FILES['files'])) {
            http_response_code(400);
            echo json_encode(['error' => 'No file uploaded']);
            exit;
        }

        $uploadDir = __DIR__ . '/../../assets/images/slides/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $response = [];

        foreach ($_FILES['files']['name'] as $index => $originalName) {
            $tmpName = $_FILES['files']['tmp_name'][$index];
            $filename = uniqid() . '-' . basename($originalName);
            $target = $uploadDir . $filename;

            if (move_uploaded_file($tmpName, $target)) {
                $response[] = [
                    'src' => '/assets/images/slides/' . $filename
                ];
            }
        }

        echo json_encode([
            'data' => $response
        ]);
        exit;
    }
}