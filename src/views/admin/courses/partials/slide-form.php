<div class="slide-details-section">
    <form
        id="slide-form"
        method="post"
        action="admin.php?page=courses&course_id=<?= urlencode($selectedCourse->uuid) ?>&module_id=<?= urlencode($selectedModule->id) ?>&slide_id=<?= urlencode($selectedSlide->id) ?>"
    >
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <input type="hidden" name="action" value="update_slide">
        <input type="hidden" name="slide_id" value="<?= htmlspecialchars($selectedSlide->id) ?>">
        <input type="hidden" name="module_id" value="<?= htmlspecialchars($selectedModule->id) ?>">

        <div class="section-header">
            <h3>Slide Details</h3>

            <div class="panel-actions">
                <button
                    type="button"
                    class="btn btn-danger btn-small"
                    onclick="deleteSlide(<?= $selectedSlide->id ?>)"
                >
                    Delete Slide
                </button>

                <button
                    type="submit"
                    class="btn btn-primary btn-small"
                    onclick="prepareSlideContent()"
                >
                    Save Slide
                </button>
            </div>
        </div>

        <div class="slide-form">
            <div class="form-group">
                <label for="slide-title">Title *</label>
                <input
                    type="text"
                    id="slide-title"
                    name="title"
                    value="<?= htmlspecialchars($selectedSlide->title) ?>"
                >
            </div>

            <div class="form-group">
                <label for="slide-content">Content</label>

                <div id="blocks"></div>

                <div id="gjs">
                    <?= $selectedSlide->htmlContent ?>
                </div>

                <textarea
                    id="slide-content"
                    name="html_content"
                    style="display:none"
                ><?= htmlspecialchars($selectedSlide->htmlContent) ?></textarea>
            </div>

            <div class="form-group">
                <label for="slide-audio-url">Audio Url</label>
                <input
                    type="text"
                    id="slide-audio-url"
                    name="audio_url"
                    value="<?= htmlspecialchars($selectedSlide->audioUrl) ?>"
                >
            </div>

            <div class="form-group">
                <label for="slide-sort">Sort Order</label>
                <input
                    type="number"
                    id="slide-sort"
                    name="sort_order"
                    value="<?= htmlspecialchars($selectedSlide->sortOrder) ?>"
                    min="0"
                >
            </div>

            <div class="form-group">
                <label>
                    <input
                        type="checkbox"
                        id="slide-is-quiz"
                        name="is_quiz"
                        value="1"
                        <?= $selectedSlide->isQuiz ? 'checked' : '' ?>
                    >
                    Quiz Slide
                </label>
            </div>
        </div>
    </form>
</div>