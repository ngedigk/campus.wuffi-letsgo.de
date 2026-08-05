<?php
/** @var array $viewModel */
/** @var string $content */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($viewModel['pageTitle'] ?? 'Admin') ?> - Admin Panel</title>
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">
    <link rel="icon" href="/assets/images/favicon/favicon.ico" sizes="any">
    <link rel="icon" type="image/png" sizes="48x48" href="/assets/images/favicon/favicon-48x48.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/assets/images/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/assets/images/favicon/favicon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/images/favicon/favicon-16x16.png">
    <link rel="icon" type="image/svg+xml" href="/assets/images/favicon/favicon.svg">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/images/favicon/apple-touch-icon.png">
    <link rel="stylesheet" href="/assets/css/style.css">

    <?php if (!empty($viewModel['additionalCss'])): ?>
        <?php foreach ($viewModel['additionalCss'] as $cssFile): ?>
            <link rel="stylesheet" href="<?= htmlspecialchars($cssFile) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body class="admin-page">
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h1>Admin Interface</h1>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li>
                        <a href="/admin" class="<?= ($viewModel['activePage'] ?? '') === 'dashboard' ? 'active' : '' ?>">
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-section">
                        <span class="nav-section-title">Courses</span>
                        <button onclick="document.getElementById('createCourseModal').style.display='flex'">
                            <span class="nav-icon">➕</span>
                            <span>Kurs hinzufügen</span>
                        </button>
                        <div class="search-box">
                            <input type="text" placeholder="Kurse suchen..." id="courseSearch">
                        </div>
                        <div class="sidebar-course-list-wrapper">
                            <ul class="sidebar-course-list">
                                <?php require __DIR__ . '/courses/partials/courses-list.php'; ?>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="/admin/users" class="<?= ($viewModel['activePage'] ?? '') === 'users' ? 'active' : '' ?>">
                            <span>Benutzer</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/audio-assets" class="<?= ($viewModel['activePage'] ?? '') === 'audio-assets' ? 'active' : '' ?>">
                            <span>Audio Assets</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/registration-codes" class="<?= ($viewModel['activePage'] ?? '') === 'registration-codes' ? 'active' : '' ?>">
                            <span>Registration Codes</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/access-codes" class="<?= ($viewModel['activePage'] ?? '') === 'access-codes' ? 'active' : '' ?>">
                            <span>Access Codes</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar">
                        <?= strtoupper(substr($viewModel['user']->email ?? 'A', 0, 1)) ?>
                    </div>
                    <div class="user-details">
                        <span class="user-name"><?= htmlspecialchars($viewModel['user']->email ?? 'Admin User') ?></span>
                        <span class="user-email"><?= htmlspecialchars($viewModel['user']->email ?? 'admin@example.com') ?></span>
                    </div>
                </div>
                <a href="/" class="back-link">← Zurück zur Seite</a>
            </div>
        </aside>

        <main class="admin-content">
            <?php if (!empty($viewModel['breadcrumb'])): ?>
            <div class="breadcrumb">
                <a href="/admin">Dashboard</a>
                <?php foreach ($viewModel['breadcrumb'] as $crumb): ?>
                    <?php if (isset($crumb['url'])): ?>
                        <span>/</span>
                        <a href="<?= htmlspecialchars($crumb['url']) ?>"><?= htmlspecialchars($crumb['title']) ?></a>
                    <?php else: ?>
                        <span>/</span>
                        <span class="current"><?= htmlspecialchars($crumb['title']) ?></span>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="content-wrapper">
                <?php if (!empty($viewModel['adminError'])): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($viewModel['adminError']) ?></div>
                <?php endif; ?>

                <?php if (!empty($viewModel['adminSuccess'])): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($viewModel['adminSuccess']) ?></div>
                <?php endif; ?>

                <div class="content-body">
                    <?= $content ?>
                </div>
            </div>

            <div class="admin-footer">
                
            </div>
        </main>

        <!-- Create Course Modals -->
        <?php require __DIR__ . '/courses/partials/modals/create-course-modal.php'; ?>
    
    </div>
    <?php if (!empty($viewModel['slideAssets'])): ?>
        <script>
        window.existingAssets = <?= json_encode($viewModel['slideAssets'] ?? []) ?>;
        </script>
    <?php endif; ?>
    <?php if (!empty($viewModel['additionalJs'])): ?>
        <?php foreach ($viewModel['additionalJs'] as $jsFile): ?>
            <script type="<?= htmlspecialchars($jsFile['type'] ?? 'text/javascript') ?>" src="<?= htmlspecialchars($jsFile['src']) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>

