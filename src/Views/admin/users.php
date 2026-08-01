<?php
/** @var array $viewModel */
?>
<div class="data-table">
    <table>
        <thead>
            <tr>
                <th>Email</th>
                <th>Role</th>
                <th>Created</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($viewModel['allUsers'] ?? [])): ?>
                <tr>
                    <td colspan="6" class="empty-state">No users found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($viewModel['allUsers'] ?? [] as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user->email) ?></td>
                        <td>
                            <?php if ($user->isAdmin): ?>
                                <span class="status-badge admin">Admin</span>
                            <?php else: ?>
                                <span class="status-badge user">User</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($user->createdAt ?? 'N/A') ?></td>
                        <td>
                            <?php if ($user->emailVerified): ?>
                                <span class="status-badge active">Verified</span>
                            <?php else: ?>
                                <span class="status-badge pending">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <?php if (!$user->emailVerified): ?>
                                <form
                                    id="manually-verify-form"
                                    method="post"
                                    action="admin.php?page=users"
                                >
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">
                                    <input type="hidden" name="action" value="manually_verify">
                                    <input type="hidden" name="email" value="<?= htmlspecialchars($user->email) ?>">
                                    <button class="btn btn-small btn-warn" type="submit">Manually Verify</button>
                                </form>
                            <?php else: ?>
                                <?php if (!$user->isAdmin): ?>
                                    <form
                                        id="grant-admin-form"
                                        method="post"
                                        action="admin.php?page=users"
                                    >
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">
                                        <input type="hidden" name="action" value="grant_admin">
                                        <input type="hidden" name="email" value="<?= htmlspecialchars($user->email) ?>">
                                        <button class="btn btn-small" type="submit">Grant Admin</button>
                                    </form>
                                <?php else: ?>
                                    <form
                                        id="revoke-admin-form"
                                        method="post"
                                        action="admin.php?page=users"
                                    >
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">
                                        <input type="hidden" name="action" value="revoke_admin">
                                        <input type="hidden" name="email" value="<?= htmlspecialchars($user->email) ?>">
                                        <button class="btn btn-small btn-danger" type="submit">Revoke Admin</button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>