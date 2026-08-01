<?php
/** @var array $viewModel */
?>
<div class="question-details-section">
    <form
        id="question-form"
        method="post"
        action="admin.php?page=courses&course_id=<?= urlencode($viewModel['selectedCourse']->uuid ?? '') ?>&module_id=<?= urlencode($viewModel['selectedModule']->id ?? '') ?>&slide_id=<?= urlencode($viewModel['selectedSlide']->id ?? '') ?>&question_id=<?= urlencode($viewModel['selectedQuestion']->id ?? '') ?>"
    >
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">
        <input type="hidden" name="action" value="update_question">
        <input type="hidden" name="question_id" value="<?= htmlspecialchars($viewModel['selectedQuestion']->id ?? '') ?>">
        <input type="hidden" name="slide_id" value="<?= htmlspecialchars($viewModel['selectedSlide']->id ?? '') ?>">

        <div class="section-header">
            <h3>Question Details</h3>

            <div class="panel-actions">
                <button
                    type="button"
                    class="btn btn-danger btn-small"
                    onclick="deleteQuestion(<?= $viewModel['selectedQuestion']->id ?? '' ?>)"
                >
                    Delete Question
                </button>

                <button type="submit" class="btn btn-primary btn-small">
                    Save Question
                </button>
            </div>
        </div>

        <div class="question-form">
            <div class="form-group">
                <label for="question-text">Question *</label>
                <input
                    type="text"
                    id="question-text"
                    name="question_text"
                    value="<?= htmlspecialchars($viewModel['selectedQuestion']->questionText ?? '') ?>"
                >
            </div>
        </div>
    </form>
</div>