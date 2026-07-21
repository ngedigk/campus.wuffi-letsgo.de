<form
    id="course-form"
    method="post"
    action="admin.php?page=courses&course_id=<?= urlencode($selectedCourse->uuid) ?>"
>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
    <input type="hidden" name="action" value="update_course">
    <input type="hidden" name="course_id" value="<?= htmlspecialchars($selectedCourse->uuid) ?>">

    <div class="panel-header">
        <h3>Course Details</h3>
        <div class="panel-actions">
            <button
                type="button"
                class="btn btn-danger btn-small"
                onclick="deleteCourse('<?= $selectedCourse->uuid ?>')"
            >
                Delete Course
            </button>
            <button type="submit" class="btn btn-primary btn-small">
                Save Course
            </button>
        </div>
    </div>
        
    <div class="course-form">
        <div class="form-row">
            <div class="form-group">
                <label for="course-title">Title *</label>
                <input type="text" id="course-title" name="title" value="<?= htmlspecialchars($selectedCourse->title) ?>">
            </div>
            <div class="form-group">
                <label for="prerequisite">Prerequisite Course</label>
                <select id="prerequisite" name="prerequisite_course_id">
                    <option value="">Select a prerequisite course (optional)</option>
                    <?php foreach ($allCourses as $course): ?>
                        <?php if ($course->uuid !== $selectedCourse->uuid): ?>
                            <option value="<?= htmlspecialchars($course->uuid) ?>"
                                <?= ($selectedCourse->prerequisiteCourseId ?? '') === $course->uuid ? 'selected' : '' ?>>
                                <?= htmlspecialchars($course->title) ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="course-description">Description</label>
            <textarea id="course-description" name="description" rows="3"><?= htmlspecialchars($selectedCourse->description ?? '') ?></textarea>
        </div>
    </div>
</form>
