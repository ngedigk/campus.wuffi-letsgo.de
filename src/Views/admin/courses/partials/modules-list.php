<div class="modules-section">
    <div class="section-header">
        <h3>Modules</h3>
        <button class="btn btn-primary btn-small" onclick="addModule('<?= htmlspecialchars($selectedCourse->uuid) ?>')">
            + Add Module
        </button>
    </div>
    <div class="modules-list" id="modulesList">
        <?php if (!empty($selectedCourse->modules)): ?>
            <?php foreach ($selectedCourse->modules as $index => $module): ?>
                <div class="module-item <?= $module->id === $selectedModuleId ? 'active' : '' ?>" 
                     data-module-id="<?= htmlspecialchars($module->id) ?>">
                    <span class="module-number"><?= $index + 1 ?>.</span>
                    <span class="module-title"><?= htmlspecialchars($module->title) ?></span>
                    <div class="module-actions">
                        <a
                            href="admin.php?page=courses&course_id=<?= urlencode($selectedCourse->uuid) ?>&module_id=<?= urlencode($module->id) ?>"
                            class="btn btn-small"
                            title="Edit Module"
                        >
                            ✏️
                        </a>
                        <button onclick="deleteModule(<?= $module->id ?>)">🗑</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-modules">
                <p>No modules yet. Click "Add Module" to get started.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
