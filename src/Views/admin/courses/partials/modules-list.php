<?php
/** @var array $viewModel */
?>
<div class="modules-section">
    <div class="section-header">
        <h3>Module</h3>
        <button class="btn btn-primary btn-small" onclick="addModule('<?= htmlspecialchars($viewModel['selectedCourse']->uuid ?? '') ?>')">
            + Modul hinzufügen
        </button>
    </div>
    <div class="modules-list" id="modulesList">
        <?php if (!empty($viewModel['selectedCourse']->modules ?? [])): ?>
            <?php foreach ($viewModel['selectedCourse']->modules ?? [] as $index => $module): ?>
                <div class="module-item" data-module-id="<?= htmlspecialchars($module->id) ?>">
                    <span class="module-number"><?= $index + 1 ?>.</span>
                    <span class="module-title"><?= htmlspecialchars($module->title) ?></span>
                    <div class="module-actions">
                        <a
                            href="admin.php?page=courses&course_id=<?= urlencode($viewModel['selectedCourse']->uuid ?? '') ?>&module_id=<?= urlencode($module->id) ?>"
                            class="btn btn-small"
                            title="Modul bearbeiten"
                        >
                            ✏️
                        </a>
                        <button onclick="deleteModule(<?= $module->id ?>)">🗑</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-modules">
                <p>Keine Module. Klicken Sie auf "Modul hinzufügen", um ein Modul zu erstellen.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
