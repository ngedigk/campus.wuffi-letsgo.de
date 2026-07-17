<form method="post">

    <input
        type="hidden"
        name="csrf_token"
        value="<?= htmlspecialchars(csrfToken()) ?>"
    >

    <label>Registration Code</label>
    <br>

    <input
        type="text"
        name="registration_code"
        value="<?= htmlspecialchars($registrationCode) ?>"
        autocomplete="off"
        required
    >

    <br><br>

    <label>Email</label>
    <br>

    <input
        type="email"
        name="email"
        value="<?= htmlspecialchars($email) ?>"
        autocomplete="email"
        required
    >

    <br><br>

    <label>Password</label>
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
            Enter a password
        </div>

    </div>

    <div id="password-hints">
        Recommendations: 12+ characters with uppercase, lowercase, numbers, and symbols.
    </div>

    <br><br>

    <label>
    Confirm password
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
        Register
    </button>

</form>
