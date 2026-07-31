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

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars(csrfToken()) ?>"
                >

                <label>Registrierungscode</label>
                <br>

                <input
                    type="text"
                    name="registration_code"
                    value="<?= htmlspecialchars($registrationCode) ?>"
                    autocomplete="off"
                    required
                >

                <br><br>

                <label>E-Mail</label>
                <br>

                <input
                    type="email"
                    name="email"
                    value="<?= htmlspecialchars($email) ?>"
                    autocomplete="email"
                    required
                >

                <br><br>

                <label>Passwort</label>
                <br>

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

                    <div id="password-label">
                        Passwort eingeben
                    </div>

                </div>

                <div id="password-hints">
                    Empfehlung: 12+ Zeichen mit Groß-/Kleinbuchstaben, Zahlen und Symbolen.
                </div>

                <br><br>

                <label>
                Passwort bestätigen
                </label>

                <br>

                <input
                    type="password"
                    name="password_confirm"
                    autocomplete="new-password"
                    required
                >

                <br><br>

                <button type="submit">
                    Registrieren
                </button>

            </form>
            <a href="index.php">
                Sie haben bereits einen Account?
            </a>
        </div>
    </div>
</div>
<?php endif; ?>