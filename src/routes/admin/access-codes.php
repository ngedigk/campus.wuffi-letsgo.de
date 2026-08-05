<?php

use App\Controller\Admin\AdminAccessCodesController;

use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;

$router->group([
    'prefix' => '/admin/access-codes',
    'middleware' => [
        AuthMiddleware::class,
        AdminMiddleware::class
    ],
], function ($router) {
    $router->get(
        '',
        [AdminAccessCodesController::class, 'render']
    );
    $router->post(
        '',
        [AdminAccessCodesController::class, 'createAccessCode']
    );
    $router->post(
        '/{accessCodeId}/update',
        [AdminAccessCodesController::class, 'updateAccessCode']
    );
    $router->post(
        '/{accessCodeId}/delete',
        [AdminAccessCodesController::class, 'deleteAccessCode']
    );
});