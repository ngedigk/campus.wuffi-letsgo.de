<?php

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/rate_limit.php';
require_once __DIR__ . '/repositories/CourseRepository.php';
require_once __DIR__ . '/repositories/UserRepository.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    validateCsrf();

    $email = trim($_POST['email'] ?? '');

    $password = $_POST['password'] ?? '';

    if (isIpBlocked($pdo)) {
        $error = "Too many login attempts. Please try again later.";
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
                $error = "Please verify your email first.";
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
            $error = "Invalid email or password";
        }
    }
}

$pageTitle = 'Home';

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

    $stmt = $pdo->prepare("
        SELECT
            c.id,
            c.title,
            c.description,
            c.prerequisite_course_id,
            pc.title AS prerequisite_title,
            uc.is_completed,
            uc.completed_at,
            CASE
                WHEN c.prerequisite_course_id IS NULL THEN 1
                WHEN EXISTS (
                    SELECT 1
                    FROM user_courses prereq_uc
                    WHERE prereq_uc.user_id = ?
                      AND prereq_uc.course_id = c.prerequisite_course_id
                      AND prereq_uc.is_completed = 1
                ) THEN 1
                ELSE 0
            END AS is_unlocked
        FROM courses c
        JOIN user_courses uc ON c.id = uc.course_id
        LEFT JOIN courses pc ON c.prerequisite_course_id = pc.id
        WHERE uc.user_id = ?
    ");

    $stmt->execute([$user['id'], $user['id']]);

    $courses = $stmt->fetchAll();
    $courseRepository = new CourseRepository($pdo);
    $courseOptions = $courseRepository->listAll();
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