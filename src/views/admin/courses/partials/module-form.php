<?php
/** @var Course $selectedCourse */
/** @var Module $selectedModule */
?>
<div class="module-details-section">
    <form
        id="module-form"
        method="post"
        action="admin.php?page=courses&course_id=<?= urlencode($selectedCourse->uuid) ?>&module_id=<?= urlencode($selectedModule->id) ?>"
    >
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <input type="hidden" name="action" value="update_module">
        <input type="hidden" name="module_id" value="<?= htmlspecialchars($selectedModule->id) ?>">

        <div class="section-header">
            <h3>Module Details</h3>
            <div class="panel-actions">
                <button
                    type="button"
                    class="btn btn-danger btn-small"
                    onclick="deleteModule(<?= $selectedModule->id ?>)"
                >
                    Delete Module
                </button>

                <button type="submit" class="btn btn-primary btn-small">
                    Save Module
                </button>
            </div>
        </div>
        <div class="module-form">
            <div class="form-group">
                <label for="module-title">Title *</label>
                <input type="text" id="module-title" name="title" value="<?= htmlspecialchars($selectedModule->title) ?>">
            </div>
            <div class="form-group">
                <label for="module-sort">Sort Order</label>
                <input type="number" id="module-sort" name="sort_order" value="<?= htmlspecialchars($selectedModule->sortOrder) ?>" min="0">
            </div>
        </div>
    </form>
</div>
