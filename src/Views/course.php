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
                <p>Campus für fachtheoretische Wissensvermittlung</p>
                <h1>Entdecken. Verstehen. Wissen testen.</h1>

                <ul id="breadcrumb">
                    <li>Startseite</li>
                    <li aria-hidden="true">></li>
                    <li>Campus</li>
                    <li aria-hidden="true">></li>
                    <li>Kursübersicht</li>
                </ul>
            </div>
        </div>
    </div>
</header>
<section aria-labelledby="courses-heading">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">

                <p class="heading-meta">Kursmaterial</p>
                <h2 id="courses-heading"><?= htmlspecialchars($viewModel['course']->title) ?></h2>

                <p><?= nl2br(htmlspecialchars($viewModel['course']->description)) ?></p>

                <?php if ($viewModel['errors'] ?? []): ?>
                    <div class="course-errors">
                        <ul>
                            <?php foreach ($viewModel['errors'] ?? [] as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!$viewModel['currentModule'] ?? null): ?>
                    <p>Kursmodule sind noch nicht konfiguriert.</p>
                <?php else: ?>
                    <div class="course-layout">
                        <?php require __DIR__ . '/course/sidebar.php'; ?>

                        <main id="course-main">
                            <div id="course-main-marker"></div>
                            <div class="slide-panel">
                                <div class="slide-navigation">
                                    <?php if ($viewModel['prevUrl'] ?? ''): ?>
                                        <a href="<?= htmlspecialchars($viewModel['prevUrl'] . "#course-main-marker" ?? '') ?>" class="btn prev-slide">
                                            <img src="assets/images/icons/chevron-left-solid-full.svg" width="28" height="28">
                                        </a>
                                    <?php endif; ?>

                                    <?php if (count($viewModel['slidesForModule'] ?? []) > 0): ?>
                                        <p>Seite <?= ($viewModel['currentSlideIndex'] ?? 0) + 1 ?> von <?= count($viewModel['slidesForModule'] ?? []) ?></p>
                                    <?php endif; ?>

                                    <?php if ($viewModel['nextUrl'] ?? ''): ?>
                                        <a href="<?= htmlspecialchars($viewModel['nextUrl'] . "#course-main-marker" ?? '') ?>" class="btn next-slide">
                                            <img src="assets/images/icons/angle-right-solid-full.svg" width="28" height="28">
                                        </a>
                                    <?php elseif ($viewModel['isLastSlide'] ?? false): ?>
                                        <a href="index.php" class="btn finish-course">
                                            Zurück zur Kursübersicht →
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <?php require __DIR__ . '/course/slide.php'; ?>
                            </div>
                        </main>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>