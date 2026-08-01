<?php
/** @var array $viewModel */
?>
<section>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">

                <h1>Anmelden</h1>

                <?php if (!empty($viewModel['loginError'])): ?>
                <p><?= htmlspecialchars($viewModel['loginError']) ?></p>
                <?php endif; ?>

                <form method="post">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>"
                    >

                    <label>E-Mail<br>
                        <input type="email" name="email" required>
                    </label>

                    <br><br>

                    <label>Passwort<br>
                        <input type="password" name="password" required>
                    </label>

                    <br><br>

                    <button type="submit">Anmelden</button>

                </form>

                <br>

                <a href="register.php">Account erstellen</a>
                <br>
                <a href="forgot-password.php">Passwort vergessen?</a>

            </div>
        </div>
    </div>
</section>