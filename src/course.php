<?php
use App\Container\Container;
use App\Services\AuthService;
use App\Controller\CourseController;

require __DIR__ . '/bootstrap.php';

$container = Container::getInstance();
$authService = $container->get(AuthService::class);
$authService->requireLogin();

$courseUuid = trim(($_GET['id'] ?? ''));
$moduleIndex = (int)($_GET['module'] ?? 0);
$slideIndex = (int)($_GET['slide'] ?? 0);

$courseController = $container->get(CourseController::class);
$courseController->handle($courseUuid, $moduleIndex, $slideIndex);
exit;