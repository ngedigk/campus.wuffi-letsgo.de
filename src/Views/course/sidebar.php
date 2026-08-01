<?php
/** @var array $viewModel */
?>
<aside class="course-sidebar">
    <h2>Kursmodule</h2>
    <ul class="module-list">
        <?php foreach ($viewModel['courseSidebar'] ?? [] as $module): ?>

            <li class="module-item<?= $module->isActive ? ' active' : '' ?><?php if ($module->isLocked) echo ' locked'; ?>">
                <?php if ($module->isLocked): ?>
                    <span class="module-title"><?= htmlspecialchars($module->title) ?></span>
                <?php else: ?>
                    <a href="<?= htmlspecialchars($module->url) . "#course-main-marker"?>">
                        <?= htmlspecialchars($module->title) ?>
                    </a>
                <?php endif; ?>
                
                <?php if ($module->isActive): ?>
                    <ul class="slide-list">
                        <?php foreach ($module->slides as $slide): ?>
                            <li class="slide-item<?= $slide->isActive ? ' active' : '' ?><?= $slide->isLocked ? '' : ' locked' ?>">
                                <?php if ($slide->isLocked): ?>
                                    <img src="assets/images/icons/paw-solid-full-inactive.svg" aria-hidden="true" width="15" height="15">
                                    <span class="slide-title"><?= htmlspecialchars($slide->title) ?></span>
                                <?php else: ?>
                                    <img src="assets/images/icons/paw-solid-full.svg" aria-hidden="true" width="15" height="15">
                                    <a href="<?= htmlspecialchars($slide->url) . "#course-main-marker" ?>">
                                        <?= htmlspecialchars($slide->title) ?>
                                        <?php if ($slide->isVisited): ?>
                                            <span class="visited-indicator">
                                                <img src="assets/images/icons/check-solid-full-green.svg" aria-hidden="true" width="15" height="15">
                                            </span>
                                        <?php endif; ?>
                                    </a>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</aside>