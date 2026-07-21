<!-- Create Course Modal -->
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

<!-- Create Module Modal -->
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

<!-- Create Slide Modal -->
<div id="createSlideModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Create New Slide</h3>
            <span class="close" onclick="document.getElementById('createSlideModal').style.display='none'">&times;</span>
        </div>
        <form method="post" id="createSlideForm" action="">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
            <input type="hidden" name="action" value="create_slide">
            <input type="hidden" id="slide-course-id" name="course_id" value="">
            <input type="hidden" id="slide-module-id" name="module_id" value="">
            
            <div class="form-group">
                <label for="new-slide-title">Slide Title *</label>
                <input type="text" id="new-slide-title" name="title" placeholder="Enter slide title" required>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('createSlideModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Slide</button>
            </div>
        </form>
    </div>
</div>

<script>
const existingAssets = <?= json_encode($slideAssets) ?>;
</script>
