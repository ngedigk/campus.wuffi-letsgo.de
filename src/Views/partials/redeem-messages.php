<?php /** @var array $viewModel */ ?>

<?php if ($viewModel['redeemSuccess'] ?? ''): ?>
    <p class="alert alert-success"><?= htmlspecialchars($viewModel['redeemSuccess']) ?></p>
<?php endif; ?>

<?php if ($viewModel['redeemError'] ?? ''): ?>
    <p class="alert alert-error"><?= htmlspecialchars($viewModel['redeemError']) ?></p>
<?php endif; ?>