<h1><?= htmlspecialchars($course['title']) ?></h1>

<p><?= nl2br(htmlspecialchars($course['description'])) ?></p>

<hr>

<?php if (!empty($isCourseLocked)): ?>
    <div class="course-errors">
        <p>This course is locked until the previous course is completed.</p>
    </div>
<?php else: ?>
    <?php if ($errors): ?>
        <div class="course-errors">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!$modules): ?>
        <p>No course modules are configured yet.</p>
    <?php else: ?>
        <div class="course-layout">
            <?php require __DIR__ . '/course/sidebar.php'; ?>

            <main class="course-main">
                <div class="slide-panel">
                    <div class="slide-header">
                        <div>
                            <h2><?= htmlspecialchars($currentModule['title'] ?? 'Module') ?></h2>
                            <p>Slide <?= $currentSlideIndex + 1 ?> of <?= count($slidesForModule) ?></p>
                        </div>
                    </div>

                    <?php require __DIR__ . '/course/slide.php'; ?>

                    <div class="slide-navigation">
                        <?php if ($prevModule !== null): ?>
                            <a class="nav-button" href="<?= htmlspecialchars($courseService->buildCourseUrl($courseUuid, $prevModule['id'], $prevSlideIndex)) ?>">Previous</a>
                        <?php endif; ?>
                        <?php if ($nextModule !== null): ?>
                            <a class="nav-button" href="<?= htmlspecialchars($courseService->buildCourseUrl($courseUuid, $nextModule['id'], $nextSlideIndex)) ?>">Next</a>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    <?php endif; ?>
<?php endif; ?>