<?php

use App\Controller\DashboardController;

use App\Middleware\AuthMiddleware;

$router->group([
    'prefix' => '',
    'middleware' => [
        AuthMiddleware::class
    ],
], function ($router) {
    $router->post(
        '/redeem',
        [DashboardController::class, 'redeem']
    );
});