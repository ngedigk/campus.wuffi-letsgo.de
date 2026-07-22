<header class="header">
    <div class="header-top">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 col-md-8 col-sm-12">
                    <div class="header-phone icon-text-left">
                        <img src="assets/images/icons/phone-alt.svg" aria-hidden="true" width="13px" height="13px">
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
                <div class="header-logo col-md-2 col-sm-2 col-xs-4">
                    <a href="index.php" class="logo" aria-label="Zur Startseite von WUFFI Let's Go!">
                        <img src="assets/images/Logo_quer.png" alt="Wuffi Let's Go!" width="150px" height="75px">
                        <span class="sr-only">Zur Startseite von WUFFI Let's Go!</span>
                    </a>
                </div>
                <div class="header-spacer col-md-8 col-sm-8 col-xs-8">
                </div>
                <div class="header-menu col-md-2 col-sm-2 col-xs-4">
                    <?php if (isLoggedIn()): ?>
                        <div class="profile-container" id="profileContainer">
                            <div class="profile-icon" onclick="toggleDropdown()">
                                <img src="assets/images/icons/user-solid-full.svg" alt="Benutzer Menu öffnen" width="40px" height="40px">
                            </div>
                            <div class="dropdown-menu">
                                <a href="profile.php">Profil</a>
                                <a href="settings.php">Einstellungen</a>
                                <?php if ($isAdmin): ?>
                                    <a href="admin.php">Admin Panel</a>
                                <?php endif; ?>
                                <form method="post" action="logout.php" style="margin: 0;">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                                    <button type="submit">Abmelden</button>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="auth-links">
                            <a href="index.php" style="margin-right: 15px; text-decoration: none; color: #333;">Anmelden</a>
                            <a href="register.php" style="text-decoration: none; color: #333;">Registrieren</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
        </div>
    </div>
    <div id="headerPlaceholder"></div>
</header>