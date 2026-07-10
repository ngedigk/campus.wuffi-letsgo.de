<aside class="course-sidebar">
    <h2>Modules</h2>
    <ul class="module-list">
        <?php foreach ($modules as $module): ?>
            <li class="module-item<?= (string)$module['id'] === (string)$currentModule['id'] ? ' active' : '' ?>">
                <a href="<?= htmlspecialchars($courseService->buildCourseUrl($courseUuid, $module['id'], 0)) ?>">
                    <?= htmlspecialchars($module['title']) ?>
                </a>
                <?php if ($module['id'] === $currentModule['id']): ?>
                    <ul class="slide-list">
                        <?php foreach ($slidesForModule as $index => $slide): ?>
                            <li class="slide-item<?= $index === $currentSlideIndex ? ' active' : '' ?>">
                                <a href="<?= htmlspecialchars($courseService->buildCourseUrl($courseUuid, $module['id'], $index)) ?>">
                                    <?= $slide['title'] ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</aside>
