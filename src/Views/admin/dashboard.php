<?php
/** @var array $viewModel */
?>
<h1>Willkommen, <?= htmlspecialchars($viewModel['user']->email ?? '') ?>!</h1>

<h2>Ihr Admin Dashboard</h2>

<div class="dashboard-stats">
    <div class="stat-card">
        <h3>Gesamte Kurse</h3>
        <span class="stat-number"><?= count($viewModel['allCourses'] ?? []) ?></span>
    </div>
    <div class="stat-card">
        <h3>Aktive Access Codes</h3>
        <span class="stat-number"><?= count($viewModel['accessCodes'] ?? []) ?></span>
    </div>
    <div class="stat-card">
        <h3>Gesamte Benutzer</h3>
        <span class="stat-number"><?= count($viewModel['allUsers'] ?? []) ?></span>
    </div>
</div>