<?php
/** @var array $viewModel */
?>

<header id="content-header">
    <section id="hero" aria-hidden="true">
        <div class="hero-image" style="--hero-image: url('/assets/images/hero/aht-hero-06.webp'); --hero-focus: 84% 44%;"></div>
        <div class="hero-image" style="--hero-image: url('/assets/images/hero/aht-hero-03.webp'); --hero-focus: 62% 42%;"></div>
        <div class="hero-image" style="--hero-image: url('/assets/images/hero/AHT-2_edit.webp'); --hero-focus: 50% 42%;"></div>
        <div class="hero-image" style="--hero-image: url('/assets/images/hero/aht-hero-04.webp'); --hero-focus: 28% 46%;"></div>
        <div class="hero-image" style="--hero-image: url('/assets/images/hero/esa-hero-01.webp'); --hero-focus: 50% 42%;"></div>
        <div class="hero-image" style="--hero-image: url('/assets/images/hero/aht-hero-01.webp'); --hero-focus: 48% 48%;"></div>
    </section>

    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <p>Dein Profil</p>

                <h1>
                    Hallo<?= !empty($viewModel['user']->name)
                        ? ', ' . htmlspecialchars($viewModel['user']->name)
                        : '' ?>
                </h1>

                <ul id="breadcrumb">
                    <?php foreach ($viewModel['breadcrumb'] ?? [] as $index => $breadcrumb): ?>
                        <?php if ($breadcrumb['url'] ?? false): ?>
                        <li>
                            <a href="<?= $breadcrumb['url'] ?>">
                                <?= $breadcrumb['title'] ?>
                            </a>
                        </li>
                        <li aria-hidden="true">></li>
                        <?php else: ?>
                        <li><?= $breadcrumb['title'] ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</header>

<section>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">

                <?php require_once __DIR__ . "/partials/general-messages.php"; ?>

                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            <form method="post" action="/profile" class="form-card">
                                <h2>Persönliche Informationen</h2>

                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>"
                                >

                                <div class="profile-fields">
                                    <div class="form-group">
                                        <label>
                                            Name
                                            <input
                                                type="text"
                                                name="name"
                                                value="<?= htmlspecialchars($viewModel['user']->name ?? '') ?>"
                                                placeholder="Ihr Name"
                                            >
                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <label>
                                            E-Mail
                                            <input
                                                type="email"
                                                name="email"
                                                value="<?= htmlspecialchars($viewModel['user']->email ?? '') ?>"
                                                required
                                            >
                                        </label>
                                    </div>
                                </div>

                                <button type="submit" class="button-primary">Speichern</button>
                            </form>
                        </div>
                        <div class="col">
                            <form method="post" action="/profile/password" class="form-card">

                                <h2>Passwort ändern</h2>

                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">

                                <div class="password-fields">
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
                                                tabindex="-1"
                                            >
                                                <img src="/assets/images/icons/eye.svg" data-alt-icon="/assets/images/icons/eye-slash.svg" alt="Passwort anzeigen" />
                                            </button>
                                        </div>
                                    </label>

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
                                                tabindex="-1"
                                            >
                                                <img src="/assets/images/icons/eye.svg" data-alt-icon="/assets/images/icons/eye-slash.svg" alt="Passwort anzeigen" />
                                            </button>
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

                                <button type="submit" class="button-primary">Passwort ändern</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>