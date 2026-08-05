<?php

use App\Controller\ProfileController;

use App\Middleware\AuthMiddleware;

$router->group([
    'prefix' => '/profile',
    'middleware' => [
        AuthMiddleware::class
    ],
], function ($router) {
    $router->get(
        '',
        [ProfileController::class, 'index']
    );
    $router->post(
        '',
        [ProfileController::class, 'update']
    );
    $router->post(
        '/password',
        [ProfileController::class, 'changePassword']
    );
});