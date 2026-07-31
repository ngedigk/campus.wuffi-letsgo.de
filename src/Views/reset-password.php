<?php
/** @var string $csrfToken */
/** @var string $success */
/** @var string $error */
/** @var string $userUuid */
?>
<section>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">

                <?php if (!empty($success)): ?>
                    <h1>Passwort aktualisiert</h1>
                    <p class="success"><?= htmlspecialchars($success) ?></p>
                    <a href="index.php">Login</a>
                <?php else: ?>
                    <h1>Passwort zurücksetzen</h1>

                    <?php if (!empty($error)): ?>
                        <p class="error"><?= htmlspecialchars($error) ?></p>
                    <?php endif; ?>

                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                        <input type="hidden" name="user_uuid" value="<?= htmlspecialchars($userUuid ?? '') ?>">

                        <label>Neues Passwort</label><br>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            autocomplete="new-password"
                            required
                        >

                        <div class="password-meter">
                            <div class="password-bar">
                                <div id="password-progress"></div>
                            </div>
                            <div id="password-label">Passwort eingeben</div>
                        </div>

                        <div id="password-hints">
                            Empfehlung: 12+ Zeichen mit Groß-/Kleinbuchstaben, Zahlen und Symbolen.
                        </div>

                        <br><br>

                        <label>Passwort bestätigen</label><br>
                        <input
                            type="password"
                            name="password_confirm"
                            autocomplete="new-password"
                            required
                        >

                        <br><br>

                        <button type="submit">Passwort aktualisieren</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
