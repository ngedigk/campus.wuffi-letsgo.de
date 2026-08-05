<?php
/** @var array $viewModel */
?>
<div id="createQuestionModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Neue Frage erstellen</h3>
            <span class="close" onclick="document.getElementById('createQuestionModal').style.display='none'">&times;</span>
        </div>
        
        <?php
        $createQuestionAction = sprintf(
            '/admin/courses/%s/modules/%s/slides/%s/questions',
            htmlspecialchars($viewModel['selectedCourse']->uuid ?? ''),
            htmlspecialchars($viewModel['selectedModule']->id ?? ''),
            htmlspecialchars($viewModel['selectedSlide']->id ?? ''),
        );
        ?>
        <form
            id="createQuestionForm"
            method="post"
            action="<?= $createQuestionAction ?>"
        >
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">
            
            <div class="form-group">
                <label for="new-question-text">Frage *</label>
                <textarea id="new-question-text" name="question_text" placeholder="Enter question text" required style="width: 100%; min-height: 80px;"></textarea>
            </div>

            <div class="form-group">
                <label>Antworten *</label>
                <div id="choices-container">
                    <div class="choice-row">
                        <input type="text" name="choices[0][text]" placeholder="Antwort Text" required style="flex: 1; margin-right: 10px;">
                        <label><input type="checkbox" name="choices[0][is_correct]" value="1"> Korrekt</label>
                        <button
                            type="button"
                            class="btn btn-sm remove-choice"
                            data-action="remove-choice"
                        >
                            🗑
                        </button>
                    </div>
                </div>
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-action="add-choice"
                    style="margin-top: 10px;"
                >+ Antwort hinzufügen</button>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('createQuestionModal').style.display='none'">Abbrechen</button>
                <button type="submit" class="btn btn-primary">Frage erstellen</button>
            </div>
        </form>
    </div>
</div>