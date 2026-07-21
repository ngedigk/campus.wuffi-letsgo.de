<h1>Anmelden</h1>

<?php if($error): ?>
<p><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post">

    <input
        type="hidden"
        name="csrf_token"
        value="<?= htmlspecialchars(csrfToken()) ?>"
    >

    <label>E-Mail</label>
    <br>

    <input
        type="email"
        name="email"
        required
    >

    <br><br>

    <label>Passwort</label>
    <br>

    <input
        type="password"
        name="password"
        required
    >

    <br><br>

    <button type="submit">
        Anmelden
    </button>

</form>

<br>

<a href="register.php">
Account erstellen
</a>

<br>

<a href="forgot-password.php">
Passwort vergessen?
</a>