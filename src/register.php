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

        $error = "Ungültige E-Mail Adresse.";

    } elseif ($password !== $passwordConfirm) {

        $error = "Passwörter stimmen nicht überein.";

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
                "Bestätigen Sie Ihre E-Mail",
                "
                <h1>Account verifizieren</h1>
                <p>Klicken Sie auf den unteren Link:</p>
                <a href='$link'>$link</a>
                "
            );

            $success = "Registrierung erfolgreich. Überprüfen Sie Ihre E-Mails.";

        } catch (Throwable $e) {

            error_log($e);

            $error = "Bei der Erstellung des Accounts ist ein Problem aufgetreten. Informieren Sie den Anbieter und versuchen Sie es später nochmal.";
        }
    }
}

$pageTitle = 'Registrierung';
$additionalCss = [
    '/assets/css/register.css'
];
$additionalJs = [
    '/assets/js/password-meter.js'
];
ob_start();
?>
<?php if ($success): ?>
<h1>Account erstellt</h1>
<p class="success">
    <?= htmlspecialchars($success) ?>
</p>
<a href="index.php">
    Zur Anmeldung
</a>
<?php else: ?>
<h1>Account erstellen</h1>

<?php if ($error): ?>

<p class="error">
    <?= htmlspecialchars($error) ?>
</p>

<?php endif; ?>

<?php require 'views/register-form.php'; ?>

<a href="index.php">
    Sie haben bereits einen Account?
</a>
<?php endif; ?>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/template.php';
?>