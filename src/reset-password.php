<?php

require_once __DIR__ . '/bootstrap.php';

$token = $_GET['token'] ?? '';
$error = '';
$success = '';

$container = Container::getInstance();

$authService = $container->get(AuthService::class);
$isLoggedIn = $authService->isLoggedIn();

$csrfService = $container->get(CsrfService::class);
$csrfToken = $csrfService->generateToken();

$passwordResetsRepository = $container->get(PasswordResetsRepository::class);

$userUuid = $passwordResetsRepository->getUserUuidByToken($token);

if (!$userUuid) {
    exit("Invalid or expired token.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        $csrfService->validateToken($_POST['csrf_token']);
    } catch (Exception $e) {
        $error = $e->getMessage();
        return;
    }

    $password = $_POST['password'];

    if (strlen($password) < 12) {
        $error = "Password too short";
    } else {

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $userService = $container->get(UserService::class);
        $userService->setPassword($userUuid, $hash);

        $passwordResetsRepository->deleteRecord($userUuid);

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
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

    <label>New Password</label><br>
    <input type="password" name="password" required>

    <br><br>

    <button type="submit">Update Password</button>

</form>

<?php endif; ?>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/Views/template.php';
?>