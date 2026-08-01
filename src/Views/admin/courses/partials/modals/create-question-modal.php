<?php
/** @var array $viewModel */
?>
<div id="createQuestionModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Create New Question</h3>
            <span class="close" onclick="document.getElementById('createQuestionModal').style.display='none'">&times;</span>
        </div>
        <form method="post" id="createQuestionForm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">
            <input type="hidden" name="action" value="create_question">
            <input type="hidden" id="question-slide-id" name="slide_id" value="">
            
            <div class="form-group">
                <label for="new-question-text">Question Text *</label>
                <textarea id="new-question-text" name="question_text" placeholder="Enter question text" required style="width: 100%; min-height: 80px;"></textarea>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('createQuestionModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Question</button>
            </div>
        </form>
    </div>
</div>