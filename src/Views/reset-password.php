<section>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1>Reset Password</h1>

                <?php if (!empty($error)): ?>
                <p><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                <p><?= htmlspecialchars($success) ?></p>
                <a href="index.php">Login</a>
                <?php else: ?>
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                    <label>New Password</label><br>
                    <input type="password" name="password" required>

                    <br><br>

                    <button type="submit">Update Password</button>

                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>