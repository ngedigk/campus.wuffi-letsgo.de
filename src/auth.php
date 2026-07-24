<?php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/services/AuthService.php';
require_once __DIR__ . '/Container.php';

$container = Container::getInstance();

$authService = $container->get(AuthService::class);
$authService->start();