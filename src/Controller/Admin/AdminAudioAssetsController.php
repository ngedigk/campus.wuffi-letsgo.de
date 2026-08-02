<?php

namespace App\Controller\Admin;

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

class AdminAudioAssetsController extends AdminPageController
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
        $context['additionalJs'][] = [
            'src' => '/assets/js/admin/audio-assets.js',
            'type' => 'module'
        ];

        $viewData = [
            ...$context,
            'activePage' => 'audio-assets',
            'breadcrumb' => [
                [
                    'url' => '',
                    'title' => 'Audio Assets'
                ],
            ],
            'audioFiles' => $this->assetsService->getAudioFiles(),
            'pageTitle' => 'Audio Assets'
        ];

        $this->viewRenderer->renderWithAdminTemplate('admin/audio-assets', $viewData);
    }

    public function handlePost(string $action): void
    {
        switch ($action) {
            case 'delete_audio_asset':
                $this->handleDeleteAudioAsset();
                break;
            default:
                throw new Exception('Nicht unterstützte Admin-Aktion.');
        }
    }

    private function handleDeleteAudioAsset(): void
    {
        $filename = trim($_POST['audio_asset_filename'] ?? '');

        if ($filename === '') {
            throw new Exception('Bitte geben Sie einen Dateinamen an.');
        }

        $this->assetsService->deleteAudioAsset($filename);
        $this->slideService->deleteAudioAssetFromSlides($filename);

        $_SESSION['admin_success'] = 'Audio Asset entfernt.';
    }
}