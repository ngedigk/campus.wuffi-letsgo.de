<?php
/** @var array $viewModel */
?>

<section aria-labelledby="courses-heading">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">

                <p class="heading-meta">Kursmaterial</p>
                <h2 id="courses-heading"><?= htmlspecialchars($viewModel['courseTitle']) ?></h2>

                <p><?= nl2br(htmlspecialchars($viewModel['courseDescription'])) ?></p>

                <?php if (!$viewModel['currentModule'] ?? null): ?>
                    <p>Kursmodule sind noch nicht konfiguriert.</p>
                <?php else: ?>
                    <div id="course-content">
                        <div class="course-layout">
                            <?php require __DIR__ . '/sidebar.php'; ?>
                            <main id="course-main">
                                <div class="slide-panel">
                                    <div class="slide-navigation">
                                        <?php if ($viewModel['prevUrl'] ?? ''): ?>
                                        <a href="<?= htmlspecialchars($viewModel['prevUrl']) ?>" class="btn prev-slide course-nav-link" data-course-nav>
                                            <img src="/assets/images/icons/chevron-left-solid-full.svg" width="28" height="28">
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($viewModel['moduleSlideCount'] > 0): ?>
                                        <p>Seite <?= ($viewModel['currentSlideIndex'] ?? 0) + 1 ?> von <?= $viewModel['moduleSlideCount'] ?></p>
                                    <?php endif; ?>

                                    <?php if ($viewModel['nextUrl'] ?? ''): ?>
                                        <a href="<?= htmlspecialchars($viewModel['nextUrl']) ?>" class="btn next-slide course-nav-link" data-course-nav>
                                            <img src="/assets/images/icons/angle-right-solid-full.svg" width="28" height="28">
                                        </a>
                                    <?php elseif ($viewModel['isLastSlide'] ?? false): ?>
                                        <a href="/" class="btn finish-course course-nav-link" data-course-nav>
                                            Zurück zur Kursübersicht →
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <?php require __DIR__ . '/slide.php'; ?>
                            </main>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>