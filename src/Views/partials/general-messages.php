<?php /** @var array $viewModel */ ?>

<?php if ($viewModel['success'] ?? ''): ?>
    <p class="alert alert-success"><?= htmlspecialchars($viewModel['success']) ?></p>
<?php endif; ?>

<?php if ($viewModel['message'] ?? ''): ?>
    <p class="alert alert-message"><?= htmlspecialchars($viewModel['message']) ?></p>
<?php endif; ?>

<?php if ($viewModel['error'] ?? ''): ?>
    <p class="alert alert-error"><?= htmlspecialchars($viewModel['error']) ?></p>
<?php endif; ?>