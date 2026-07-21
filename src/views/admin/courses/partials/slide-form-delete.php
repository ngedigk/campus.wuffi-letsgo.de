<form
    id="delete-slide-form"
    method="post"
    action="admin.php?page=courses&course_id=<?= urlencode($selectedCourse->uuid) ?>&module_id=<?= urlencode($selectedModule->id) ?>"
>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
    <input type="hidden" name="action" value="delete_slide">
    <input type="hidden" id="delete-slide-id" name="slide_id" value="">
</form>