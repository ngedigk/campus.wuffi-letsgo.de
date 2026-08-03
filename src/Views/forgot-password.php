<?php
/** @var array $viewModel */
?>
<section>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                
                <?php require_once __DIR__ . "/partials/general-messages.php"; ?>

                <form method="post" class="form-card">
                    <h2>Passwort vergessen</h2>

                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">

                    <div class="form-group">
                        <label>E-Mail
                            <input type="email" name="email" required>
                        </label>
                    </div>

                    <button type="submit" class="button-primary">Reset-Link senden</button>

                </form>
            </div>
        </div>
    </div>
</section>