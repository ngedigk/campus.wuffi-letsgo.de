<?php

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/mail.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/Database.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    validateCsrf();

    $email = trim($_POST['email']);

    $pdo = Database::getInstance();
    $stmt = $pdo->prepare("
        SELECT id FROM users WHERE email = ?
    ");

    $stmt->execute([$email]);

    $user = $stmt->fetch();

    $message = "If the email exists, a reset link was sent.";

    if ($user) {

        $token = bin2hex(random_bytes(32));

        $stmt = $pdo->prepare("
            INSERT INTO password_resets
            (user_id, token, expires_at)
            VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))
        ");

        $stmt->execute([$user['id'], $token]);

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