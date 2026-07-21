<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Home') ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="assets/css/style.css">
    

    <?php if (!empty($additionalCss)): ?>
        <?php foreach ($additionalCss as $cssFile): ?>
            <link rel="stylesheet" href="<?= htmlspecialchars($cssFile) ?>">
        <?php endforeach; ?>
    <?php endif; ?>

</head>
<body>
    <?php require_once __DIR__ . '/views/header.php'; ?>

    <main>
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <?= $content ?? '' ?>
                </div>
            </div>
        </div>
    </main>

    <?php require_once __DIR__ . '/views/footer.php'; ?>

    <script type="text/javascript" src="assets/js/sticky-header.js"></script>
    <script type="text/javascript" src="assets/js/menu.js"></script>
    <?php if (!empty($additionalJs)): ?>
        <?php foreach ($additionalJs as $jsFile): ?>
            <script type="text/javascript" src="<?= htmlspecialchars($jsFile) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>