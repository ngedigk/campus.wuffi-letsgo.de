<?php
/** @var array $viewModel */
?>
<div class="slide-details-section">
    <form
        id="slide-form"
        method="post"
        action="admin.php?page=courses&course_id=<?= urlencode($viewModel['selectedCourse']->uuid ?? '') ?>&module_id=<?= urlencode($viewModel['selectedModule']->id ?? '') ?>&slide_id=<?= urlencode($viewModel['selectedSlide']->id ?? '') ?>"
        enctype="multipart/form-data"
    >
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">
        <input type="hidden" name="action" value="update_slide">
        <input type="hidden" name="slide_id" value="<?= htmlspecialchars($viewModel['selectedSlide']->id ?? '') ?>">
        <input type="hidden" name="module_id" value="<?= htmlspecialchars($viewModel['selectedModule']->id ?? '') ?>">

        <div class="section-header">
            <h3>Folien Details</h3>

            <div class="panel-actions">
                <button
                    type="button"
                    class="btn btn-danger btn-small"
                    data-action="delete-slide"
                    data-slide-id="<?= $viewModel['selectedSlide']->id ?? '' ?>"
                >
                    Folie löschen
                </button>

                <button
                    type="submit"
                    id="save-slide"
                    class="btn btn-primary btn-small"
                >
                    Folie speichern
                </button>
            </div>
        </div>

        <div class="slide-form">
            <div class="form-group">
                <label for="slide-title">Titel *</label>
                <input
                    type="text"
                    id="slide-title"
                    name="title"
                    value="<?= htmlspecialchars($viewModel['selectedSlide']->title ?? '') ?>"
                >
            </div>

            <div class="form-group">
                <label for="slide-content">Inhalt</label>

                <div id="blocks"></div>

                <div id="gjs">
                    <?= $viewModel['selectedSlide']->htmlContent ?? '' ?>
                </div>

                <textarea
                    id="slide-content"
                    name="html_content"
                    style="display:none"
                ><?= htmlspecialchars($viewModel['selectedSlide']->htmlContent ?? '') ?></textarea>
            </div>

            <div class="form-group audio-select-wrapper">
                <label for="slide-audio-url">Audio Url
                    <select
                        id="slide-audio-url"
                        name="audio_url"
                        style="width: 100%"
                        <?= empty($viewModel['audioFiles']) ? 'disabled' : '' ?>
                    >
                        <option value="">-- Kein Audio --</option>
                        <?php foreach ($viewModel['audioFiles'] as $file): ?>
                            <option value="<?= htmlspecialchars($file) ?>" <?= ($viewModel['selectedSlide']->audioUrl ?? '') === $file ? 'selected' : '' ?>>
                                <?= htmlspecialchars($file) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <div id="slide-dropdown-audio-preview-container" style="<?php if (empty($viewModel['selectedSlide']->audioUrl)): ?> display: none;<?php endif; ?>">
                    <label>Vorschau:
                        <audio
                            id="slide-dropdown-audio-preview"
                            controls
                            style="width: 100%;"
                            src="<?= "/assets/audio/" . ($viewModel['selectedSlide']->audioUrl ?? '') ?>"
                        >
                            Ihr Browser unterstützt kein Audio-Element.
                        </audio>
                    </label>
                </div>
                <?php if (empty($viewModel['audioFiles'])): ?>
                    <small>Keine Audio-Dateien verfügbar. Bitte laden Sie eine Datei hoch.</small>
                <?php endif; ?>
            </div>

            <div class="form-group audio-upload-wrapper">
                <label for="slide-audio-file">Audio Datei hochladen
                    <input
                        type="file"
                        id="slide-audio-file"
                        name="audio_file"
                        accept="audio/*"
                    >
                    <small>Optional. Nur neue Dateien hochladen. Bestehende Dateien werden nicht überschrieben.</small>
                </label>
                <div id="audio-preview-container" style="margin-top: 10px; display: none;">
                    <label>Vorschau:</label>
                    <audio id="audio-preview" controls style="width: 100%;">
                        Ihr Browser unterstützt kein Audio-Element.
                    </audio>
                </div>
            </div>

            <div class="form-group">
                <label for="slide-sort">Reihenfolge</label>
                <input
                    type="number"
                    id="slide-sort"
                    name="sort_order"
                    value="<?= htmlspecialchars($viewModel['selectedSlide']->sortOrder ?? 0) ?>"
                    min="0"
                >
            </div>
        </div>
    </form>
</div>