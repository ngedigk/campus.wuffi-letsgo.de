<aside class="course-sidebar">
    <h2>Modules</h2>
    <ul class="module-list">
        <?php foreach ($modules as $module): ?>
            <?php
                $moduleId = (string)$module['id'];
                $isCurrentModule = (string)($currentModule['id'] ?? '') === $moduleId;
                $moduleUnlocked = $isCurrentModule || !empty($moduleUnlockState[$moduleId]);
            ?>
            <li class="module-item<?= $isCurrentModule ? ' active' : '' ?><?= $moduleUnlocked ? ' unlocked' : ' locked' ?>">
                <?php if ($moduleUnlocked || $isCurrentModule): ?>
                    <a href="<?= htmlspecialchars($courseService->buildCourseUrl($courseUuid, $module['id'], 0)) ?>">
                        <?= htmlspecialchars($module['title']) ?>
                    </a>
                <?php else: ?>
                    <span class="module-link disabled">
                        <?= htmlspecialchars($module['title']) ?>
                    </span>
                <?php endif; ?>
                <?php if ($isCurrentModule): ?>
                    <ul class="slide-list">
                        <?php foreach ($slidesForModule as $index => $slide): ?>
                            <?php
                                $slideId = (string)($slide['id'] ?? '');
                                $isCurrentSlide = !empty($currentSlide) && (string)$currentSlide['id'] === $slideId;
                                $isUnlocked = $slideId === '' || !empty($slideUnlockState[$slideId]);
                                $isVisited = $slideId !== '' && !empty($viewedSlideIds[$slideId]);
                            ?>
                            <li class="slide-item<?= $isCurrentSlide ? ' active' : '' ?><?= $isVisited ? ' visited' : '' ?><?= $isUnlocked ? ' unlocked' : ' locked' ?>">
                                <?php if ($isCurrentSlide || $isUnlocked): ?>
                                    <a href="<?= htmlspecialchars($courseService->buildCourseUrl($courseUuid, $module['id'], $index)) ?>">
                                        <?= htmlspecialchars($slide['title']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="slide-link disabled">
                                        <?= htmlspecialchars($slide['title']) ?>
                                    </span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</aside>
