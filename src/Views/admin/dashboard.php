<?php
/** @var array $viewModel */
?>
<h1>Welcome, <?= htmlspecialchars($viewModel['user']->email ?? '') ?>!</h1>

<h2>Your Admin Dashboard</h2>

<div class="dashboard-stats">
    <div class="stat-card">
        <h3>Total Courses</h3>
        <span class="stat-number"><?= count($viewModel['allCourses'] ?? []) ?></span>
    </div>
    <div class="stat-card">
        <h3>Active Access Codes</h3>
        <span class="stat-number"><?= count($viewModel['accessCodes'] ?? []) ?></span>
    </div>
    <div class="stat-card">
        <h3>Total Users</h3>
        <span class="stat-number"><?= count($viewModel['allUsers'] ?? []) ?></span>
    </div>
</div>