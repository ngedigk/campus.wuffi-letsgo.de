<?php

namespace App\Tests\Services;

use App\Services\AdminCourseManagementService;
use App\Services\CourseService;
use App\Services\ModuleService;
use App\Services\SlideService;
use App\Services\QuizQuestionService;
use App\Services\QuestionChoiceService;
use App\Services\AssetsService;

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

use App\Exceptions\CourseModuleNotFoundException;
use App\Exceptions\CourseSlideNotFoundException;
use \InvalidArgumentException;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AdminCourseManagementServiceTest extends TestCase
{
    private CourseService&MockObject $courseService;
    private ModuleService&MockObject $moduleService;
    private SlideService&MockObject $slideService;
    private QuizQuestionService&MockObject $quizQuestionService;
    private QuestionChoiceService&MockObject $questionChoiceService;
    private AssetsService&MockObject $assetsService;
    private TransactionManagerInterface&MockObject $transactionManager;

    private AdminCourseManagementService $service;

    protected function setUp(): void
    {
        $this->courseService = $this->createMock(CourseService::class);
        $this->moduleService = $this->createMock(ModuleService::class);
        $this->slideService = $this->createMock(SlideService::class);
        $this->quizQuestionService = $this->createMock(QuizQuestionService::class);
        $this->questionChoiceService = $this->createMock(QuestionChoiceService::class);
        $this->assetsService = $this->createMock(AssetsService::class);
        $this->transactionManager = $this->createMock(TransactionManagerInterface::class);

        $this->service = new AdminCourseManagementService(
            $this->courseService,
            $this->moduleService,
            $this->slideService,
            $this->quizQuestionService,
            $this->questionChoiceService,
            $this->assetsService,
            $this->transactionManager,
        );
    }

    // ====================================================================
    // Helpers
    // ====================================================================

    private function createCourse(): Course
    {
        return new Course(
            uuid: 'course-1',
            title: 'My Course',
            description: 'desc',
            prerequisiteCourseId: null,
            sortOrder: 1,
        );
    }

    private function createCourseWithModule(): Course
    {
        return new Course(
            uuid: 'course-1',
            title: 'My Course',
            description: 'desc',
            prerequisiteCourseId: null,
            sortOrder: 1,
            modules: [
                new Module(id: 5, title: 'Module 1', sortOrder: 1),
            ],
        );
    }

    private function createCourseWithModuleAndSlide(): Course
    {
        return new Course(
            uuid: 'course-1',
            title: 'My Course',
            description: 'desc',
            prerequisiteCourseId: null,
            sortOrder: 1,
            modules: [
                new Module(
                    id: 5,
                    title: 'Module 1',
                    sortOrder: 1,
                    slides: [
                        new Slide(id: 10, title: 'Slide 1', htmlContent: '', audioUrl: null, sortOrder: 1),
                    ],
                ),
            ],
        );
    }

    // ====================================================================
    // Course
    // ====================================================================

    public function testCreateCourseThrowsExceptionWhenTitleIsEmpty(): void
    {
        $input = new CourseInput(
            title: '',
            description: 'desc',
            prerequisiteCourseId: null,
            sortOrder: 0,
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Bitte geben Sie einen Kursnamen an.');

        $this->service->createCourse($input);
    }

    public function testCreateCourseDelegatesToCourseServiceWhenTitleIsValid(): void
    {
        $input = new CourseInput(
            title: 'My Course',
            description: 'A great course',
            prerequisiteCourseId: null,
            sortOrder: 1,
        );

        $expectedCourse = new Course(
            uuid: 'course-1',
            title: 'My Course',
            description: 'A great course',
            prerequisiteCourseId: null,
            sortOrder: 1,
        );

        $this->courseService
            ->expects($this->once())
            ->method('create')
            ->with($input)
            ->willReturn($expectedCourse);

        $result = $this->service->createCourse($input);

        $this->assertSame($expectedCourse, $result);
    }

    public function testUpdateCourseThrowsExceptionWhenTitleIsEmpty(): void
    {
        $course = new Course(
            uuid: 'course-1',
            title: '',
            description: 'desc',
            prerequisiteCourseId: null,
            sortOrder: 1,
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Bitte geben Sie einen Kursnamen an.');

        $this->service->updateCourse($course);
    }

    public function testUpdateCourseDelegatesToCourseServiceWhenTitleIsValid(): void
    {
        $course = new Course(
            uuid: 'course-1',
            title: 'Updated Course',
            description: 'Updated desc',
            prerequisiteCourseId: null,
            sortOrder: 1,
        );

        $this->courseService
            ->expects($this->once())
            ->method('update')
            ->with($course)
            ->willReturn($course);

        $result = $this->service->updateCourse($course);

        $this->assertSame($course, $result);
    }

    // ====================================================================
    // Module
    // ====================================================================

    public function testCreateModuleThrowsExceptionWhenTitleIsEmpty(): void
    {
        $input = new ModuleInput(
            courseId: 'course-1',
            title: '',
            sortOrder: 0,
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Bitte geben Sie einen Modulnamen an.');

        $this->service->createModule($input);
    }

    public function testCreateModuleDelegatesToModuleServiceWhenTitleIsValid(): void
    {
        $input = new ModuleInput(
            courseId: 'course-1',
            title: 'My Module',
            sortOrder: 1,
        );

        $expectedModule = new Module(
            id: 1,
            title: 'My Module',
            sortOrder: 1,
        );

        $this->moduleService
            ->expects($this->once())
            ->method('create')
            ->with($input)
            ->willReturn($expectedModule);

        $result = $this->service->createModule($input);

        $this->assertSame($expectedModule, $result);
    }

    public function testUpdateModuleThrowsExceptionWhenTitleIsEmpty(): void
    {
        $module = new Module(
            id: 1,
            title: '',
            sortOrder: 1,
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Bitte geben Sie einen Modulnamen an.');

        $this->service->updateModule($module);
    }

    public function testUpdateModuleDelegatesToModuleServiceWhenTitleIsValid(): void
    {
        $module = new Module(
            id: 1,
            title: 'Updated Module',
            sortOrder: 1,
        );

        $this->moduleService
            ->expects($this->once())
            ->method('update')
            ->with($module)
            ->willReturn($module);

        $result = $this->service->updateModule($module);

        $this->assertSame($module, $result);
    }

    // ====================================================================
    // Slide
    // ====================================================================

    public function testCreateSlideThrowsExceptionWhenTitleIsEmpty(): void
    {
        $input = new SlideInput(
            moduleId: 1,
            title: '',
            htmlContent: '<p>content</p>',
            audioUrl: null,
            sortOrder: 0,
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Bitte geben Sie einen Folientitel an.');

        $this->service->createSlide($input);
    }

    public function testCreateSlideDelegatesToSlideServiceWhenTitleIsValid(): void
    {
        $input = new SlideInput(
            moduleId: 1,
            title: 'My Slide',
            htmlContent: '<p>content</p>',
            audioUrl: null,
            sortOrder: 1,
        );

        $expectedSlide = new Slide(
            id: 1,
            title: 'My Slide',
            htmlContent: '<p>content</p>',
            audioUrl: null,
            sortOrder: 1,
        );

        $this->slideService
            ->expects($this->once())
            ->method('create')
            ->with($input)
            ->willReturn($expectedSlide);

        $result = $this->service->createSlide($input);

        $this->assertSame($expectedSlide, $result);
    }

    public function testUpdateSlideThrowsExceptionWhenTitleIsEmpty(): void
    {
        $slide = new Slide(
            id: 1,
            title: '',
            htmlContent: '<p>content</p>',
            audioUrl: null,
            sortOrder: 1,
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Bitte geben Sie einen Folientitel an.');

        $this->service->updateSlide($slide);
    }

    public function testUpdateSlideDelegatesToSlideServiceWhenTitleIsValid(): void
    {
        $slide = new Slide(
            id: 1,
            title: 'Updated Slide',
            htmlContent: '<p>updated content</p>',
            audioUrl: null,
            sortOrder: 1,
        );

        $this->slideService
            ->expects($this->once())
            ->method('update')
            ->with($slide)
            ->willReturn($slide);

        $result = $this->service->updateSlide($slide);

        $this->assertSame($slide, $result);
    }

    // ====================================================================
    // Question
    // ====================================================================

    public function testCreateQuestionThrowsExceptionWhenQuestionTextIsEmpty(): void
    {
        $input = new QuizQuestionInput(
            slideId: 1,
            questionText: '',
            choices: [
                new QuestionChoiceInput(choiceText: 'A', isCorrect: true),
            ],
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Bitte geben Sie einen Fragen-Text an.');

        $this->service->createQuestion($input);
    }

    public function testCreateQuestionThrowsExceptionWhenChoicesAreEmpty(): void
    {
        $input = new QuizQuestionInput(
            slideId: 1,
            questionText: 'What is 2+2?',
            choices: [],
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Bitte geben Sie mindestens eine Antwort ein.');

        $this->service->createQuestion($input);
    }

    public function testCreateQuestionThrowsExceptionWhenNoCorrectAnswerMarked(): void
    {
        $input = new QuizQuestionInput(
            slideId: 1,
            questionText: 'What is 2+2?',
            choices: [
                new QuestionChoiceInput(choiceText: 'A', isCorrect: false),
                new QuestionChoiceInput(choiceText: 'B', isCorrect: false),
            ],
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Bitte markieren Sie mindestens eine korrekte Antwort.');

        $this->service->createQuestion($input);
    }

    public function testCreateQuestionThrowsExceptionWhenChoiceTextIsEmpty(): void
    {
        $input = new QuizQuestionInput(
            slideId: 1,
            questionText: 'What is 2+2?',
            choices: [
                new QuestionChoiceInput(choiceText: '', isCorrect: true),
            ],
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Antwort Text darf nicht leer sein.');

        $this->service->createQuestion($input);
    }

    public function testCreateQuestionCreatesQuestionAndChoicesInsideTransaction(): void
    {
        $input = new QuizQuestionInput(
            slideId: 1,
            questionText: 'What is 2+2?',
            choices: [
                new QuestionChoiceInput(choiceText: '4', isCorrect: true),
                new QuestionChoiceInput(choiceText: '5', isCorrect: false),
            ],
        );

        $createdQuestion = new QuizQuestion(
            id: 10,
            questionText: 'What is 2+2?',
        );

        $insideTransaction = false;

        $this->transactionManager
            ->expects($this->once())
            ->method('run')
            ->willReturnCallback(function (callable $callback) use (&$insideTransaction) {
                $insideTransaction = true;
                try {
                    return $callback();
                } finally {
                    $insideTransaction = false;
                }
            });

        $this->quizQuestionService
            ->expects($this->once())
            ->method('create')
            ->with($input)
            ->willReturnCallback(function () use (&$insideTransaction, $createdQuestion) {
                $this->assertTrue($insideTransaction, 'Question creation must happen inside the transaction.');
                return $createdQuestion;
            });

        $expectedCalls = [
            [10, new QuestionChoiceInput(choiceText: '4', isCorrect: true)],
            [10, new QuestionChoiceInput(choiceText: '5', isCorrect: false)],
        ];
        $callIndex = 0;

        $this->questionChoiceService
            ->expects($this->exactly(2))
            ->method('create')
            ->willReturnCallback(function ($questionId, $choiceInput) use (&$callIndex, &$insideTransaction, $expectedCalls) {
                $this->assertTrue($insideTransaction, 'Choice creation must happen inside the transaction.');
                [$expectedQuestionId, $expectedChoiceInput] = $expectedCalls[$callIndex];
                $this->assertSame($expectedQuestionId, $questionId);
                $this->assertSame($expectedChoiceInput->choiceText, $choiceInput->choiceText);
                $this->assertSame($expectedChoiceInput->isCorrect, $choiceInput->isCorrect);
                $callIndex++;
                return new QuestionChoice(id: $callIndex + 100, questionId: $questionId, choiceText: $choiceInput->choiceText, isCorrect: $choiceInput->isCorrect);
            });

        $result = $this->service->createQuestion($input);

        $this->assertSame($createdQuestion, $result);
    }

    public function testCreateQuestionPropagatesExceptionFromChoiceCreation(): void
    {
        $input = new QuizQuestionInput(
            slideId: 1,
            questionText: 'What is 2+2?',
            choices: [
                new QuestionChoiceInput(choiceText: '4', isCorrect: true),
            ],
        );

        $createdQuestion = new QuizQuestion(
            id: 10,
            questionText: 'What is 2+2?',
        );

        $testException = new \RuntimeException('Choice creation failed');

        $this->quizQuestionService
            ->expects($this->once())
            ->method('create')
            ->with($input)
            ->willReturn($createdQuestion);

        $this->questionChoiceService
            ->expects($this->once())
            ->method('create')
            ->willThrowException($testException);

        $this->transactionManager
            ->expects($this->once())
            ->method('run')
            ->willReturnCallback(fn (callable $callback) => $callback());

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Choice creation failed');

        $this->service->createQuestion($input);
    }

    public function testUpdateQuestionThrowsExceptionWhenQuestionTextIsEmpty(): void
    {
        $question = new QuizQuestion(
            id: 10,
            questionText: '',
            choices: [
                new QuestionChoice(id: 1, questionId: 10, choiceText: 'A', isCorrect: true),
            ],
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Bitte geben Sie einen gültigen Fragen-Text an.');

        $this->service->updateQuestion($question);
    }

    public function testUpdateQuestionThrowsExceptionWhenChoicesAreEmpty(): void
    {
        $question = new QuizQuestion(
            id: 10,
            questionText: 'What is 2+2?',
            choices: [],
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Bitte geben Sie mindestens eine Antwort ein.');

        $this->service->updateQuestion($question);
    }

    public function testUpdateQuestionThrowsExceptionWhenNoCorrectAnswerMarked(): void
    {
        $question = new QuizQuestion(
            id: 10,
            questionText: 'What is 2+2?',
            choices: [
                new QuestionChoice(id: 1, questionId: 10, choiceText: 'A', isCorrect: false),
                new QuestionChoice(id: 2, questionId: 10, choiceText: 'B', isCorrect: false),
            ],
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Bitte markieren Sie mindestens eine korrekte Antwort.');

        $this->service->updateQuestion($question);
    }

    public function testUpdateQuestionThrowsExceptionWhenChoiceTextIsEmpty(): void
    {
        $question = new QuizQuestion(
            id: 10,
            questionText: 'What is 2+2?',
            choices: [
                new QuestionChoice(id: 1, questionId: 10, choiceText: '', isCorrect: true),
            ],
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Antwort Text darf nicht leer sein.');

        $this->service->updateQuestion($question);
    }

    public function testUpdateQuestionUpdatesQuestionAndRecreatesChoicesInsideTransaction(): void
    {
        $question = new QuizQuestion(
            id: 10,
            questionText: 'What is 2+2?',
            choices: [
                new QuestionChoice(id: 1, questionId: 10, choiceText: '4', isCorrect: true),
                new QuestionChoice(id: 2, questionId: 10, choiceText: '5', isCorrect: false),
            ],
        );

        $insideTransaction = false;

        $this->transactionManager
            ->expects($this->once())
            ->method('run')
            ->willReturnCallback(function (callable $callback) use (&$insideTransaction) {
                $insideTransaction = true;
                try {
                    return $callback();
                } finally {
                    $insideTransaction = false;
                }
            });

        $this->quizQuestionService
            ->expects($this->once())
            ->method('update')
            ->with($question)
            ->willReturnCallback(function () use (&$insideTransaction, $question) {
                $this->assertTrue($insideTransaction, 'Question update must happen inside the transaction.');
                return $question;
            });

        $this->questionChoiceService
            ->expects($this->once())
            ->method('deleteByQuestionId')
            ->with(10)
            ->willReturnCallback(function () use (&$insideTransaction) {
                $this->assertTrue($insideTransaction, 'Choice deletion must happen inside the transaction.');
            });

        $expectedCalls = [
            [10, new QuestionChoiceInput(choiceText: '4', isCorrect: true)],
            [10, new QuestionChoiceInput(choiceText: '5', isCorrect: false)],
        ];
        $callIndex = 0;

        $this->questionChoiceService
            ->expects($this->exactly(2))
            ->method('create')
            ->willReturnCallback(function ($questionId, $choiceInput) use (&$callIndex, &$insideTransaction, $expectedCalls) {
                $this->assertTrue($insideTransaction, 'Choice creation must happen inside the transaction.');
                [$expectedQuestionId, $expectedChoiceInput] = $expectedCalls[$callIndex];
                $this->assertSame($expectedQuestionId, $questionId);
                $this->assertSame($expectedChoiceInput->choiceText, $choiceInput->choiceText);
                $this->assertSame($expectedChoiceInput->isCorrect, $choiceInput->isCorrect);
                $callIndex++;
                return new QuestionChoice(id: $callIndex + 200, questionId: $questionId, choiceText: $choiceInput->choiceText, isCorrect: $choiceInput->isCorrect);
            });

        $result = $this->service->updateQuestion($question);

        $this->assertSame($question, $result);
    }

    public function testUpdateQuestionPropagatesExceptionFromChoiceCreation(): void
    {
        $question = new QuizQuestion(
            id: 10,
            questionText: 'What is 2+2?',
            choices: [
                new QuestionChoice(id: 1, questionId: 10, choiceText: '4', isCorrect: true),
            ],
        );

        $testException = new \RuntimeException('Choice creation failed');

        $this->quizQuestionService
            ->expects($this->once())
            ->method('update')
            ->with($question)
            ->willReturn($question);

        $this->questionChoiceService
            ->expects($this->once())
            ->method('deleteByQuestionId')
            ->with(10);

        $this->questionChoiceService
            ->expects($this->once())
            ->method('create')
            ->willThrowException($testException);

        $this->transactionManager
            ->expects($this->once())
            ->method('run')
            ->willReturnCallback(fn (callable $callback) => $callback());

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Choice creation failed');

        $this->service->updateQuestion($question);
    }

    // ====================================================================
    // Editor data
    // ====================================================================

    public function testGetCourseEditorDataReturnsCourseWithPageTitleAndBreadcrumb(): void
    {
        $course = new Course(
            uuid: 'course-1',
            title: 'My Course',
            description: 'desc',
            prerequisiteCourseId: null,
            sortOrder: 1,
        );

        $this->courseService
            ->expects($this->once())
            ->method('getWithDetails')
            ->with('course-1')
            ->willReturn($course);

        $result = $this->service->getCourseEditorData('course-1');

        $this->assertSame($course, $result['selectedCourse']);
        $this->assertSame('Kurs bearbeiten: My Course', $result['pageTitle']);
        $this->assertCount(1, $result['breadcrumb']);
        $this->assertSame('/admin/courses/course-1', $result['breadcrumb'][0]['url']);
        $this->assertSame('Kurs: My Course', $result['breadcrumb'][0]['title']);
    }

    public function testGetModuleEditorDataReturnsModuleWithPageTitleAndBreadcrumb(): void
    {
        $course = $this->createCourseWithModule();

        $this->courseService
            ->expects($this->once())
            ->method('getWithDetails')
            ->with('course-1')
            ->willReturn($course);

        $this->assetsService
            ->expects($this->once())
            ->method('getAudioFiles')
            ->willReturn([]);

        $result = $this->service->getModuleEditorData('course-1', 5);

        $this->assertSame($course, $result['selectedCourse']);
        $this->assertSame($course->modules[0], $result['selectedModule']);
        $this->assertSame('Modul bearbeiten: Module 1', $result['pageTitle']);
        $this->assertCount(2, $result['breadcrumb']);
        $this->assertSame('/admin/courses/course-1/modules/5', $result['breadcrumb'][1]['url']);
        $this->assertSame('Modul: Module 1', $result['breadcrumb'][1]['title']);
        $this->assertSame([], $result['audioFiles']);
    }

    public function testGetModuleEditorDataThrowsExceptionWhenModuleNotFound(): void
    {
        $course = $this->createCourse();
        $course->modules = [];

        $this->courseService
            ->expects($this->once())
            ->method('getWithDetails')
            ->with('course-1')
            ->willReturn($course);

        $this->expectException(CourseModuleNotFoundException::class);
        $this->expectExceptionMessage('Module 99 not found.');

        $this->service->getModuleEditorData('course-1', 99);
    }

    public function testGetSlideEditorDataReturnsSlideWithPageTitleAndBreadcrumb(): void
    {
        $course = $this->createCourseWithModuleAndSlide();

        $this->courseService
            ->expects($this->once())
            ->method('getWithDetails')
            ->with('course-1')
            ->willReturn($course);

        $this->slideService
            ->expects($this->once())
            ->method('hasQuiz')
            ->with(10)
            ->willReturn(false);

        $this->assetsService
            ->expects($this->once())
            ->method('getSlideAssets')
            ->willReturn([]);

        $this->assetsService
            ->expects($this->once())
            ->method('getAudioFiles')
            ->willReturn([]);

        $result = $this->service->getSlideEditorData('course-1', 5, 10);

        $this->assertSame($course, $result['selectedCourse']);
        $this->assertSame($course->modules[0]->slides[0], $result['selectedSlide']);
        $this->assertSame('Folie bearbeiten: Slide 1', $result['pageTitle']);
        $this->assertCount(3, $result['breadcrumb']);
        $this->assertSame('/admin/courses/course-1/modules/5/slides/10', $result['breadcrumb'][2]['url']);
        $this->assertSame('Folie: Slide 1', $result['breadcrumb'][2]['title']);
        $this->assertSame([], $result['quizQuestions']);
        $this->assertSame([], $result['quizChoicesByQuestion']);
        $this->assertSame($course->modules[0], $result['selectedModule']);
        $this->assertSame([], $result['slideAssets']);
        $this->assertSame([], $result['audioFiles']);
    }

    public function testGetSlideEditorDataWithQuizButNoQuestionsReturnsEmptyQuestionData(): void
    {
        $course = $this->createCourseWithModuleAndSlide();

        $this->courseService
            ->expects($this->once())
            ->method('getWithDetails')
            ->with('course-1')
            ->willReturn($course);

        $this->slideService
            ->expects($this->once())
            ->method('hasQuiz')
            ->with(10)
            ->willReturn(true);

        $this->quizQuestionService
            ->expects($this->once())
            ->method('getBySlideId')
            ->with(10)
            ->willReturn([]);

        $this->questionChoiceService
            ->expects($this->never())
            ->method('getByQuestionId');

        $this->assetsService
            ->expects($this->once())
            ->method('getSlideAssets')
            ->willReturn([]);

        $this->assetsService
            ->expects($this->once())
            ->method('getAudioFiles')
            ->willReturn([]);

        $result = $this->service->getSlideEditorData('course-1', 5, 10);

        $this->assertSame([], $result['quizQuestions']);
        $this->assertSame([], $result['quizChoicesByQuestion']);
    }

    public function testGetSlideEditorDataWithQuizQuestionsFetchesQuestionsAndChoices(): void
    {
        $course = $this->createCourseWithModuleAndSlide();

        $question1 = new QuizQuestion(id: 100, questionText: 'Q1');
        $question2 = new QuizQuestion(id: 101, questionText: 'Q2');

        $choice1 = new QuestionChoice(id: 200, questionId: 100, choiceText: 'A1', isCorrect: true);
        $choice2 = new QuestionChoice(id: 201, questionId: 100, choiceText: 'B1', isCorrect: false);

        $this->courseService
            ->expects($this->once())
            ->method('getWithDetails')
            ->with('course-1')
            ->willReturn($course);

        $this->slideService
            ->expects($this->once())
            ->method('hasQuiz')
            ->with(10)
            ->willReturn(true);

        $this->quizQuestionService
            ->expects($this->once())
            ->method('getBySlideId')
            ->with(10)
            ->willReturn([$question1, $question2]);

        $this->questionChoiceService
            ->expects($this->exactly(2))
            ->method('getByQuestionId')
            ->willReturnMap([
                [100, [$choice1, $choice2]],
                [101, []],
            ]);

        $this->assetsService
            ->expects($this->once())
            ->method('getSlideAssets')
            ->willReturn([]);

        $this->assetsService
            ->expects($this->once())
            ->method('getAudioFiles')
            ->willReturn([]);

        $result = $this->service->getSlideEditorData('course-1', 5, 10);

        $this->assertSame([$question1, $question2], $result['quizQuestions']);
        $this->assertSame([$choice1, $choice2], $result['quizChoicesByQuestion'][100]);
        $this->assertSame([], $result['quizChoicesByQuestion'][101]);
    }

    public function testGetSlideEditorDataThrowsExceptionWhenSlideNotFound(): void
    {
        $course = $this->createCourseWithModuleAndSlide();
        $course->modules[0]->slides = [];

        $this->courseService
            ->expects($this->once())
            ->method('getWithDetails')
            ->with('course-1')
            ->willReturn($course);

        $this->expectException(CourseSlideNotFoundException::class);
        $this->expectExceptionMessage('Slide 99 not found.');

        $this->service->getSlideEditorData('course-1', 5, 99);
    }

    public function testGetSlideEditorDataThrowsExceptionWhenModuleNotFound(): void
    {
        $course = $this->createCourse();
        $course->modules = [];

        $this->courseService
            ->expects($this->once())
            ->method('getWithDetails')
            ->with('course-1')
            ->willReturn($course);

        $this->expectException(CourseModuleNotFoundException::class);
        $this->expectExceptionMessage('Module 99 not found.');

        $this->service->getSlideEditorData('course-1', 99, 10);
    }

    // ====================================================================
    // Delete
    // ====================================================================

    public function testDeleteSlideDelegatesToSlideService(): void
    {
        $this->slideService
            ->expects($this->once())
            ->method('delete')
            ->with(42);

        $this->service->deleteSlide(42);
    }

    public function testDeleteModuleDelegatesToModuleService(): void
    {
        $this->moduleService
            ->expects($this->once())
            ->method('delete')
            ->with(42);

        $this->service->deleteModule(42);
    }

    public function testDeleteCourseDelegatesToCourseService(): void
    {
        $this->courseService
            ->expects($this->once())
            ->method('delete')
            ->with('course-uuid-1');

        $this->service->deleteCourse('course-uuid-1');
    }

    // ====================================================================
    // Assets
    // ====================================================================

    public function testUploadImageDelegatesToAssetsService(): void
    {
        $uploadedUrl = '/uploads/image.png';

        $this->assetsService
            ->expects($this->once())
            ->method('handleUploadImage')
            ->willReturn($uploadedUrl);

        $result = $this->service->uploadImage();

        $this->assertSame($uploadedUrl, $result);
    }

    public function testDeleteImageDelegatesToAssetsService(): void
    {
        $deletedPath = '/uploads/deleted/image.png';

        $this->assetsService
            ->expects($this->once())
            ->method('handleDeleteImage')
            ->willReturn($deletedPath);

        $result = $this->service->deleteImage();

        $this->assertSame($deletedPath, $result);
    }
}
