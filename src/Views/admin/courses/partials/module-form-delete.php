<form
    id="delete-module-form"
    method="post"
    action="admin.php?page=courses&course_id=<?= urlencode($viewModel['selectedCourse']->uuid ?? '') ?>"
>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">
    <input type="hidden" name="action" value="delete_module">
    <input type="hidden" id="delete-module-id" name="module_id" value="">
</form>