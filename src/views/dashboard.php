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
                <p>Campus für fachtheoretische Wissensvermittlung</p>
                <h1>Herzlich Willkommen<?= !empty($user['name']) ? ' ' . htmlspecialchars($user['name']) : '' ?>!</h1>

                <ul id="breadcrumb">
                    <li>Startseite</li>
                    <li aria-hidden="true">></li>
                    <li>Campus</li>
                </ul>
            </div>
        </div>
    </div>
</header>

<section aria-labelledby="courses-heading">

    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <p class="heading-meta">Dashboard</p>

                <h2 id="courses-heading">Deine Kurse</h2>

                <?php if (!$courses): ?>
                <p>You don't have any courses yet.</p>
                <?php else: ?>
                    <div class="course-list">
                        <?php foreach ($courses as $course): ?>
                            <?php
                            $courseCardClass = 'course-card';
                            if (!empty($course->isCompleted)) {
                                $courseCardClass .= ' completed';
                            } elseif (empty($course->isUnlocked)) {
                                $courseCardClass .= ' locked';
                            }
                            ?>
                            <article class="<?= htmlspecialchars($courseCardClass) ?>">
                                <div class="card-image"></div>
                                <div class="card-text">
                                    <h3><?= htmlspecialchars($course->title) ?></h3>
                                    <div class="course-description"><?= htmlspecialchars($course->description) ?></div>
                                    <?php if (!empty($course->isUnlocked)): ?>
                                        <a href="course.php?id=<?= urlencode($course->uuid) ?>" class="button-primary">
                                            Zum Kurs
                                        </a>
                                    <?php else: ?>
                                        <span>Als nächstes an der Reihe<img src="/assets/images/icons/lock-solid-full.svg" aria-hidden="true"></span>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<section id="redeem-section">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">

                <h4>Weitere Kurse</h4>

                <?php if ($redeemError): ?>
                    <p style="color: red; font-weight: bold;"><?= htmlspecialchars($redeemError) ?></p>
                <?php endif; ?>

                <?php if ($redeemSuccess): ?>
                    <p style="color: green; font-weight: bold;"><?= htmlspecialchars($redeemSuccess) ?></p>
                <?php endif; ?>

                <form method="post" action="redeem.php">

                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">

                    <input type="text" name="code" placeholder="Freischaltcode" required>

                    <button type="submit" class="button-primary">Freischalten</button>

                </form>
            </div>
        </div>
    </div>
</section>