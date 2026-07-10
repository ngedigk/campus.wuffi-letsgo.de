<h1>Login</h1>

<?php if($error): ?>
<p><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

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
        required
    >

    <br><br>

    <label>Password</label>
    <br>

    <input
        type="password"
        name="password"
        required
    >

    <br><br>

    <button type="submit">
        Login
    </button>

</form>

<br>

<a href="register.php">
Create account
</a>

<br>

<a href="forgot-password.php">
Forgot password
</a>