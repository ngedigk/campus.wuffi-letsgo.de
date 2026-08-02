<?php
/** @var array $viewModel */
?>
<section>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">

                <?php if (!empty($viewModel['loginError'])): ?>
                <p><?= htmlspecialchars($viewModel['loginError']) ?></p>
                <?php endif; ?>

                <form method="post" class="form-card">

                    <h2>Anmelden</h2>

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>"
                    >

                    <div class="form-group">
                        <label>E-Mail
                            <input type="email"name="email" required>
                        </label>
                    </div>

                    <div class="form-group">
                        <label>Passwort
                            <div class="form-input">
                                <input type="password" id="password" name="password" required>
                                <button
                                    type="button"
                                    class="password-toggle"
                                    aria-label="Passwort anzeigen"
                                    aria-pressed="false"
                                    data-target="password"
                                >
                                    <img src="/assets/images/icons/eye.svg" data-alt-icon="/assets/images/icons/eye-slash.svg" alt="Passwort anzeigen" />
                                </button>
                            </div>
                        </label>
                    </div>

                    <button type="submit" class="button-primary">Anmelden</button>

                </form>

                <a href="register.php">Account erstellen</a>
                <br>
                <a href="forgot-password.php">Passwort vergessen?</a>

            </div>
        </div>
    </div>
</section>