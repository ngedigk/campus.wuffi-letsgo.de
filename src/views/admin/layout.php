<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> - Admin Panel</title>
    <link rel="stylesheet" href="/assets/css/style.css">

    <?php if (!empty($additionalCss)): ?>
        <?php foreach ($additionalCss as $cssFile): ?>
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
                        <a href="admin.php?page=dashboard" class="<?= $activePage === 'dashboard' ? 'active' : '' ?>">
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-section">
                        <span class="nav-section-title">Courses</span>
                        <button onclick="document.getElementById('createCourseModal').style.display='flex'">
                            <span class="nav-icon">➕</span>
                            <span>Add Course</span>
                        </button>
                        <div class="search-box">
                            <input type="text" placeholder="Search courses..." id="courseSearch">
                        </div>
                        <div class="sidebar-course-list-wrapper">
                            <ul class="sidebar-course-list">
                                <?php require __DIR__ . '/courses/partials/courses-list.php'; ?>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="admin.php?page=users" class="<?= $activePage === 'users' ? 'active' : '' ?>">
                            <span>Users</span>
                        </a>
                    </li>
                    <li>
                        <a href="admin.php?page=access-codes" class="<?= $activePage === 'access-codes' ? 'active' : '' ?>">
                            <span>Access Codes</span>
                        </a>
                    </li>
                    <li>
                        <a href="admin.php?page=settings" class="<?= $activePage === 'settings' ? 'active' : '' ?>">
                            <span>Settings</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar">
                        <?= strtoupper(substr($user['email'] ?? 'A', 0, 1)) ?>
    </div>
                    <div class="user-details">
                        <span class="user-name"><?= htmlspecialchars($user['email'] ?? 'Admin User') ?></span>
                        <span class="user-email"><?= htmlspecialchars($user['email'] ?? 'admin@example.com') ?></span>
                    </div>
                </div>
                <a href="index.php" class="back-link">← Back to Site</a>
            </div>
        </aside>

        <main class="admin-content">
            <?php if (isset($breadcrumb)): ?>
            <div class="breadcrumb">
                <a href="admin.php?page=dashboard">Dashboard</a>
                <?php foreach ($breadcrumb as $crumb): ?>
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
                <?php if ($adminError): ?>
                    <div class="alert alert-error">
                        <span class="alert-icon"></span>
                        <?= htmlspecialchars($adminError) ?>
                    </div>
                <?php endif; ?>

                <?php if ($adminSuccess): ?>
                    <div class="alert alert-success">
                        <span class="alert-icon"></span>
                        <?= htmlspecialchars($adminSuccess) ?>
                    </div>
                <?php endif; ?>

                <div class="content-body">
                    <?= $content ?>
                </div>
            </div>

            <div class="admin-footer">
                
            </div>
        </main>
    </div>
    <?php if (!empty($additionalJs)): ?>
        <?php foreach ($additionalJs as $jsFile): ?>
            <script type="text/javascript" src="<?= htmlspecialchars($jsFile) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>

