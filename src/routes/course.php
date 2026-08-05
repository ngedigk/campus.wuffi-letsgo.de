<?php

use App\Controller\CourseController;

use App\Middleware\AuthMiddleware;

$router->group([
    'prefix' => '/course',
    'middleware' => [
        AuthMiddleware::class
    ],
], function ($router) {
    $router->get(
        '',
        [CourseController::class, 'index']
    );

    $router->post(
        '/quiz',
        [CourseController::class, 'submitQuiz']
    );
});