<?php
/** @var array $viewModel */
?>
<section>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">

                <?php require_once __DIR__ . "/partials/general-messages.php"; ?>

                <?php if (!empty($viewModel['success'])): ?>

                    <div class="general-card">
                        <h2>Account erstellt</h2>
                        <div>
                            <a href="/">
                                Zur Anmeldung
                            </a>
                        </div>
                    </div>

                <?php else: ?>

                    <form method="post" class="form-card">

                        <h2>Account erstellen</h2>

                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">

                        <div class="register-fields">
                            <div class="user-data-group">
                                <div class="form-group">
                                    <label>Registrierungscode *
                                        <input
                                            type="text"
                                            name="registration_code"
                                            value="<?= htmlspecialchars($viewModel['registrationCode'] ?? '') ?>"
                                            autocomplete="off"
                                            required
                                        >
                                    </label>
                                </div>

                                <div class="form-group">
                                    <label>E-Mail *
                                        <input
                                            type="email"
                                            name="email"
                                            value="<?= htmlspecialchars($viewModel['email'] ?? '') ?>"
                                            autocomplete="email"
                                            required
                                        >
                                    </label>
                                </div>

                                <div class="form-group">
                                    <label>Benutzername (optional)
                                        <input
                                            type="text"
                                            name="name"
                                            value="<?= htmlspecialchars($viewModel['name'] ?? '') ?>"
                                        >
                                    </label>
                                </div>
                            </div>
                            <div class="password-group">
                                <div class="form-group">
                                    <label>Passwort *
                                        <div class="form-input">
                                            <input
                                                type="password"
                                                id="password"
                                                name="password"
                                                autocomplete="new-password"
                                                required
                                            >
                                            <button
                                                type="button"
                                                class="password-toggle"
                                                aria-label="Passwort anzeigen"
                                                aria-pressed="false"
                                                data-target="password"
                                                tabindex="-1"
                                            >
                                                <img src="/assets/images/icons/eye.svg" data-alt-icon="/assets/images/icons/eye-slash.svg" alt="Passwort anzeigen" />
                                            </button>
                                        </div>
                                    </label>
                                </div>
                                
                                <div class="form-group">
                                    <label>Passwort bestätigen *
                                        <div class="form-input">
                                            <input
                                                type="password"
                                                id="password-confirm"
                                                name="password_confirm"
                                                autocomplete="new-password"
                                                required
                                            >
                                            <button
                                                type="button"
                                                class="password-toggle"
                                                aria-label="Passwort anzeigen"
                                                aria-pressed="false"
                                                data-target="password-confirm"
                                                tabindex="-1"
                                            >
                                                <img src="/assets/images/icons/eye.svg" data-alt-icon="/assets/images/icons/eye-slash.svg" alt="Passwort anzeigen" />
                                            </button>
                                        </div>
                                    </label>
                                </div>

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

                                <button type="submit" class="button-primary">Registrieren</button>
                            </div>
                        </div>

                    </form>
                    <a href="/">
                        Sie haben bereits einen Account?
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>