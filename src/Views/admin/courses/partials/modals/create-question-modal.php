<?php
/** @var array $viewModel */
?>
<div id="createQuestionModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Neue Frage erstellen</h3>
            <span class="close" onclick="document.getElementById('createQuestionModal').style.display='none'">&times;</span>
        </div>
        <form method="post" id="createQuestionForm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">
            <input type="hidden" name="action" value="create_question">
            <input type="hidden" id="question-slide-id" name="slide_id" value="">
            
            <div class="form-group">
                <label for="new-question-text">Frage *</label>
                <textarea id="new-question-text" name="question_text" placeholder="Enter question text" required style="width: 100%; min-height: 80px;"></textarea>
            </div>

            <div class="form-group">
                <label>Antworten *</label>
                <div id="choices-container">
                    <div class="choice-row">
                        <input type="text" name="choices[0][text]" placeholder="Antwort Text" required style="flex: 1; margin-right: 10px;">
                        <label><input type="checkbox" name="choices[0][is_correct]" unchecked> Korrekt</label>
                        <button type="button" class="btn btn-danger btn-sm remove-choice" onclick="removeChoice(this)" style="display:none;">&times;</button>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary" onclick="addChoice()" style="margin-top: 10px;">+ Antwort hinzufügen</button>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('createQuestionModal').style.display='none'">Abbrechen</button>
                <button type="submit" class="btn btn-primary">Frage erstellen</button>
            </div>
        </form>
    </div>
</div>