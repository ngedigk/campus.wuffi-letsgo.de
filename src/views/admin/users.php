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
            <?php if (empty($allUsers)): ?>
                <tr>
                    <td colspan="6" class="empty-state">No users found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($allUsers as $userItem): ?>
                    <tr>
                        <td><?= htmlspecialchars($userItem['email']) ?></td>
                        <td>
                            <?php if ($userItem['is_admin']): ?>
                                <span class="status-badge admin">Admin</span>
                            <?php else: ?>
                                <span class="status-badge user">User</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($userItem['created_at'] ?? 'N/A') ?></td>
                        <td>
                            <?php if ($userItem['email_verified']): ?>
                                <span class="status-badge active">Verified</span>
                            <?php else: ?>
                                <span class="status-badge pending">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <?php if (!$userItem['email_verified']): ?>
                                <form
                                    id="manually-verify-form"
                                    method="post"
                                    action="admin.php?page=users"
                                >
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                                    <input type="hidden" name="action" value="manually_verify">
                                    <input type="hidden" name="email" value="<?= htmlspecialchars($userItem['email']) ?>">
                                    <button class="btn btn-small btn-warn" type="submit">Manually Verify</button>
                                </form>
                            <?php else: ?>
                                <?php if (!$userItem['is_admin']): ?>
                                    <form
                                        id="grant-admin-form"
                                        method="post"
                                        action="admin.php?page=users"
                                    >
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                                        <input type="hidden" name="action" value="grant_admin">
                                        <input type="hidden" name="email" value="<?= htmlspecialchars($userItem['email']) ?>">
                                        <button class="btn btn-small" type="submit">Grant Admin</button>
                                    </form>
                                <?php else: ?>
                                    <form
                                        id="revoke-admin-form"
                                        method="post"
                                        action="admin.php?page=users"
                                    >
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                                        <input type="hidden" name="action" value="revoke_admin">
                                        <input type="hidden" name="email" value="<?= htmlspecialchars($userItem['email']) ?>">
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