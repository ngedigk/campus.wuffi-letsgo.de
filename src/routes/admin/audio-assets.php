<?php

use App\Controller\Admin\AdminAudioAssetsController;
use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;

$router->group([
    'prefix' => '/admin/audio-assets',
    'middleware' => [
        AuthMiddleware::class,
        AdminMiddleware::class
    ],
], function ($router) {
    $router->get(
        '',
        [AdminAudioAssetsController::class, 'render']
    );
    $router->post(
        '/{assetFilename}/delete',
        [AdminAudioAssetsController::class, 'deleteAudioAsset']
    );
});