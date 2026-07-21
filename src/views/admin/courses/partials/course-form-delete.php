<form
    id="delete-course-form"
    method="post"
    action="admin.php?page=courses"
>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
    <input type="hidden" name="action" value="delete_course">
    <input type="hidden" id="delete-course-id" name="course_id" value="<?= htmlspecialchars($selectedCourse->uuid) ?>">
</form>