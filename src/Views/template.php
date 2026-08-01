<?php
/** @var array $viewModel */
/** @var string $content */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($viewModel['pageTitle'] ?? 'Home') ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="/assets/images/favicon/favicon.ico" sizes="any">
    <link rel="icon" type="image/png" sizes="48x48" href="/assets/images/favicon/favicon-48x48.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/assets/images/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/assets/images/favicon/favicon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/images/favicon/favicon-16x16.png">
    <link rel="icon" type="image/svg+xml" href="/assets/images/favicon/favicon.svg">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/images/favicon/apple-touch-icon.png">
    <link rel="stylesheet" href="assets/css/style.css">
    

    <?php if (!empty($viewModel['additionalCss'])): ?>
        <?php foreach ($viewModel['additionalCss'] as $cssFile): ?>
            <link rel="stylesheet" href="<?= htmlspecialchars($cssFile) ?>">
        <?php endforeach; ?>
    <?php endif; ?>

</head>
<body>
    <?php require_once __DIR__ . '/header.php'; ?>

    <main>
        <?= $content ?? '' ?>
    </main>

    <?php require_once __DIR__ . '/footer.php'; ?>

    <script type="text/javascript" src="assets/js/sticky-header.js"></script>
    <script type="text/javascript" src="assets/js/menu.js"></script>
    <?php if (!empty($viewModel['additionalJs'])): ?>
        <?php foreach ($viewModel['additionalJs'] as $jsFile): ?>
            <script type="<?= htmlspecialchars($jsFile['type'] ?? 'text/javascript') ?>" src="<?= htmlspecialchars($jsFile['src']) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>