<?php
/** @var array $viewModel */
?>
<form
    id="delete-question-form"
    method="post"
    action="admin.php?page=courses&course_id=<?= urlencode($viewModel['selectedCourse']->uuid ?? '') ?>&module_id=<?= urlencode($viewModel['selectedModule']->id ?? '') ?>&slide_id=<?= urlencode($viewModel['selectedSlide']->id ?? '') ?>"
>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">
    <input type="hidden" name="action" value="delete_question">
    <input type="hidden" id="delete-question-id" name="question_id" value="">
</form>