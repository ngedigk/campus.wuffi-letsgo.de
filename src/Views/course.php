<?php
/** @var Course $course */
/** @var array $errors */
/** @var Module $currentModule */
/** @var int $currentSlideIndex */
/** @var array $slidesForModule */
/** @var string $prevUrl */
/** @var string $nextUrl */
/** @var bool $isLastSlide */
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
                <h2 id="courses-heading"><?= htmlspecialchars($course->title) ?></h2>

                <p><?= nl2br(htmlspecialchars($course->description)) ?></p>

                <?php if ($errors): ?>
                    <div class="course-errors">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!$currentModule): ?>
                    <p>No course modules are configured yet.</p>
                <?php else: ?>
                    <div class="course-layout">
                        <?php require __DIR__ . '/course/sidebar.php'; ?>

                        <main class="course-main">
                            <div class="slide-panel">
                                <div class="slide-navigation">
                                    <?php if ($prevUrl): ?>
                                        <a href="<?= htmlspecialchars($prevUrl) ?>" class="btn prev-slide">
                                            <img src="assets/images/icons/chevron-left-solid-full.svg" width="28" height="28">
                                        </a>
                                    <?php endif; ?>

                                    <p>Seite <?= $currentSlideIndex + 1 ?> von <?= count($slidesForModule) ?></p>
                                    
                                    <?php if ($nextUrl): ?>
                                        <a href="<?= htmlspecialchars($nextUrl) ?>" class="btn next-slide">
                                            <img src="assets/images/icons/angle-right-solid-full.svg" width="28" height="28">
                                        </a>
                                    <?php elseif ($isLastSlide): ?>
                                        <a href="index.php" class="btn finish-course">
                                            Back to course overview →
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