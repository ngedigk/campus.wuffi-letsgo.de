<?php
require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/Container.php';

$container = Container::getInstance();

// Entrypoint
$app = $container->get(AuthController::class);
$app->handle();