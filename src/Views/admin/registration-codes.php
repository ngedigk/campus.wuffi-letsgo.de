<?php
/** @var array $viewModel */
?>
<div class="page-actions">
    <button class="btn btn-primary" onclick="document.getElementById('createRegistrationCodeModal').style.display='flex'">
        + Create Registration Code
    </button>
</div>

<form
    id="delete-registration-code-form"
    method="post"
    action="admin.php?page=registration-codes"
>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">
    <input type="hidden" name="action" value="delete_registration_code">
    <input type="hidden" id="delete-registration-code-id" name="registration_code_id" value="">
</form>

<div class="list-grid registration-codes-list">
    <div class="list-item header-row">
        <div class="cell code-cell">Code</div>
        <div class="cell courses-cell">Courses</div>
        <div class="cell status-cell">Status</div>
        <div class="cell used-at-cell">Used At</div>
        <div class="cell actions-cell">Actions</div>
    </div>
    <?php if (empty($viewModel['registrationCodes'] ?? [])): ?>
        <div class="empty-state" style="grid-column: 1 / -1;">No registration codes found. Create your first registration code!</div>
    <?php else: ?>
        <?php foreach ($viewModel['registrationCodes'] ?? [] as $code): ?>
            <div class="list-item">
                <div class="cell code-cell">
                    <code><?= htmlspecialchars($code['code']) ?></code>
                </div>
                <div class="cell courses-cell">
                    <?php if (!empty($code['course_titles'])): ?>
                        <ul class="course-list">
                            <?php foreach ($code['course_titles'] as $course): ?>
                                <li>
                                    <a href="admin.php?page=courses&course_id=<?= htmlspecialchars($course['id']) ?>">
                                        <?= htmlspecialchars($course['title']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        No Courses
                    <?php endif; ?>
                </div>
                <div class="cell status-cell">
                    <?php if ($code['used_by_user_id']): ?>
                        <span class="status-badge active">Used</span>
                    <?php else: ?>
                        <span class="status-badge pending">Available</span>
                    <?php endif; ?>
                </div>
                <div class="cell used-at-cell">
                    <?php if ($code['used_at']): ?>
                        <?= htmlspecialchars($code['used_at']) ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </div>
                <div class="cell actions-cell">
                    <button class="btn btn-small" 
                            onclick="editRegistrationCode('<?= $code['id'] ?>')" 
                            data-course-ids="<?= !empty($code['course_titles']) ? implode(',', array_column($code['course_titles'], 'id')) : '' ?>">
                        Edit
                    </button>
                    <button class="btn btn-small btn-danger" onclick="deleteRegistrationCode('<?= $code['id'] ?>')">Delete</button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Create Registration Code Modal -->
<div id="createRegistrationCodeModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Create Registration Code</h3>
            <span class="close" onclick="document.getElementById('createRegistrationCodeModal').style.display='none'">&times;</span>
        </div>
        <form method="post" action="admin.php?page=registration-codes">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">
            <input type="hidden" name="action" value="create_registration_code">
            
            <div class="form-group">
                <label for="reg-code">Registration Code</label>
                <input type="text" id="reg-code" name="code" placeholder="Enter registration code" required>
                <small>Will be automatically converted to uppercase</small>
            </div>
            
            <div class="form-group">
                <label>Courses</label>
                <div class="course-checkboxes">
                    <?php foreach ($viewModel['allCourses'] ?? [] as $course): ?>
                        <label class="checkbox-label">
                            <input type="checkbox" name="course_ids[]" value="<?= htmlspecialchars($course->uuid) ?>">
                            <?= htmlspecialchars($course->title) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                <small>Select at least one course</small>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('createRegistrationCodeModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Code</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Registration Code Modal -->
<div id="editRegistrationCodeModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Registration Code</h3>
            <span class="close" onclick="document.getElementById('editRegistrationCodeModal').style.display='none'">&times;</span>
        </div>
        <form method="post" action="admin.php?page=registration-codes">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">
            <input type="hidden" name="action" value="update_courses_to_registration_code_assignment">
            <input type="hidden" id="edit-registration-code-id" name="registration_code_id" value="">
            
            <div class="form-group">
                <label>Assign Courses</label>
                <div class="course-checkboxes">
                    <?php foreach ($viewModel['allCourses'] ?? [] as $course): ?>
                        <label class="checkbox-label">
                            <input type="checkbox" name="course_ids[]" value="<?= htmlspecialchars($course->uuid) ?>" class="edit-course-checkbox">
                            <?= htmlspecialchars($course->title) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                <small>Only courses not currently assigned will be added</small>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('editRegistrationCodeModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Courses</button>
            </div>
        </form>
    </div>
</div>
