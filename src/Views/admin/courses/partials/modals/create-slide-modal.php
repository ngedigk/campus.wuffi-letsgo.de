<?php
/** @var array $viewModel */
?>
<div id="createSlideModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Neue Folie erstellen</h3>
            <span class="close" onclick="document.getElementById('createSlideModal').style.display='none'">&times;</span>
        </div>
        <form method="post" id="createSlideForm" action="" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">
            <input type="hidden" name="action" value="create_slide">
            <input type="hidden" id="slide-course-id" name="course_id" value="">
            <input type="hidden" id="slide-module-id" name="module_id" value="">
            
            <div class="form-group">
                <label for="new-slide-title">Folie Titel *</label>
                <input type="text" id="new-slide-title" name="title" placeholder="Folien Titel eingeben" required>
            </div>
            
            <div class="form-group audio-select-wrapper">
                <label for="new-slide-audio-url">Audio Url
                    <select
                        id="new-slide-audio-url"
                        name="audio_url"
                        <?= empty($viewModel['audioFiles']) ? 'disabled' : '' ?>
                    >
                        <option value="">-- Kein Audio --</option>
                        <?php foreach ($viewModel['audioFiles'] as $file): ?>
                            <option value="<?= htmlspecialchars($file) ?>">
                                <?= htmlspecialchars($file) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <div id="create-dropdown-audio-preview-container" style="display: none;">
                    <label>Vorschau:
                        <audio id="create-dropdown-audio-preview" controls style="width: 100%;">
                            Ihr Browser unterstützt kein Audio-Element.
                        </audio>
                    </label>
                </div>
                <?php if (empty($viewModel['audioFiles'])): ?>
                    <small>Keine Audio-Dateien verfügbar. Bitte laden Sie eine Datei hoch.</small>
                <?php endif; ?>
            </div>

            <div class="form-group audio-upload-wrapper">
                <label for="new-slide-audio-file">Audio Datei hochladen
                    <input type="file" id="new-slide-audio-file" name="audio_file" accept="audio/*">
                    <small>Optional. Nur neue Dateien hochladen.</small>
                </label>
                <div id="create-audio-preview-container" style="display: none;">
                    <label>Vorschau:
                        <audio id="create-audio-preview" controls style="width: 100%;">
                            Ihr Browser unterstützt kein Audio-Element.
                        </audio>
                    </label>
                </div>
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

