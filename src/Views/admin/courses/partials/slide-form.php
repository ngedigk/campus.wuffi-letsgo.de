<?php
/** @var array $viewModel */
?>
<div class="slide-details-section">
    <form
        id="slide-form"
        method="post"
        action="admin.php?page=courses&course_id=<?= urlencode($viewModel['selectedCourse']->uuid ?? '') ?>&module_id=<?= urlencode($viewModel['selectedModule']->id ?? '') ?>&slide_id=<?= urlencode($viewModel['selectedSlide']->id ?? '') ?>"
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

            <div class="form-group">
                <label for="slide-audio-url">Audio Url</label>
                <input
                    type="text"
                    id="slide-audio-url"
                    name="audio_url"
                    value="<?= htmlspecialchars($viewModel['selectedSlide']->audioUrl ?? '') ?>"
                >
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