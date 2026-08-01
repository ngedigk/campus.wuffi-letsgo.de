<?php
/** @var string $csrfToken */
/** @var string $success */
/** @var string $error */
/** @var string $registrationCode */
/** @var string $email */
?>
<section>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">

                <?php if ($success): ?>
                    <h1>Account erstellt</h1>
                    <p class="success">
                        <?= htmlspecialchars($success) ?>
                    </p>
                    <a href="index.php">
                        Zur Anmeldung
                    </a>
                <?php else: ?>
                    <h1>Account erstellen</h1>

                    <?php if ($error): ?>

                        <p class="error">
                            <?= htmlspecialchars($error) ?>
                        </p>

                    <?php endif; ?>
                        
                    <form method="post">

                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                        <label>Registrierungscode<br>
                            <input
                                type="text"
                                name="registration_code"
                                value="<?= htmlspecialchars($registrationCode) ?>"
                                autocomplete="off"
                                required
                            >
                        </label>

                        <br>

                        <label>E-Mail<br>
                            <input
                                type="email"
                                name="email"
                                value="<?= htmlspecialchars($email) ?>"
                                autocomplete="email"
                                required
                            >
                        </label>

                        <br>

                        <label>Passwort<br>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                autocomplete="new-password"
                                required
                            >
                        </label>

                        <br>

                        <label>Passwort bestätigen<br>
                            <input
                                type="password"
                                name="password_confirm"
                                autocomplete="new-password"
                                required
                            >
                        </label>

                        <div class="password-meter">
                            <div class="password-bar">
                                <div id="password-progress"></div>
                            </div>
                            <div id="password-label">Passwort eingeben</div>
                            <div id="password-hints">
                                Empfehlung: 12+ Zeichen mit Groß-/Kleinbuchstaben, Zahlen und Symbolen.
                            </div>
                            <div class="pw-status"></div>
                        </div>

                        <button type="submit">Registrieren</button>

                    </form>
                    <a href="index.php">
                        Sie haben bereits einen Account?
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>