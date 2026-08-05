<?php
/** @var array $viewModel */
?>
<form
    id="delete-slide-form"
    method="post"
>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">
</form>