<?php

namespace App\Services;

use App\Contracts\Database\TransactionManagerInterface;

use App\Dto\Course;
use App\Dto\CourseInput;
use App\Dto\Module;
use App\Dto\ModuleInput;
use App\Dto\QuestionChoice;
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
        private QuizQuestionService $quizQuestionService,
        private QuestionChoiceService $questionChoicesService,
        private AssetsService $assetsService,
        private TransactionManagerInterface $transactionManager
    ) {}

    public function getCourseEditorData(string $courseUuid): array {
        $course = $this->courseService->getWithDetails($courseUuid);

        return [
            'selectedCourse' => $course,
            'pageTitle' => "Kurs bearbeiten: {$course->title}",
            'breadcrumb' => [
                [
                    'url' => "/admin/courses/{$course->uuid}",
                    'title' => "Kurs: {$course->title}"
                ]
            ]
        ];
    }
    
    public function getModuleEditorData(string $courseUuid, int $moduleId): array {
        $course = $this->courseService->getWithDetails($courseUuid);
        $module = $this->findModule($course, $moduleId);

        return [
            'selectedCourse' => $course,
            'selectedModule' => $module,
            'audioFiles' => $this->assetsService->getAudioFiles(),
            'pageTitle' => "Modul bearbeiten: {$module->title}",
            'breadcrumb' => [
                [
                    'url' => "/admin/courses/{$course->uuid}",
                    'title' => "Kurs: {$course->title}"
                ],
                [
                    'url' => "/admin/courses/{$course->uuid}/modules/{$module->id}",
                    'title' => "Modul: {$module->title}"
                ]
            ]
        ];
    }

    public function getSlideEditorData(string $courseUuid, int $moduleId, int $slideId): array {
        $course = $this->courseService->getWithDetails($courseUuid);
        $module = $this->findModule($course, $moduleId);
        $slide = $this->findSlide($module, $slideId);

        return array_merge(
            $this->getQuizEditorData($slide->id),
            [
                'selectedCourse' => $course,
                'selectedModule' => $module,
                'selectedSlide' => $slide,
                'slideAssets' => $this->assetsService->getSlideAssets(),
                'audioFiles' => $this->assetsService->getAudioFiles(),
                'pageTitle' => "Folie bearbeiten: {$slide->title}",
                'breadcrumb' => [
                    [
                        'url' => "/admin/courses/{$course->uuid}",
                        'title' => "Kurs: {$course->title}"
                    ], [
                        'url' => "/admin/courses/{$course->uuid}/modules/{$module->id}",
                        'title' => "Modul: {$module->title}"
                    ], [
                        'url' => "/admin/courses/{$course->uuid}/modules/{$module->id}/slides/{$slide->id}",
                        'title' => "Folie: {$slide->title}"
                    ]
                ]
            ]
        );
    }

    private function getQuizEditorData(int $slideId): array {
        if (!$this->slideService->hasQuiz($slideId)) {
            return [
                'quizQuestions' => [],
                'quizChoicesByQuestion' => []
            ];
        }
        
        $questions = $this->quizQuestionService->getBySlideId($slideId);
        $choicesByQuestion = [];

        foreach ($questions as $question) {
            $choicesByQuestion[$question->id] = $this->questionChoicesService->getByQuestionId($question->id);
        }
        
        return [
            'quizQuestions' => $questions,
            'quizChoicesByQuestion' => $choicesByQuestion
        ];
    }

    public function createCourse(CourseInput $course): Course
    {
        if ($course->title === '') {
            throw new Exception('Bitte geben Sie einen Kursnamen an.');
        }

        return $this->courseService->create($course);
    }

    public function updateCourse(Course $course): Course
    {
        if ($course->title === '') {
            throw new Exception('Bitte geben Sie einen Kursnamen an.');
        }

        return $this->courseService->update($course);
    }

    public function createModule(ModuleInput $module): Module
    {
        if ($module->title === '') {
            throw new Exception('Bitte geben Sie einen Modulnamen an.');
        }
        return $this->moduleService->create($module);
    }

    public function updateModule(Module $module): Module
    {
        if ($module->title === '') {
            throw new Exception('Bitte geben Sie einen Modulnamen an.');
        }
        return $this->moduleService->update($module);
    }

    public function createSlide(SlideInput $slide): Slide
    {
        if ($slide->title === '') {
            throw new Exception('Bitte geben Sie einen Folientitel an.');
        }
        return $this->slideService->create($slide);
    }

    public function updateSlide(Slide $slide): Slide
    {
        if ($slide->title === '') {
            throw new Exception('Bitte geben Sie einen Folientitel an.');
        }
        return $this->slideService->update($slide);
    }

    public function createQuestion(QuizQuestionInput $input): QuizQuestion
    {
        if ($input->questionText === '') {
            throw new Exception('Bitte geben Sie einen Fragen-Text an.');
        }
        
        if ($input->choices === []) {
            throw new Exception('Bitte geben Sie mindestens eine Antwort ein.');
        }
        
        $this->validateChoices($input->choices);

        return $this->transactionManager->run(function () use ($input) {
            $question = $this->quizQuestionService->create($input);
            
            foreach ($input->choices as $choice) {
                $this->questionChoicesService->create(
                    $question->id,
                    new QuestionChoiceInput(
                        choiceText: $choice->choiceText,
                        isCorrect: $choice->isCorrect
                    )
                );
            }

            return $question;
        });
    }

    public function updateQuestion(QuizQuestion $question): QuizQuestion
    {
        if ($question->questionText === '') {
            throw new Exception('Bitte geben Sie einen gültigen Fragen-Text an.');
        }

        if (empty($question->choices)) {
            throw new Exception('Bitte geben Sie mindestens eine Antwort ein.');
        }

        $this->validateChoices($question->choices);

        return $this->transactionManager->run(
            function () use ($question): QuizQuestion {
                $questionUpdate = $this->quizQuestionService->update($question);

                $this->questionChoicesService->deleteByQuestionId($question->id);

                foreach ($question->choices as $choice) {
                    $this->questionChoicesService->create(
                        $question->id,
                        new QuestionChoiceInput(
                            choiceText: $choice->choiceText,
                            isCorrect: $choice->isCorrect
                        )
                    );
                }

                return $questionUpdate;
            }
        );
    }

    public function deleteQuestion(int $questionId): void
    {
        $this->quizQuestionService->delete($questionId);
    }

    public function deleteSlide(int $slideId): void
    {
        $this->slideService->delete($slideId);
    }

    public function deleteModule(int $moduleId): void
    {
        $this->moduleService->delete($moduleId);
    }

    public function deleteCourse(string $courseUuid): void
    {
        $this->courseService->delete($courseUuid);
    }

    public function uploadImage(): string
    {
        return $this->assetsService->handleUploadImage();
    }

    public function deleteImage(): string
    {
        return $this->assetsService->handleDeleteImage();
    }
    
    private function findModule(Course $course, int $moduleId): Module {
        foreach ($course->modules as $module) {
            if ($module->id === $moduleId) {
                return $module;
            }
        }
        
        throw new \RuntimeException("Module {$moduleId} not found.");
    }
    
    private function findSlide(Module $module, int $slideId): Slide {
        foreach ($module->slides as $slide) {
            if ($slide->id === $slideId) {
                return $slide;
            }
        }
        
        throw new \RuntimeException("Slide {$slideId} not found.");
    }

    private function validateChoices(array $choices): void
    {
        $hasCorrect = false;

        /** @var QuestionChoice $choice */
        foreach ($choices as $choice) {
            if ($choice->choiceText === '') {
                throw new Exception('Antwort Text darf nicht leer sein.');
            }
            if ($choice->isCorrect) {
                $hasCorrect = true;
            }
        }

        if (!$hasCorrect) {
            throw new Exception('Bitte markieren Sie mindestens eine korrekte Antwort.');
        }
    }
}
