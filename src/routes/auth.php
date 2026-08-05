<?php

use App\Controller\AuthController;
use App\Controller\ForgotPasswordController;
use App\Controller\RegistrationController;
use App\Controller\ResetPasswordController;

$router->get(
    '/register',
    [RegistrationController::class, 'index']
);
$router->post(
    '/register',
    [RegistrationController::class, 'register']
);

$router->get(
    '/register/verify',
    [RegistrationController::class, 'verify']
);

$router->get(
    '/forgot-password',
    [ForgotPasswordController::class, 'index']
);
$router->post(
    '/forgot-password',
    [ForgotPasswordController::class, 'requestReset']
);

$router->get(
    '/reset-password',
    [ResetPasswordController::class, 'index']
);
$router->post(
    '/reset-password',
    [ResetPasswordController::class, 'reset']
);

$router->post(
    '/login',
    [AuthController::class, 'login']
);
$router->post(
    '/logout',
    [AuthController::class, 'logout']
);