<div id="createModuleModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Create New Module</h3>
            <span class="close" onclick="document.getElementById('createModuleModal').style.display='none'">&times;</span>
        </div>
        <form method="post" id="createModuleForm" action="">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
            <input type="hidden" name="action" value="create_module">
            <input type="hidden" id="module-course-id" name="course_id" value="">
            
            <div class="form-group">
                <label for="new-module-title">Module Title *</label>
                <input type="text" id="new-module-title" name="title" placeholder="Enter module title" required>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('createModuleModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Module</button>
            </div>
        </form>
    </div>
</div>