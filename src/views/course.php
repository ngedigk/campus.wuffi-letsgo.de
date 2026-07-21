<h1><?= htmlspecialchars($course->title) ?></h1>

<p><?= nl2br(htmlspecialchars($course->description)) ?></p>

<hr>

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
                <div class="slide-header">
                    <div>
                        <h2><?= htmlspecialchars($currentModule->title ?? 'Module') ?></h2>
                        <p>Slide <?= $currentSlideIndex + 1 ?> of <?= count($slidesForModule) ?></p>
                    </div>
                </div>

                <?php require __DIR__ . '/course/slide.php'; ?>

                <div class="slide-navigation">
                    <?php if ($prevUrl): ?>
                        <a href="<?= htmlspecialchars($prevUrl) ?>" class="btn prev-slide">
                            ← Previous
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($nextUrl): ?>
                        <a href="<?= htmlspecialchars($nextUrl) ?>" class="btn next-slide">
                            Next →
                        </a>
                    <?php elseif ($isLastSlide): ?>
                        <a href="index.php" class="btn finish-course">
                            Back to course overview →
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
<?php endif; ?>