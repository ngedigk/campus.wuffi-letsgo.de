<?php

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/uuid.php';
require_once __DIR__ . '/validation.php';

require_once __DIR__ . '/repositories/UserRepository.php';
require_once __DIR__ . '/repositories/EmailVerificationRepository.php';
require_once __DIR__ . '/repositories/RegistrationCodeRepository.php';
require_once __DIR__ . '/repositories/AccessCodeRepository.php';
require_once __DIR__ . '/services/RegistrationService.php';

$error = '';
$success = '';
$email = '';
$registrationCode = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    validateCsrf();

    $email = strtolower(trim($_POST['email'] ?? ''));
    $registrationCode = trim($_POST['registration_code'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Invalid email address.";

    } elseif ($password !== $passwordConfirm) {

        $error = "Passwords do not match.";

    } elseif ($passwordError = validatePassword($password)) {

        $error = $passwordError;

    } else {

        try {

            $service = new RegistrationService(
                $pdo,
                new UserRepository($pdo),
                new EmailVerificationRepository($pdo),
                new RegistrationCodeRepository($pdo),
                new AccessCodeRepository($pdo)
            );

            $result = $service->register(
                $email,
                $password,
                $registrationCode
            );

            $link = SITE_URL .
                "/verify-email.php?token=" .
                $result['token'];

            require_once 'mail.php';

            sendMail(
                $email,
                "Verify your email",
                "
                <h1>Verify your account</h1>
                <p>Click the link below:</p>
                <a href='$link'>$link</a>
                "
            );

            $success = "Registration successful. Check your email.";

        } catch (Throwable $e) {

            error_log($e);

            $error = "Unable to create account.";
        }
    }
}

$pageTitle = 'Register';
$additionalCss = [
    '/assets/css/register.css'
];
$additionalJs = [
    '/assets/js/password-meter.js'
];
ob_start();
?>
<?php if ($success): ?>
<h1>Account created</h1>
<p class="success">
    <?= htmlspecialchars($success) ?>
</p>
<a href="index.php">
    Go to Login
</a>
<?php else: ?>
<h1>Create Account</h1>

<?php if ($error): ?>

<p class="error">
    <?= htmlspecialchars($error) ?>
</p>

<?php endif; ?>

<?php require 'views/register-form.php'; ?>

<a href="index.php">
    Already have an account?
</a>
<?php endif; ?>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/template.php';
?>