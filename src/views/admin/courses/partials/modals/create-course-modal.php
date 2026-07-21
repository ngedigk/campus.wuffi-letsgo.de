<div id="createCourseModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Create New Course</h3>
            <span class="close" onclick="document.getElementById('createCourseModal').style.display='none'">&times;</span>
        </div>
        <form method="post" action="admin.php?page=courses">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
            <input type="hidden" name="action" value="create_course">
            
            <div class="form-group">
                <label for="new-course-title">Course Title *</label>
                <input type="text" id="new-course-title" name="title" placeholder="Enter course title" required>
            </div>
            
            <div class="form-group">
                <label for="new-course-description">Description</label>
                <textarea id="new-course-description" name="description" placeholder="Course description" rows="4"></textarea>
            </div>
            
            <div class="form-group">
                <label for="new-course-prerequisite">Prerequisite Course</label>
                <select id="new-course-prerequisite" name="prerequisite_course_id">
                    <option value="">No prerequisite</option>
                    <?php foreach ($allCourses as $course): ?>
                        <option value="<?= htmlspecialchars($course->uuid) ?>">
                            <?= htmlspecialchars($course->title) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('createCourseModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Course</button>
            </div>
        </form>
    </div>
</div>