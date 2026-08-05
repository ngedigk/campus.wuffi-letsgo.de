<?php
/** @var array $viewModel */
?>
<div id="createModuleModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Neues Modul erstellen</h3>
            <span class="close" onclick="document.getElementById('createModuleModal').style.display='none'">&times;</span>
        </div>
        
        <?php
        $createModuleAction = sprintf(
            '/admin/courses/%s/modules',
            htmlspecialchars($viewModel['selectedCourse']->uuid ?? '')
        );
        ?>
        <form
            id="createModuleForm"
            method="post"
            action="<?= $createModuleAction ?>"
        >
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">
            
            <div class="form-group">
                <label for="new-module-title">Modul Titel *</label>
                <input type="text" id="new-module-title" name="title" placeholder="Enter module title" required>
            </div>

            <div class="form-group">
                <label for="new-module-title">Reihenfolge</label>
                <input type="text" id="new-module-sort" name="sort_order" value="0">
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('createModuleModal').style.display='none'">Abbrechen</button>
                <button type="submit" class="btn btn-primary">Modul erstellen</button>
            </div>
        </form>
    </div>
</div>