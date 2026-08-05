<?php

require __DIR__ . '/auth.php';
require __DIR__ . '/dashboard.php';
require __DIR__ . '/profile.php';
require __DIR__ . '/course.php';
require __DIR__ . '/admin.php';

use App\Controller\HomeController;

$router->get(
    '/',
    [HomeController::class, 'index']
);