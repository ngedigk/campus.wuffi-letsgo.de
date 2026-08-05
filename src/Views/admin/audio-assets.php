<?php
/** @var array $viewModel */
?>

<form
    id="delete-audio-asset-form"
    method="post"
>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">
</form>

<div class="list-grid audio-assets-list">
    <div class="list-item header-row">
        <div class="cell filename-cell">Dateiname</div>
        <div class="cell preview-cell">Preview</div>
        <div class="cell actions-cell">Aktionen</div>
    </div>
    <?php if (empty($viewModel['audioFiles'] ?? [])): ?>
        <div class="empty-state" style="grid-column: 1 / -1;">Keine Access Codes gefunden. Erstellen Sie Ihren ersten Access Code!</div>
    <?php else: ?>
        <?php foreach ($viewModel['audioFiles'] ?? [] as $audioFile): ?>
            <div class="list-item">
                <div class="cell filename-cell">
                    <?= htmlspecialchars($audioFile) ?>
                </div>
                <div class="cell preview-cell">
                    <audio
                        class="audio-preview"
                        controls
                        style="width: 100%;"
                        src="<?= "/assets/audio/" . $audioFile ?>"
                    >
                        Ihr Browser unterstützt kein Audio-Element.
                    </audio>
                </div>
                <div class="cell actions-cell">
                    <div class="cell actions-cell">
                        <button
                            class="btn btn-small btn-danger"
                            data-action="delete-audio-asset"
                            data-filename="<?= htmlspecialchars($audioFile) ?>"
                        >Löschen</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>