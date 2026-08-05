<?php

namespace App\Controller\Admin;

use App\Helpers\Redirect;
use App\Services\AdminContextService;
use App\Services\AssetsService;
use App\Services\AuthService;
use App\Services\SlideService;

use App\Helpers\ViewRenderer;

use \Exception;

class AdminAudioAssetsController
{
    public function __construct(
        protected SlideService $slideService,
        protected ViewRenderer $viewRenderer,
        protected AuthService $authService,
        protected AdminContextService $adminContextService,
        private AssetsService $assetsService
    ) {}

    public function render(): void
    {
        $context = $this->adminContextService->buildContext(
            $this->authService->currentUser()
        );

        $context['additionalJs'][] = [
            'src' => '/assets/js/admin/audio-assets.js',
            'type' => 'module'
        ];

        $viewData = array_merge(
            $context,
            [
                'activePage' => 'audio-assets',
                'breadcrumb' => [
                    [
                        'url' => '',
                        'title' => 'Audio Assets'
                    ],
                ],
                'audioFiles' => $this->assetsService->getAudioFiles(),
                'pageTitle' => 'Audio Assets'
            ]
        );

        $this->viewRenderer->renderWithAdminTemplate('admin/audio-assets', $viewData);
    }

    public function deleteAudioAsset(string $assetFilename): void
    {
        if ($assetFilename === '') {
            throw new Exception('Bitte geben Sie einen Dateinamen an.');
        }

        $this->assetsService->deleteAudioAsset($assetFilename);
        $this->slideService->deleteAudioAssetFromSlides($assetFilename);

        $_SESSION['admin_success'] = 'Audio Asset entfernt.';

        Redirect::to('/admin/audio-assets');
    }
}