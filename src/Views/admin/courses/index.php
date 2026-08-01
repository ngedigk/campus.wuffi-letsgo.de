<?php
/** @var array $viewModel */
?>
<div class="admin-courses-layout">
    <!-- Course Details -->
    <div class="course-details-panel">
        <?php if ($viewModel['selectedCourse'] ?? null): ?>
            <?php require __DIR__ . '/partials/course-form-delete.php'; ?>
            <?php require __DIR__ . '/partials/module-form-delete.php'; ?>
            <?php require __DIR__ . '/partials/slide-form-delete.php'; ?>
            <?php require __DIR__ . '/partials/question-form-delete.php'; ?>
            
            <?php if (!$viewModel['selectedModule']): ?>
                <!-- Create Module Modal -->
                <?php require __DIR__ . '/partials/modals/create-module-modal.php'; ?>

                <!-- Course Details Section -->
                <?php require __DIR__ . '/partials/course-form.php'; ?>

                <!-- Modules Section (navigation) -->
                <?php require __DIR__ . '/partials/modules-list.php'; ?>
            <?php endif; ?>

            <!-- Module & Slide Context -->
            <?php if ($viewModel['selectedModule'] ?? null): ?>
                <?php if (!$viewModel['selectedSlide']): ?>
                    <!-- Create Slide Modal -->
                    <?php require __DIR__ . '/partials/modals/create-slide-modal.php'; ?>

                    <!-- Module Details Section -->
                    <?php require __DIR__ . '/partials/module-form.php'; ?>

                    <!-- Slides Section (navigation) -->
                    <?php require __DIR__ . '/partials/slides-list.php'; ?>
                <?php endif; ?>

                <!-- Slide Details Section -->
                <?php if ($viewModel['selectedSlide'] ?? null): ?>
                    <?php if (!$viewModel['selectedQuestion']): ?>
                        <!-- Question Modals -->
                        <?php require __DIR__ . '/partials/modals/create-question-modal.php'; ?>
                        <?php require __DIR__ . '/partials/modals/edit-question-modal.php'; ?>

                        <!-- Slide Details Section -->
                        <?php require __DIR__ . '/partials/slide-form.php'; ?>

                        <!-- Questions Section (navigation) -->
                        <?php require __DIR__ . '/partials/questions-list.php'; ?>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state">
                <p>Kein Kurs ausgewählt. Erstellen Sie einen neuen Kurs, um zu beginnen.</p>
                <button class="btn btn-primary" onclick="document.getElementById('createCourseModal').style.display='flex'">
                    + Kurs hinzufügen
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>