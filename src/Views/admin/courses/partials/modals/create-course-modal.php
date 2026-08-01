<?php
/** @var array $viewModel */
?>
<div id="createCourseModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Neuen Kurs erstellen</h3>
            <span class="close" onclick="document.getElementById('createCourseModal').style.display='none'">&times;</span>
        </div>
        <form method="post" action="admin.php?page=courses">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">
            <input type="hidden" name="action" value="create_course">
            
            <div class="form-group">
                <label for="new-course-title">Kurs Titel *</label>
                <input type="text" id="new-course-title" name="title" placeholder="Enter course title" required>
            </div>
            
            <div class="form-group">
                <label for="new-course-description">Beschreibung</label>
                <textarea id="new-course-description" name="description" placeholder="Course description" rows="4"></textarea>
            </div>
            
            <div class="form-group">
                <label for="new-course-prerequisite">Voraussetzungs-Kurs</label>
                <select id="new-course-prerequisite" name="prerequisite_course_id">
                    <option value="">Keine Voraussetzung</option>
                    <?php foreach ($viewModel['allCourses'] ?? [] as $course): ?>
                        <option value="<?= htmlspecialchars($course->uuid) ?>">
                            <?= htmlspecialchars($course->title) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('createCourseModal').style.display='none'">Abbrechen</button>
                <button type="submit" class="btn btn-primary">Kurs erstellen</button>
            </div>
        </form>
    </div>
</div>