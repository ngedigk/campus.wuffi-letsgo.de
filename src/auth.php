<?php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/services/AuthService.php';

$pdo = Database::getInstance();
(new AuthService($pdo))->start();