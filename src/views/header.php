<header class="header">
    <div class="header-top">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 col-md-8 col-sm-12">
                    <div class="header-phone icon-text-left">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M497.39 361.8l-112-48a24 24 0 0 0-28 6.9l-49.6 60.6A370.66 370.66 0 0 1 130.6 204.11l60.6-49.6a23.94 23.94 0 0 0 6.9-28l-48-112A24.16 24.16 0 0 0 122.6.61l-104 24A24 24 0 0 0 0 48c0 256.5 207.9 464 464 464a24 24 0 0 0 23.4-18.6l24-104a24.29 24.29 0 0 0-14.01-27.6z"/></svg>
                        <a href="tel:+4917630136957">0176 3013 6957</a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-5 col-md-4 col-sm-12 ">

                </div>
            </div>
        </div>
    </div>
    <div id="headerMain" class="header-main">
        <div class="container">
            <div class="row">
                <div class="col-md-2 col-sm-2 col-xs-4">
                    <a href="index.php" class="logo" aria-label="Zur Startseite von WUFFI Let's Go!">
                        <img src="assets/images/Logo_quer.png" alt="Wuffi Let's Go!">
                        <span class="sr-only">Zur Startseite von WUFFI Let's Go!</span>
                    </a>
                </div>
                <div class="col-md-8 col-sm-8 col-xs-8">
                </div>
                <div class="col-md-2 col-sm-2 col-xs-4">
                    <?php if (isLoggedIn()): ?>
                        <div class="profile-container" id="profileContainer">
                            <div class="profile-icon" onclick="toggleDropdown()">
                                <svg viewBox="0 0 24 24">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            </div>
                            <div class="dropdown-menu">
                                <a href="profile.php">Profile</a>
                                <a href="settings.php">Settings</a>
                                <?php if ($isAdmin): ?>
                                    <a href="admin.php">Admin Panel</a>
                                <?php endif; ?>
                                <form method="post" action="logout.php" style="margin: 0;">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                                    <button type="submit">Logout</button>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="auth-links">
                            <a href="index.php" style="margin-right: 15px; text-decoration: none; color: #333;">Login</a>
                            <a href="register.php" style="text-decoration: none; color: #333;">Register</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
        </div>
    </div>
    <div id="headerPlaceholder"></div>
</header>