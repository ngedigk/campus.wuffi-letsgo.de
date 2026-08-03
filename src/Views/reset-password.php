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
                        <h2>Passwort aktualisiert</h2>
                        <div>
                            <a href="index.php">Login</a>
                        </div>
                    </div>

                <?php else: ?>

                    <form method="post" class="form-card">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">
                        <input type="hidden" name="user_uuid" value="<?= htmlspecialchars($viewModel['userUuid'] ?? '') ?>">

                        <h2>Passwort zurücksetzen</h2>

                        <div class="form-group">
                            <label>Neues Passwort
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
                                    >
                                        <img src="/assets/images/icons/eye.svg" data-alt-icon="/assets/images/icons/eye-slash.svg" alt="Passwort anzeigen" />
                                    </button>
                                </div>
                            </label>
                        </div>

                        <div class="form-group">
                            <label>Passwort bestätigen
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
                            <div id="password-hints">Empfehlung: 12+ Zeichen mit Groß-/Kleinbuchstaben, Zahlen und Symbolen.</div>
                            <div class="pw-status"></div>
                        </div>

                        <button type="submit" class="button-primary">Passwort aktualisieren</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
