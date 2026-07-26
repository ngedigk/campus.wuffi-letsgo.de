<?php
require __DIR__ . '/bootstrap.php';
require __DIR__ . '/csrf.php';

$container = Container::getInstance();
$authService = $container->get(AuthService::class);
$authService->requireLogin(__DIR__);

$courseUuid = trim(($_GET['id'] ?? ''));
$moduleId = (int)($_GET['module'] ?? 0);
$slideIndex = (int)($_GET['slide'] ?? 0);

$courseController = $container->get(CourseController::class);
$courseController->handle($courseUuid, $moduleId, $slideIndex);
exit;