<h1>Welcome, <?= htmlspecialchars($user['email']) ?>!</h1>

<h2>Your Dashboard</h2>

<h3>Your Courses</h3>

<?php if (!$courses): ?>
<p>You don't have any courses yet.</p>
<?php else: ?>
    <ul class="course-list">
        <?php foreach ($courses as $course): ?>
            <?php
            $courseCardClass = 'course-card';
            if (!empty($course['is_completed'])) {
                $courseCardClass .= ' completed';
            } elseif (empty($course['is_unlocked'])) {
                $courseCardClass .= ' locked';
            }
            ?>
            <li class="<?= htmlspecialchars($courseCardClass) ?>">
                <div class="card-image"></div>
                <div class="card-text">
                    <strong><?= htmlspecialchars($course['title']) ?></strong>
                    <div><?= htmlspecialchars($course['description']) ?></div>
                    <br>
                    <?php if (!empty($course['is_unlocked'])): ?>
                        <a href="course.php?id=<?= urlencode($course['id']) ?>" class="button-primary">
                            Open Course
                        </a>
                    <?php else: ?>
                        <span>Locked</span>
                    <?php endif; ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<hr>

<h2>Redeem Code</h2>

<?php if ($redeemError): ?>
    <p style="color: red; font-weight: bold;"><?= htmlspecialchars($redeemError) ?></p>
<?php endif; ?>

<?php if ($redeemSuccess): ?>
    <p style="color: green; font-weight: bold;"><?= htmlspecialchars($redeemSuccess) ?></p>
<?php endif; ?>

<form method="post" action="redeem.php">

    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">

    <input type="text" name="code" placeholder="Enter code" required>

    <br><br>

    <button type="submit">Redeem</button>

</form>