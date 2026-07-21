<div class="admin-courses-layout">
    <!-- Course Details -->
    <div class="course-details-panel">
        <?php if ($selectedCourse): ?>
            <?php require __DIR__ . '/partials/course-form-delete.php'; ?>
            <?php require __DIR__ . '/partials/course-form.php'; ?>

            <!-- Modules Section -->
            <?php require __DIR__ . '/partials/modules-list.php'; ?>
            <?php require __DIR__ . '/partials/module-form-delete.php'; ?>

            <!-- Create Module Modals -->
            <?php require __DIR__ . '/partials/modals/create-module-modal.php'; ?>
            <?php require __DIR__ . '/partials/modals/create-slide-modal.php'; ?>

            <?php if ($selectedModule): ?>
                <!-- Module Details Section -->
                <?php require __DIR__ . '/partials/module-form.php'; ?>

                <!-- Slides Section -->
                <?php require __DIR__ . '/partials/slides-list.php'; ?>
                <?php require __DIR__ . '/partials/slide-form-delete.php'; ?>

                <!-- Slide Details Section -->
                <?php if ($selectedSlide): ?>
                    <?php require __DIR__ . '/partials/slide-form.php'; ?>
                <?php endif; ?>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state">
                <p>No course selected. Create a new course to get started.</p>
                <button class="btn btn-primary" onclick="document.getElementById('createCourseModal').style.display='flex'">
                    + Add Course
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>