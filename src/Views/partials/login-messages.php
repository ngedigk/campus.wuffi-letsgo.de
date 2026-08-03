<?php /** @var array $viewModel */ ?>

<?php if ($viewModel['loginSuccess'] ?? ''): ?>
    <p class="alert alert-success"><?= htmlspecialchars($viewModel['loginSuccess']) ?></p>
<?php endif; ?>

<?php if ($viewModel['loginError'] ?? ''): ?>
    <p class="alert alert-error"><?= htmlspecialchars($viewModel['loginError']) ?></p>
<?php endif; ?>