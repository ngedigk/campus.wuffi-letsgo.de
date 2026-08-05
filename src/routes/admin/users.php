<?php

use App\Controller\Admin\AdminUsersController;

use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;

$router->group([
    'prefix' => '/admin/users',
    'middleware' => [
        AuthMiddleware::class,
        AdminMiddleware::class
    ],
], function ($router) {
    $router->get(
        '',
        [AdminUsersController::class, 'render']
    );
    $router->post(
        '/grant-admin',
        [AdminUsersController::class, 'grantAdmin']
    );
    $router->post(
        '/revoke-admin',
        [AdminUsersController::class, 'revokeAdmin']
    );
    $router->post(
        '/verify',
        [AdminUsersController::class, 'manuallyVerify']
    );
    $router->post(
        '/resend-verification',
        [AdminUsersController::class, 'resendVerificationEmail']
    );
});