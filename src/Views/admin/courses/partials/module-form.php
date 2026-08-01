<?php
/** @var array $viewModel */
?>
<div class="module-details-section">
    <form
        id="module-form"
        method="post"
        action="admin.php?page=courses&course_id=<?= urlencode($viewModel['selectedCourse']->uuid ?? '') ?>&module_id=<?= urlencode($viewModel['selectedModule']->id ?? '') ?>"
    >
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">
        <input type="hidden" name="action" value="update_module">
        <input type="hidden" name="module_id" value="<?= htmlspecialchars($viewModel['selectedModule']->id ?? '') ?>">

        <div class="section-header">
            <h3>Modul Details</h3>
            <div class="panel-actions">
                <button
                    type="button"
                    class="btn btn-danger btn-small"
                    onclick="deleteModule(<?= $viewModel['selectedModule']->id ?? '' ?>)"
                >
                    Modul löschen
                </button>

                <button type="submit" class="btn btn-primary btn-small">
                    Modul speichern
                </button>
            </div>
        </div>
        <div class="module-form">
            <div class="form-group">
                <label for="module-title">Titel *</label>
                <input type="text" id="module-title" name="title" value="<?= htmlspecialchars($viewModel['selectedModule']->title ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="module-sort">Reihenfolge</label>
                <input type="number" id="module-sort" name="sort_order" value="<?= htmlspecialchars($viewModel['selectedModule']->sortOrder ?? 0) ?>" min="0">
            </div>
        </div>
    </form>
</div>
