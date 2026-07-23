<?php
/** @var Course $course */
/** @var Module $currentModule */
?>
<aside class="course-sidebar">
    <h2>Modules</h2>
    <ul class="module-list">
        <?php foreach ($course->modules as $moduleIndex => $module): ?>
            <?php
                $isCurrentModule = $currentModule->id === $module->id;

                $isModuleLocked = true;
                if (!empty($module->slides)) {
                    $firstSlideId = $module->slides[0]->id;
                    $isModuleLocked = !in_array($firstSlideId, $allowedSlideIds ?? []);
                }
            ?>

            <li class="module-item<?= $isCurrentModule ? ' active' : '' ?><?php if ($isModuleLocked) echo ' locked'; ?>">
                <?php if ($isModuleLocked): ?>
                    <span class="module-title"><?= htmlspecialchars($module->title) ?></span>
                <?php else: ?>
                    <a href="<?= htmlspecialchars("course.php?id={$course->uuid}&module={$moduleIndex}&slide=0") ?>">
                        <?= htmlspecialchars($module->title) ?>
                    </a>
                <?php endif; ?>
                
                <?php if ($isCurrentModule): ?>
                    <ul class="slide-list">
                        <?php foreach ($module->slides as $index => $slide): ?>
                            <?php
                                $slideId = (string)($slide->id ?? '');
                                $isCurrentSlide = !empty($currentSlide) && (string)$currentSlide->id === $slideId;
                                $isSlideAllowed = in_array((int)$slideId, $allowedSlideIds ?? []);
                            ?>
                            <li class="slide-item<?= $isCurrentSlide ? ' active' : '' ?><?= $isSlideAllowed ? '' : ' locked' ?>">
                                <?php if ($isSlideAllowed): ?>
                                    <a href="<?= htmlspecialchars("course.php?id={$course->uuid}&module={$moduleIndex}&slide=" . $index) ?>">
                                        <?= htmlspecialchars($slide->title) ?>
                                        <?php if (in_array($slide->id, $visitedSlideIds ?? [])): ?>
                                            <span class="visited-indicator">✓</span>
                                        <?php endif; ?>
                                    </a>
                                <?php else: ?>
                                    <span class="slide-title"><?= htmlspecialchars($slide->title) ?></span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</aside>