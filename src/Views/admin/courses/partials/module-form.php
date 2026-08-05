<?php
/** @var array $viewModel */
?>
<div class="section-header">
    <h3>Modul Details</h3>
    
    <div class="panel-actions">
        <button
            type="button"
            class="btn btn-danger btn-small"
            data-action="delete-module"
            data-course-id="<?= $viewModel['selectedCourse']->uuid ?? '' ?>"
            data-module-id="<?= $viewModel['selectedModule']->id ?? '' ?>"
        >
            Modul löschen
        </button>
        <button type="submit" form="module-form" class="btn btn-primary btn-small">Modul speichern</button>
    </div>
</div>

<?php
$updateModuleAction = sprintf(
    '/admin/courses/%s/modules/%s/update',
    htmlspecialchars($viewModel['selectedCourse']->uuid ?? ''),
    htmlspecialchars($viewModel['selectedModule']->id ?? ''),
);
?>

<form
    id="module-form"
    method="post"
    action="<?= $updateModuleAction ?>"
>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">

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
