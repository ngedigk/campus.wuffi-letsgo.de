<?php

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/mail.php';
require_once __DIR__ . '/csrf.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    validateCsrf();

    $email = trim($_POST['email']);

    $container = Container::getInstance();
    $userRepository = $container->get(UserRepository::class);
    $user = $userRepository->findByEmail($email);

    $message = "If the email exists, a reset link was sent.";

    if ($user) {

        $token = bin2hex(random_bytes(32));

        $passwordResetsRepository = $container->get(PasswordResetsRepository::class);
        $passwordResetsRepository->recordReset($user->id, $token);

        $link = SITE_URL . "/reset-password.php?token=" . $token;

        sendMail(
            $email,
            "Password Reset",
            "<p>Reset your password:</p>
             <a href='$link'>$link</a>"
        );
    }
}

$pageTitle = 'Forgot Password';
ob_start();
?>
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h1>Forgot Password</h1>

            <p><?= htmlspecialchars($message) ?></p>

            <form method="post">

                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">

                <label>Email</label><br>
                <input type="email" name="email" required>

                <br><br>

                <button type="submit">Send reset link</button>

            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/template.php';
?>
