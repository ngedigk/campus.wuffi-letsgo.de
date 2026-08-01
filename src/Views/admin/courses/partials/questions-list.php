<?php
/** @var array $viewModel */
?>
<div class="quiz-questions-section" style="margin-top: 30px;">
    <div class="section-header">
        <h3>Quiz Questions</h3>
        <button type="button" class="btn btn-primary btn-small" onclick="addQuestion(<?= $viewModel['selectedSlideId'] ?? 0 ?>)">+ Add Question</button>
    </div>

    <?php if (empty($viewModel['quizQuestions'])): ?>
        <p class="empty-state">No questions yet. Click "Add Question" to create one.</p>
    <?php else: ?>
        <div class="questions-list">
            <?php foreach ($viewModel['quizQuestions'] as $index => $question): ?>
                <div class="question-item" data-question-id="<?= htmlspecialchars($question->id) ?>">
                    <span class="question-number"><?= $index + 1 ?>.</span>
                    <span class="question-title"><?= htmlspecialchars($question->questionText) ?></span>
                    <div class="question-actions">
                        <a
                            href="admin.php?page=courses&course_id=<?= urlencode($viewModel['selectedCourse']->uuid ?? '') ?>&module_id=<?= urlencode($viewModel['selectedModule']->id) ?>&slide_id=<?= urlencode($viewModel['selectedSlideId'] ?? 0) ?>&question_id=<?= urlencode($question->id) ?>"
                            class="btn btn-small"
                            title="Edit Question"
                        >
                            ✏️
                        </a>
                        <button onclick="deleteQuestion(<?= $question->id ?>)">🗑</button>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
    <?php endif; ?>
</div>