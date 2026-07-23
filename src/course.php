<?php

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/uuid.php';
require_once __DIR__ . '/Container.php';

$container = Container::getInstance();

$authService = $container->get(AuthService::class);
$authService->requireLogin(__DIR__);

$courseUuid = trim(($_GET['id'] ?? ''));
$moduleId = (int)($_GET['module'] ?? 0);
$slideIndex = (int)($_GET['slide'] ?? 0);

$courseController = $container->get(CourseController::class);
$courseController->handle($courseUuid, $moduleId, $slideIndex);
exit;