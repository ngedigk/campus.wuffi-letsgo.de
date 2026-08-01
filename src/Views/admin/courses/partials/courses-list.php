<?php
/** @var array $viewModel */
?>
<div class="courses-list" id="coursesList">
    <?php foreach ($viewModel['allCourses'] ?? [] as $course): ?>
        <a
            href="admin.php?page=courses&course_id=<?= $course->uuid ?>"
            class="course-item <?= $course->uuid === ($viewModel['selectedCourseId'] ?? '' ?? '') ? 'active' : '' ?>"
            title="Kurs bearbeiten"
        >
            <div class="course-info">
                <h4><?= htmlspecialchars($course->title) ?></h4>
            </div>
        </a>
    <?php endforeach; ?>
</div>

