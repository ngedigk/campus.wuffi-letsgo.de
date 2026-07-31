<section>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1>Forgot Password</h1>

                <p><?= htmlspecialchars($message ?? '') ?></p>

                <form method="post">

                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                    <label>Email</label><br>
                    <input type="email" name="email" required>

                    <br><br>

                    <button type="submit">Send reset link</button>

                </form>
            </div>
        </div>
    </div>
</section>