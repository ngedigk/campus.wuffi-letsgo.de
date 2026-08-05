<?php
/** @var array $viewModel */
?>
<div class="course-details-panel">
    <?php require __DIR__ . '/partials/delete-form.php'; ?>

    <?php if ($viewModel['selectedCourse'] ?? null): ?>
        <?php require __DIR__ . '/partials/modals/create-module-modal.php'; ?>
        <?php require __DIR__ . '/partials/course-form.php'; ?>
        <?php require __DIR__ . '/partials/modules-list.php'; ?>
    <?php else: ?>
        <div class="empty-state">
            <p>Kein Kurs ausgewählt. Erstellen Sie einen neuen Kurs, um zu beginnen.</p>
            <button class="btn btn-primary" onclick="document.getElementById('createCourseModal').style.display='flex'">
                + Kurs hinzufügen
            </button>
        </div>
    <?php endif; ?>
</div>