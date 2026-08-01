<?php
/** @var array $viewModel */
?>
<form
    id="course-form"
    method="post"
    action="admin.php?page=courses&course_id=<?= urlencode($viewModel['selectedCourse']->uuid ?? '') ?>"
>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">
    <input type="hidden" name="action" value="update_course">
    <input type="hidden" name="course_id" value="<?= htmlspecialchars($viewModel['selectedCourse']->uuid ?? '') ?>">

    <div class="panel-header">
        <h3>Kurs Details</h3>
        <div class="panel-actions">
            <button
                type="button"
                class="btn btn-danger btn-small"
                onclick="deleteCourse('<?= $viewModel['selectedCourse']->uuid ?? '' ?>')"
            >
                Kurs löschen
            </button>
            <button type="submit" class="btn btn-primary btn-small">
                Kurs speichern
            </button>
        </div>
    </div>
        
    <div class="course-form">
        <div class="form-row">
            <div class="form-group">
                <label for="course-title">Titel *</label>
                <input type="text" id="course-title" name="title" value="<?= htmlspecialchars($viewModel['selectedCourse']->title ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="prerequisite">Voraussetzungs-Kurs</label>
                <select id="prerequisite" name="prerequisite_course_id">
                    <option value="">Voraussetzungs-Kurs auswählen (Optional)</option>
                    <?php foreach ($viewModel['allCourses'] ?? [] as $course): ?>
                        <?php if ($course->uuid !== $viewModel['selectedCourse']->uuid ?? ''): ?>
                            <option value="<?= htmlspecialchars($course->uuid) ?>"
                                <?= (($viewModel['selectedCourse']->prerequisiteCourseId ?? '') ?? '') === $course->uuid ? 'selected' : '' ?>>
                                <?= htmlspecialchars($course->title) ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="course-description">Beschreibung</label>
            <textarea id="course-description" name="description" rows="3"><?= htmlspecialchars($viewModel['selectedCourse']->description ?? '' ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <label for="course-sort">Reihenfolge</label>
            <input type="number" id="course-sort" name="sort_order" value="<?= htmlspecialchars($viewModel['selectedCourse']->sortOrder ?? 0) ?>" min="0">
        </div>
    </div>
</form>
