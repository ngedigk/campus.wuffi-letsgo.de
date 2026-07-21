<div class="page-actions">
    <button class="btn btn-primary" onclick="document.getElementById('createAccessCodeModal').style.display='flex'">
        + Create Access Code
    </button>
</div>

<form
    id="delete-access-code-form"
    method="post"
    action="admin.php?page=access-codes"
>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
    <input type="hidden" name="action" value="delete_access_code">
    <input type="hidden" id="delete-access-code-id" name="access_code_id" value="">
</form>

<div class="data-table">
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Course</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($accessCodes)): ?>
                <tr>
                    <td colspan="5" class="empty-state">No access codes found. Create your first access code!</td>
                </tr>
            <?php else: ?>
                <?php foreach ($accessCodes as $code): ?>
                    <tr>
                        <td><code><?= htmlspecialchars($code['code']) ?></code></td>
                        <td>
                            <?php if (!empty($code['course_title'])): ?>
                                <a href="admin.php?page=courses&course_id=<?= $code['course_id'] ?>">
                                    <?= htmlspecialchars($code['course_title']) ?>
                                </a>
                            <?php else: ?>
                                Unknown Course
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($code['claimed']): ?>
                                <span class="status-badge active">
                                    Claimed
                                </span>
                            <?php else: ?>
                                <span class="status-badge pending">
                                    Not Claimed
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <button class="btn btn-small" onclick="editAccessCode('<?= $code['id'] ?>')">Edit</button>
                            <button class="btn btn-small btn-danger" onclick="deleteAccessCode('<?= $code['id'] ?>')">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Create Access Code Modal -->
<div id="createAccessCodeModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Create Access Code</h3>
            <span class="close" onclick="document.getElementById('createAccessCodeModal').style.display='none'">&times;</span>
        </div>
        <form method="post" action="admin.php?page=access-codes">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
            <input type="hidden" name="action" value="create_access_code">
            
            <div class="form-group">
                <label for="access-code">Access Code</label>
                <input type="text" id="access-code" name="code" placeholder="Enter access code" required>
                <small>Will be automatically converted to uppercase</small>
            </div>
            
            <div class="form-group">
                <label for="access-course">Course</label>
                <select id="access-course" name="course_id" required>
                    <option value="">Select a course</option>
                    <?php foreach ($allCourses as $course): ?>
                        <option value="<?= htmlspecialchars($course->uuid) ?>">
                            <?= htmlspecialchars($course->title) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('createAccessCodeModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Code</button>
            </div>
        </form>
    </div>
</div>
