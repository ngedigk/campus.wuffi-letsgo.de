<form method="post">

    <input
        type="hidden"
        name="csrf_token"
        value="<?= htmlspecialchars(csrfToken()) ?>"
    >

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
        minlength="12"
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
    Use 12+ characters with uppercase, lowercase, numbers, and symbols.
    </div>

    <br><br>

    <label>
    Confirm password
    </label>

    <br>

    <input
        type="password"
        name="password_confirm"
        minlength="12"
        autocomplete="new-password"
        required
    >

    <br><br>

    <button type="submit">
        Register
    </button>

</form>