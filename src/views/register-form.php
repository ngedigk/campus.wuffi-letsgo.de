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
