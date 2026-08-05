<?php

use App\Controller\Admin\AdminRegistrationCodesController;

use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;

$router->group([
    'prefix' => '/admin/registration-codes',
    'middleware' => [
        AuthMiddleware::class,
        AdminMiddleware::class
    ],
], function ($router) {
    $router->get(
        '',
        [AdminRegistrationCodesController::class, 'render']
    );
    $router->post(
        '',
        [AdminRegistrationCodesController::class, 'createRegistrationCode']
    );
    $router->post(
        '/{registrationCodeId}/update',
        [AdminRegistrationCodesController::class, 'updateRegistrationCode']
    );
    $router->post(
        '/{registrationCodeId}/delete',
        [AdminRegistrationCodesController::class, 'deleteRegistrationCode']
    );
});