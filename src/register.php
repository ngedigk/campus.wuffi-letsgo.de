<?php

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/uuid.php';
require_once __DIR__ . '/validation.php';
require_once __DIR__ . '/Container.php';

$container = Container::getInstance();

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
            $registrationService = $container->get(RegistrationService::class);         

            $result = $registrationService->register(
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

$authService = $container->get(AuthService::class);

$isLoggedIn = $authService->isLoggedIn();

$pageTitle = 'Registrierung';
$additionalCss = [
    '/assets/css/register.css'
];
$additionalJs = [
    '/assets/js/password-meter.js'
];
ob_start();
?>
<?php require 'views/register-form.php'; ?>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/views/template.php';
?>