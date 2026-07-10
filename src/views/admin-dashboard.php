<h1>Welcome, <?= htmlspecialchars($user['email']) ?>!</h1>

<h2>Your Admin Dashboard</h2>

<?php if ($isAdmin): ?>

<h2>Admin Panel</h2>

<?php if ($adminError): ?>
    <p style="color: red; font-weight: bold;"><?= htmlspecialchars($adminError) ?></p>
<?php endif; ?>

<?php if ($adminSuccess): ?>
    <p style="color: green; font-weight: bold;"><?= htmlspecialchars($adminSuccess) ?></p>
<?php endif; ?>

<h3>Grant Admin Access</h3>
<form method="post" action="admin.php">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
    <input type="hidden" name="action" value="grant_admin">
    <input type="email" name="email" placeholder="User email" required>
    <button type="submit">Grant Admin</button>
</form>

<h3>Create Course</h3>
<form method="post" action="admin.php">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
    <input type="hidden" name="action" value="create_course">
    <input type="text" name="title" placeholder="Course title" required>
    <br><br>
    <textarea name="description" placeholder="Course description" rows="4"></textarea>
    <br><br>
    <select name="prerequisite_course_id">
        <option value="">No prerequisite</option>
        <?php foreach ($courseOptions as $courseOption): ?>
            <option value="<?= htmlspecialchars($courseOption['id']) ?>">
                <?= htmlspecialchars($courseOption['title']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br><br>
    <button type="submit">Create Course</button>
</form>

<h3>Current Access Codes</h3>
<ul>
    <?php foreach ($accessCodes as $accessCode): ?>
        <li><?= htmlspecialchars($accessCode['code']) ?></li>
    <?php endforeach; ?>
</ul>

<h3>Create Access Code</h3>
<form method="post" action="admin.php">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
    <input type="hidden" name="action" value="create_access_code">
    <input type="text" name="code" placeholder="Access code" required>
    <br><br>
    <select name="course_id" required>
        <?php foreach ($courseOptions as $courseOption): ?>
            <option value="<?= htmlspecialchars($courseOption['id']) ?>">
                <?= htmlspecialchars($courseOption['title']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br><br>
    <button type="submit">Create Code</button>
</form>
<?php endif; ?>