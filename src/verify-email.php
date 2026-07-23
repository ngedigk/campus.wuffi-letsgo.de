<?php
require_once __DIR__ . '/Database.php';

$token = $_GET['token'] ?? '';

$pdo = Database::getInstance();

$stmt = $pdo->prepare("
    SELECT user_id
    FROM email_verifications
    WHERE token = ?
    AND expires_at > NOW()
");

$stmt->execute([$token]);

$row = $stmt->fetch();

if (!$row) {
    die("Invalid or expired token.");
}

$userUuid = $row['user_id'];

$stmt = $pdo->prepare("
    UPDATE users
    SET email_verified = 1
    WHERE id = ?
");

$stmt->execute([$userUuid]);

$stmt = $pdo->prepare("
    DELETE FROM email_verifications
    WHERE user_id = ?
");

$stmt->execute([$userUuid]);

$pageTitle = 'Email Verified';
ob_start();
?>
<h1>Email verified successfully</h1>
<a href="index.php">Go to login</a>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/template.php';
?>