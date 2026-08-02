<?php

namespace App\Controller\Admin;

use App\Services\AdminContextService;
use App\Services\AssetsService;
use App\Services\AuthService;
use App\Services\SlideService;

use App\Helpers\ViewRenderer;

use \Exception;

class AdminAudioAssetsController extends AdminPageController
{
    public function __construct(
        protected SlideService $slideService,
        protected ViewRenderer $viewRenderer,
        protected AuthService $authService,
        protected AdminContextService $adminContextService,
        private AssetsService $assetsService
    ) {
        parent::__construct($adminContextService, $authService);
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