<?php

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/rate_limit.php';

require_once __DIR__ . '/repositories/CourseRepository.php';
require_once __DIR__ . '/repositories/ModuleRepository.php';
require_once __DIR__ . '/repositories/SlideRepository.php';
require_once __DIR__ . '/repositories/UserRepository.php';

require_once __DIR__ . '/services/SlideService.php';
require_once __DIR__ . '/services/ModuleService.php';
require_once __DIR__ . '/services/QuizService.php';
require_once __DIR__ . '/services/CourseService.php';
require_once __DIR__ . '/repositories/ProgressRepository.php';
require_once __DIR__ . '/services/ProgressService.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    validateCsrf();

    $email = trim($_POST['email'] ?? '');

    $password = $_POST['password'] ?? '';

    if (isIpBlocked($pdo)) {
        $error = "Zu viele Anmeldeversuche. Bitte versuchen Sie es später nochmal.";
    } else {

        $stmt = $pdo->prepare(
            "SELECT *
            FROM users
            WHERE email = ?"
        );

        $stmt->execute([$email]);

        $user = $stmt->fetch();

        if (
            $user &&
            password_verify($password, $user['password_hash'])
        ) {

            if ((int)$user['email_verified'] !== 1) {
                $error = "Bestätigen Sie bitte erst Ihre E-Mail Adresse.";
            } else {
                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['is_admin'] = (int)$user['is_admin'];

                clearOldAttempts($pdo);

                header("Location: index.php");
                exit;
            }
        }
        else {
            recordFailedLogin($pdo);
            $error = "E-Mail oder Passwort ungültig";
        }
    }
}

$pageTitle = 'Startseite';

ob_start();
?>
<?php if(isLoggedIn()): ?>

    <?php
    requireLogin();

    $user = currentUser($pdo);
    $isAdmin = isAdmin($pdo);

    $redeemError = $_SESSION['redeem_error'] ?? null;
    $redeemSuccess = $_SESSION['redeem_success'] ?? null;
    unset(
        $_SESSION['redeem_error'],
        $_SESSION['redeem_success'],
        $_SESSION['admin_error'],
        $_SESSION['admin_success']
    );

    $courseRepository = new CourseRepository($pdo);
    $progressRepo = new ProgressRepository($pdo);
    $progressService = new ProgressService($progressRepo);

    $courseService = new CourseService(
        $courseRepository,
        new ModuleRepository($pdo),
        new SlideRepository($pdo)
    );

    $courses = $courseService->getAllForUser($user['id']);

    foreach ($courses as $course) {
        $course->isUnlocked = true;
        if ($course->prerequisiteCourseId) {
            $course->isUnlocked = $progressService->isCourseCompleted($user['id'], $course->prerequisiteCourseId) ? 1 : 0;
        }
        $course->isCompleted = $progressService->isCourseCompleted($user['id'], $course->uuid) ? 1 : 0;
    }
    $additionalCss = [
        '/assets/css/dashboard.css'
    ];
    ?>

    <?php require_once __DIR__ . '/views/dashboard.php'; ?>

<?php else: ?>

    <?php require_once __DIR__ . '/views/login-form.php'; ?>

<?php endif; ?>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/template.php';
?>