<?php

use App\Controller\Admin\AdminDashboardController;

use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;

require __DIR__ . '/admin/courses.php';
require __DIR__ . '/admin/users.php';
require __DIR__ . '/admin/access-codes.php';
require __DIR__ . '/admin/registration-codes.php';
require __DIR__ . '/admin/audio-assets.php';

$router->group([
    'prefix' => '/admin',
    'middleware' => [
        AuthMiddleware::class,
        AdminMiddleware::class
    ],
], function ($router) {
    $router->get(
        '',
        [AdminDashboardController::class, 'render']
    );
});