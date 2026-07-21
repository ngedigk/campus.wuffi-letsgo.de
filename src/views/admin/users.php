<?php if ($isAdmin): ?>

<div class="data-table">
    <table>
        <thead>
            <tr>
                <th>Email</th>
                <th>Role</th>
                <th>Created</th>
                <th>Last Login</th>
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
                        <td><?= htmlspecialchars($userItem['last_login'] ?? 'Never') ?></td>
                        <td>
                            <?php if ($userItem['email_verified']): ?>
                                <span class="status-badge active">Verified</span>
                            <?php else: ?>
                                <span class="status-badge pending">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <?php if (!$userItem['is_admin']): ?>
                                <button class="btn btn-small" onclick="grantAdmin('<?= $userItem['id'] ?>')">Grant Admin</button>
                            <?php else: ?>
                                <button class="btn btn-small btn-danger" onclick="revokeAdmin('<?= $userItem['id'] ?>')">Revoke Admin</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function grantAdmin(userId) {
    if (confirm('Are you sure you want to grant admin access to this user?')) {
        // Implementation would go here
        console.log('Grant admin to:', userId);
    }
}

function revokeAdmin(userId) {
    if (confirm('Are you sure you want to revoke admin access from this user?')) {
        // Implementation would go here
        console.log('Revoke admin from:', userId);
    }
}
</script>

<?php endif; ?>
