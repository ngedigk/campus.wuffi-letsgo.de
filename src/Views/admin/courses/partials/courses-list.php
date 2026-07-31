
<div class="courses-list" id="coursesList">
    <?php foreach ($allCourses as $course): ?>
        <a
            href="admin.php?page=courses&course_id=<?= $course->uuid ?>"
            class="course-item <?= $course->uuid === $selectedCourseId ? 'active' : '' ?>"
            title="Edit Course"
        >
            <div class="course-info">
                <h4><?= htmlspecialchars($course->title) ?></h4>
            </div>
        </a>
    <?php endforeach; ?>
</div>
