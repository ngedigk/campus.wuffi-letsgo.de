<?php

namespace App\Services;

use App\Dto\CourseInput;
use App\Dto\Module;
use App\Dto\ModuleInput;
use App\Dto\QuestionChoiceInput;
use App\Dto\QuizQuestion;
use App\Dto\QuizQuestionInput;
use App\Dto\Slide;
use App\Dto\SlideInput;

use \Exception;

class AdminCourseManagementService
{
    public function __construct(
        private CourseService $courseService,
        private ModuleService $moduleService,
        private SlideService $slideService,
        private UuidService $uuidService,
        private QuizQuestionService $quizQuestionService,
        private QuestionChoiceService $questionChoicesService,
        private AssetsService $assetsService
    ) {}

    public function createCourse(array $post): void
    {
        $title = trim((string)($post['title'] ?? ''));
        $description = trim((string)($post['description'] ?? ''));
        $prerequisiteCourseId = trim((string)($post['prerequisite_course_id'] ?? ''));
        $sortOrder = (int)trim((string)($post['sort_order'] ?? 0));

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

    public function updateCourse(array $post): void
    {
        $courseId = trim((string)($post['course_id'] ?? ''));
        $title = trim((string)($post['title'] ?? ''));
        $description = trim((string)($post['description'] ?? ''));
        $prerequisiteCourseId = trim((string)($post['prerequisite_course_id'] ?? '')) ?: null;
        $sortOrder = (int)trim((string)($post['sort_order'] ?? 0));

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

    public function createModule(array $post): void
    {
        $courseId = trim((string)($post['course_id'] ?? ''));
        $title = trim((string)($post['title'] ?? ''));
        $sortOrder = (int)trim((string)($post['sort_order'] ?? 0));

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

    public function updateModule(array $post): void
    {
        $moduleId = (int)trim((string)($post['module_id'] ?? ''));
        $title = trim((string)($post['title'] ?? ''));
        $sortOrder = (int)trim((string)($post['sort_order'] ?? 0));

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

    public function createSlide(array $post, array $files): void
    {
        $moduleId = (int)trim((string)($post['module_id'] ?? ''));
        $title = trim((string)($post['title'] ?? ''));
        $audioUrl = trim((string)($post['audio_url'] ?? ''));
        $sortOrder = (int)trim((string)($post['sort_order'] ?? 0));

        if ($title === '') {
            throw new Exception('Bitte geben Sie einen Folientitel an.');
        }

        $uploadedAudio = $this->assetsService->handleAudioUpload($files);
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

    public function updateSlide(array $post, array $files): void
    {
        $slideId = (int)trim((string)($post['slide_id'] ?? ''));
        $title = trim((string)($post['title'] ?? ''));
        $htmlContent = trim((string)($post['html_content'] ?? ''));
        $audioUrl = trim((string)($post['audio_url'] ?? ''));
        $sortOrder = (int)trim((string)($post['sort_order'] ?? 0));

        if ($title === '') {
            throw new Exception('Bitte geben Sie einen Folientitel an.');
        }

        $uploadedAudio = $this->assetsService->handleAudioUpload($files);
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

    public function createQuestion(array $post): void
    {
        $slideId = (int)trim((string)($post['slide_id'] ?? ''));
        $questionText = trim((string)($post['question_text'] ?? ''));
        $choices = $post['choices'] ?? [];

        if ($questionText === '') {
            throw new Exception('Bitte geben Sie einen Fragen-Text an.');
        }

        if (empty($choices)) {
            throw new Exception('Bitte geben Sie mindestens eine Antwort ein.');
        }

        $this->validateChoices($choices);

        $questionId = $this->quizQuestionService->create(new QuizQuestionInput(
            slideId: $slideId,
            questionText: $questionText
        ));

        foreach ($choices as $choiceData) {
            $this->questionChoicesService->create(new QuestionChoiceInput(
                questionId: $questionId,
                choiceText: trim((string)($choiceData['text'] ?? '')),
                isCorrect: !empty($choiceData['is_correct'])
            ));
        }

        $_SESSION['admin_success'] = "Frage $questionId erstellt.";
    }

    public function updateQuestion(array $post): void
    {
        $questionId = (int)trim((string)($post['question_id'] ?? ''));
        $slideId = (int)trim((string)($post['slide_id'] ?? ''));
        $questionText = trim((string)($post['question_text'] ?? ''));
        $choices = $post['choices'] ?? [];

        if ($questionId === 0 || $questionText === '') {
            throw new Exception('Bitte geben Sie einen gültigen Fragen-Text an.');
        }

        if (empty($choices)) {
            throw new Exception('Bitte geben Sie mindestens eine Antwort ein.');
        }

        $this->validateChoices($choices);

        $this->quizQuestionService->update(new QuizQuestion(
            id: $questionId,
            slideId: $slideId,
            questionText: $questionText
        ));

        try {
            $this->questionChoicesService->deleteByQuestionId($questionId);
            foreach ($choices as $choiceData) {
                $this->questionChoicesService->create(new QuestionChoiceInput(
                    questionId: $questionId,
                    choiceText: trim((string)($choiceData['text'] ?? '')),
                    isCorrect: !empty($choiceData['is_correct'])
                ));
            }
        } catch (Exception $e) {
            throw new Exception('Fehler beim Aktualisieren der Antworten: ' . $e->getMessage());
        }

        $_SESSION['admin_success'] = 'Frage aktualisiert.';
    }

    public function deleteQuestion(array $post): void
    {
        $questionId = (int)trim((string)($post['question_id'] ?? ''));
        $this->quizQuestionService->delete($questionId);
        $_SESSION['admin_success'] = 'Frage gelöscht.';
    }

    public function deleteSlide(array $post): void
    {
        $slideId = (int)trim((string)($post['slide_id'] ?? ''));
        $this->slideService->delete($slideId);
        $_SESSION['admin_success'] = 'Folie gelöscht.';
    }

    public function deleteModule(array $post): void
    {
        $moduleId = (int)trim((string)($post['module_id'] ?? ''));
        $this->moduleService->delete($moduleId);
        $_SESSION['admin_success'] = 'Modul gelöscht.';
    }

    public function deleteCourse(array $post): void
    {
        $courseId = trim((string)($post['course_id'] ?? ''));
        $this->courseService->delete($courseId);
        $_SESSION['admin_success'] = 'Kurs gelöscht.';
    }

    public function handleUploadImage(): string
    {
        return $this->assetsService->handleUploadImage();
    }

    public function handleDeleteImage(): string
    {
        return $this->assetsService->handleDeleteImage();
    }

    private function validateChoices(array $choices): void
    {
        $hasCorrect = false;

        foreach ($choices as $choice) {
            if (trim((string)($choice['text'] ?? '')) === '') {
                throw new Exception('Antwort Text darf nicht leer sein.');
            }
            if (!empty($choice['is_correct'])) {
                $hasCorrect = true;
            }
        }

        if (!$hasCorrect) {
            throw new Exception('Bitte markieren Sie mindestens eine korrekte Antwort.');
        }
    }
}
