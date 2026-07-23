<?php

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/Database.php';

$token = $_GET['token'] ?? '';
$error = '';
$success = '';

$pdo = Database::getInstance();
$stmt = $pdo->prepare("
    SELECT user_id
    FROM password_resets
    WHERE token = ?
    AND expires_at > NOW()
");
$stmt->execute([$token]);
$row = $stmt->fetch();

if (!$row) {
    exit("Invalid or expired token.");
}

$userUuid = $row['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    validateCsrf(); 

    $password = $_POST['password'];

    if (strlen($password) < 12) {
        $error = "Password too short";
    } else {

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            UPDATE users
            SET password_hash = ?
            WHERE id = ?
        ");
        $stmt->execute([$hash, $userUuid]);

        $stmt = $pdo->prepare("
            DELETE FROM password_resets
            WHERE user_id = ?
        ");
        $stmt->execute([$userUuid]);

        $success = "Password updated successfully.";
    }
}

$pageTitle = 'Reset Password';
ob_start();
?>
<h1>Reset Password</h1>

<?php if ($error): ?><p><?= htmlspecialchars($error) ?></p><?php endif; ?>

<?php if ($success): ?><p><?= htmlspecialchars($success) ?></p><a href="index.php">Login</a><?php endif; ?>

<?php if (!$success): ?>
<form method="post">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">

    <label>New Password</label><br>
    <input type="password" name="password" required>

    <br><br>

    <button type="submit">Update Password</button>

</form>

<?php endif; ?>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/template.php';
?>