<?php
/** @var array $viewModel */
?>
<div class="section-header">
    <h3>Folien Details</h3>

    <div class="panel-actions">
        <button
            type="button"
            class="btn btn-danger btn-small"
            data-action="delete-slide"
            data-course-id="<?= $viewModel['selectedCourse']->uuid ?? '' ?>"
            data-module-id="<?= $viewModel['selectedModule']->id ?? '' ?>"
            data-slide-id="<?= $viewModel['selectedSlide']->id ?? '' ?>"
        >
            Folie löschen
        </button>

        <button id="save-slide" type="submit" form="slide-form" class="btn btn-primary btn-small">Folie speichern</button>
    </div>
</div>

<?php
$updateSlideAction = sprintf(
    '/admin/courses/%s/modules/%s/slides/%s/update',
    htmlspecialchars($viewModel['selectedCourse']->uuid ?? ''),
    htmlspecialchars($viewModel['selectedModule']->id ?? ''),
    htmlspecialchars($viewModel['selectedSlide']->id ?? ''),
);
?>

<form
    id="slide-form"
    method="post"
    action="<?= $updateSlideAction ?>"
    enctype="multipart/form-data"
>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">

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
                        <?php if (!empty($viewModel['selectedSlide']->audioUrl)): ?>
                        src="<?= "/assets/audio/" . ($viewModel['selectedSlide']->audioUrl ?? '') ?>"
                        <?php endif; ?>
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