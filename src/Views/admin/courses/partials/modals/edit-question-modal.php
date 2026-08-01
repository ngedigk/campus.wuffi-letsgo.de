<div id="editQuestionModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Frage bearbeiten</h3>
            <span class="close" onclick="document.getElementById('editQuestionModal').style.display='none'">&times;</span>
        </div>
        <form method="post" id="editQuestionForm" onsubmit="return validateQuestionForm(this)">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">
            <input type="hidden" name="action" value="update_question">
            <input type="hidden" id="edit-question-id" name="question_id" value="">
            <input type="hidden" id="edit-question-slide-id" name="slide_id" value="">
            
            <div class="form-group">
                <label for="edit-question-text">Frage *</label>
                <textarea id="edit-question-text" name="question_text" placeholder="Enter question text" required style="width: 100%; min-height: 80px;"></textarea>
            </div>
            
            <div class="form-group">
                <label>Antworten *</label>
                <div id="edit-choices-container">
                    <!-- Choices will be injected here via JS -->
                </div>
                <button type="button" class="btn btn-secondary" onclick="addEditChoice()" style="margin-top: 10px;">+ Antwort hinzufügen</button>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('editQuestionModal').style.display='none'">Abbrechen</button>
                <button type="submit" class="btn btn-primary">Aktualisieren</button>
            </div>
        </form>
    </div>
</div>