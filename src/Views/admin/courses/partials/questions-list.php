<?php
/** @var array $viewModel */
?>
<div class="quiz-questions-section" style="margin-top: 30px;">
    <div class="section-header">
        <h3>Quiz Fragen</h3>
        <button
            type="button"
            class="btn btn-primary btn-small"
            data-action="add-question"
            data-slide-id="<?= $viewModel['selectedSlideId'] ?? 0 ?>"
        >+ Frage hinzufügen</button>
    </div>

    <?php if (empty($viewModel['quizQuestions'])): ?>
        <p class="empty-state">Keine Fragen. Klicken Sie auf "Frage hinzufügen", um eine zu erstellen.</p>
    <?php else: ?>
        <div class="questions-list">
            <?php foreach ($viewModel['quizQuestions'] as $index => $question): ?>
                <div class="question-item" data-question-id="<?= htmlspecialchars($question->id) ?>">
                    <span class="question-number"><?= $index + 1 ?>.</span>
                    <span class="question-title"><?= htmlspecialchars($question->questionText) ?></span>
                    <div class="question-actions">
                        <button
                            data-action="edit-question"
                            data-question-id="<?= $question->id ?>"
                            data-question-text="<?= htmlspecialchars($question->questionText) ?>"
                            data-choices='<?= json_encode($viewModel["quizChoicesByQuestion"][$question->id] ?? []) ?>'
                        >✏️</button>
                        <button
                            data-action="delete-question"
                            data-question-id="<?= $question->id ?>"
                        >🗑</button>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
    <?php endif; ?>
</div>