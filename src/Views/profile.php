<?php
/** @var string $csrfToken */
/** @var User $user */
/** @var string $success */
/** @var string $error */
?>
<section>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1>Mein Profil</h1>

                <?php if (!empty($success)): ?>
                    <p class="success"><?= htmlspecialchars($success) ?></p>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <p class="error"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>

                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                    <label>Name<br>
                        <input
                            type="text"
                            name="name"
                            value="<?= htmlspecialchars($user->name ?? '') ?>"
                            placeholder="Ihr Name"
                        >
                    </label>
                    
                    <br><br>

                    <label>E-Mail<br>
                        <input
                            type="email"
                            name="email"
                            value="<?= htmlspecialchars($user->email) ?>"
                            required
                        >
                    </label>

                    <br><br>

                    <button type="submit">Speichern</button>
                </form>

                <br>

                <h2>Sicherheit</h2>

                <form method="post">
                    <input type="hidden" name="action" value="change_password">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                    <label>Neues Passwort<br>
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

                    <button type="submit">Passwort ändern</button>
                </form>
            </div>
        </div>
    </div>
</section>

