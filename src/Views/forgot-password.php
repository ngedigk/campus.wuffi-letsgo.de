<?php
/** @var array $viewModel */
?>
<section>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1>Passwort vergessen</h1>

                <p><?= htmlspecialchars($viewModel['message'] ?? '') ?></p>

                <form method="post">

                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">

                    <label>E-Mail</label><br>
                    <input type="email" name="email" required>

                    <br><br>

                    <button type="submit">Reset-Link senden</button>

                </form>
            </div>
        </div>
    </div>
</section>