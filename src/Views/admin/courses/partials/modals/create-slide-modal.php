<?php
/** @var array $viewModel */
?>
<div id="createSlideModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Neue Folie erstellen</h3>
            <span class="close" onclick="document.getElementById('createSlideModal').style.display='none'">&times;</span>
        </div>
        <form method="post" id="createSlideForm" action="">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">
            <input type="hidden" name="action" value="create_slide">
            <input type="hidden" id="slide-course-id" name="course_id" value="">
            <input type="hidden" id="slide-module-id" name="module_id" value="">
            
            <div class="form-group">
                <label for="new-slide-title">Folie Titel *</label>
                <input type="text" id="new-slide-title" name="title" placeholder="Enter slide title" required>
            </div>
            
            <div class="form-group">
                <label for="new-slide-title">Audio Url</label>
                <input type="text" id="new-slide-audio-url" name="audio_url" placeholder="Enter audio file name. (example: filename.mp3)">
            </div>

            <div class="form-group">
                <label for="new-module-title">Reihenfolge</label>
                <input type="text" id="new-module-sort" name="sort_order" value="0">
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('createSlideModal').style.display='none'">Abbrechen</button>
                <button type="submit" class="btn btn-primary">Folie erstellen</button>
            </div>
        </form>
    </div>
</div>

<script>
const existingAssets = <?= json_encode($viewModel['slideAssets'] ?? []) ?>;
</script>
