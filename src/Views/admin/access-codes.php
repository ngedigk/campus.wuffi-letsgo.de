<?php
/** @var array $viewModel */
?>
<div class="page-actions">
    <button class="btn btn-primary" onclick="document.getElementById('createAccessCodeModal').style.display='flex'">
        + Access Code erstellen
    </button>
</div>

<form
    id="delete-access-code-form"
    method="post"
    action="admin.php?page=access-codes"
>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">
    <input type="hidden" name="action" value="delete_access_code">
    <input type="hidden" id="delete-access-code-id" name="access_code_id" value="">
</form>

<div class="list-grid access-codes-list">
    <div class="list-item header-row">
        <div class="cell code-cell">Code</div>
        <div class="cell course-cell">Kurs</div>
        <div class="cell status-cell">Status</div>
        <div class="cell actions-cell">Aktionen</div>
    </div>
    <?php if (empty($viewModel['accessCodes'] ?? [])): ?>
        <div class="empty-state" style="grid-column: 1 / -1;">Keine Access Codes gefunden. Erstellen Sie Ihren ersten Access Code!</div>
    <?php else: ?>
        <?php foreach ($viewModel['accessCodes'] ?? [] as $code): ?>
            <div class="list-item">
                <div class="cell code-cell">
                    <code><?= htmlspecialchars($code['code']) ?></code>
                </div>
                <div class="cell course-cell">
                    <?php if (!empty($code['course_title'])): ?>
                        <a href="admin.php?page=courses&course_id=<?= $code['course_id'] ?>">
                            <?= htmlspecialchars($code['course_title']) ?>
                        </a>
                    <?php else: ?>
                        Unbekannter Kurs
                    <?php endif; ?>
                </div>
                <div class="cell status-cell">
                    <?php if ($code['claimed']): ?>
                        <span class="status-badge active">Eingelöst</span>
                    <?php else: ?>
                        <span class="status-badge pending">Nicht eingelöst</span>
                    <?php endif; ?>
                </div>
                <div class="cell actions-cell">
                    <div class="cell actions-cell">
                        <button class="btn btn-small"
                                onclick="editAccessCode('<?= $code['id'] ?>')"
                                data-course-id="<?= htmlspecialchars($code['course_id']) ?>"
                                data-code="<?= htmlspecialchars($code['code']) ?>">
                            Bearbeiten
                        </button>
                        <button class="btn btn-small btn-danger" onclick="deleteAccessCode('<?= $code['id'] ?>')">Löschen</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Create Access Code Modal -->
<div id="createAccessCodeModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Access Code erstellen</h3>
            <span class="close" onclick="document.getElementById('createAccessCodeModal').style.display='none'">&times;</span>
        </div>
        <form method="post" action="admin.php?page=access-codes">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">
            <input type="hidden" name="action" value="create_access_code">
            
            <div class="form-group">
                <label for="access-code">Access Code</label>
                <input type="text" id="access-code" name="code" placeholder="Access Code eingeben" required>
            </div>
            
            <div class="form-group">
                <label for="access-course">Kurs</label>
                <select id="access-course" name="course_id" required>
                    <option value="">Kurs auswählen</option>
                    <?php foreach ($viewModel['allCourses'] ?? [] as $course): ?>
                        <option value="<?= htmlspecialchars($course->uuid) ?>">
                            <?= htmlspecialchars($course->title) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('createAccessCodeModal').style.display='none'">Abbrechen</button>
                <button type="submit" class="btn btn-primary">Code erstellen</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Access Code Modal -->
<div id="editAccessCodeModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Access Code</h3>
            <span class="close" onclick="document.getElementById('editAccessCodeModal').style.display='none'">&times;</span>
        </div>
        <form method="post" action="admin.php?page=access-codes">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">
            <input type="hidden" name="action" value="update_access_code">
            <input type="hidden" id="edit-access-code-id" name="access_code_id" value="">
            
            <div class="form-group">
                <label for="edit-access-code">Access Code</label>
                <input type="text" id="edit-access-code" name="code" placeholder="Enter access code" required>
                <small>Will be automatically converted to uppercase</small>
            </div>
            
            <div class="form-group">
                <label for="edit-access-course">Course</label>
                <select id="edit-access-course" name="course_id" required>
                    <option value="">Select a course</option>
                    <?php foreach ($viewModel['allCourses'] ?? [] as $course): ?>
                        <option value="<?= htmlspecialchars($course->uuid) ?>">
                            <?= htmlspecialchars($course->title) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('editAccessCodeModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Code</button>
            </div>
        </form>
    </div>
</div>