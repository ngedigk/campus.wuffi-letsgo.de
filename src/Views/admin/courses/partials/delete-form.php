<?php
/** @var array $viewModel */
?>
<form
    id="delete-form"
    method="post"
>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">
</form>