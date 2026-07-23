<?php
header('Content-Type: application/json');

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/Database.php';

$pdo = Database::getInstance();

if (!isAdmin($pdo)) {
    $_SESSION['admin_error'] = 'You do not have permission to manage admin features.';
    header('Location: index.php');
    exit;
}

if (!isset($_FILES['files'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No file uploaded']);
    exit;
}

$uploadDir = __DIR__ . '/assets/images/slides/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0775, true);
}

$response = [];

foreach ($_FILES['files']['name'] as $index => $originalName) {

    $tmpName = $_FILES['files']['tmp_name'][$index];

    $filename = uniqid() . '-' . basename($originalName);

    $target = $uploadDir . $filename;

    if (move_uploaded_file($tmpName, $target)) {
        $response[] = [
            'src' => '/assets/images/slides/' . $filename
        ];
    }
}

echo json_encode([
    'data' => $response
]);