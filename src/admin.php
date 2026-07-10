<?php

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/uuid.php';

require_once __DIR__ . '/repositories/CourseRepository.php';
require_once __DIR__ . '/repositories/AccessCodeRepository.php';
require_once __DIR__ . '/repositories/UserRepository.php';

requireLogin();

if (!isAdmin($pdo)) {
    $_SESSION['admin_error'] = 'You do not have permission to manage admin features.';
    header('Location: index.php');
    exit;
}

$userRepository = new UserRepository($pdo);
$courseRepository = new CourseRepository($pdo);
$accessCodeRepository = new AccessCodeRepository($pdo);

$action = $_POST['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        switch ($action) {
            case 'grant_admin':
                $email = strtolower(trim($_POST['email'] ?? ''));

                if ($email === '') {
                    throw new Exception('Please provide an email address.');
                }

                $user = $userRepository->findByEmail($email);

                if (!$user) {
                    throw new Exception('No user with that email exists.');
                }

                $userRepository->setAdmin($user['id'], true);
                $_SESSION['admin_success'] = 'Admin permissions granted.';
                break;

            case 'create_course':
                $title = trim($_POST['title'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $prerequisiteCourseId = trim($_POST['prerequisite_course_id'] ?? '');

                if ($title === '') {
                    throw new Exception('Please provide a course title.');
                }

                $prerequisiteCourseId = $prerequisiteCourseId !== '' ? $prerequisiteCourseId : null;

                $courseRepository->create(
                    generateUuid(),
                    $title,
                    $description,
                    $prerequisiteCourseId
                );

                $_SESSION['admin_success'] = 'Course created.';
                break;

            case 'create_access_code':
                $code = strtoupper(trim($_POST['code'] ?? ''));
                $courseId = trim($_POST['course_id'] ?? '');

                if ($code === '' || $courseId === '') {
                    throw new Exception('Please provide both an access code and a course.');
                }

                if ($accessCodeRepository->existsByCode($code)) {
                    throw new Exception('That access code already exists.');
                }

                $accessCodeRepository->create($code, $courseId);
                $_SESSION['admin_success'] = 'Access code created.';
                break;

            default:
                throw new Exception('Unsupported admin action.');
        }
    } catch (Throwable $e) {
        $_SESSION['admin_error'] = $e->getMessage();
    }
}

$user = currentUser($pdo);
$isAdmin = isAdmin($pdo);

$adminError = $_SESSION['admin_error'] ?? null;
$adminSuccess = $_SESSION['admin_success'] ?? null;

$accessCodes = $accessCodeRepository->list();

ob_start();
?>
<?php require_once __DIR__ . '/views/admin-dashboard.php'; ?>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/template.php';
?>