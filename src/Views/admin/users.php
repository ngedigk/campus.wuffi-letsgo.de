<?php
/** @var array $viewModel */
?>
<div class="data-table">
    <table>
        <thead>
            <tr>
                <th>E-Mail</th>
                <th>Rolle</th>
                <th>Erstellt</th>
                <th>Status</th>
                <th>Aktionen</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($viewModel['allUsers'] ?? [])): ?>
                <tr>
                    <td colspan="6" class="empty-state">Keine Benutzer gefunden.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($viewModel['allUsers'] ?? [] as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user->email) ?></td>
                        <td>
                            <?php if ($user->isAdmin): ?>
                                <span class="status-badge admin">Admin</span>
                            <?php else: ?>
                                <span class="status-badge user">Benutzer</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($user->createdAt ?? 'N/A') ?></td>
                        <td>
                            <?php if ($user->emailVerified): ?>
                                <span class="status-badge active">Bestätigt</span>
                            <?php else: ?>
                                <span class="status-badge pending">Ausstehend</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions vertical-actions">
                            <?php if (!$user->emailVerified): ?>
                                <form
                                    id="resend-verify-form"
                                    method="post"
                                    action="admin.php?page=users"
                                    onsubmit="return confirm('Sind Sie sicher, dass Sie die E-Mail nochmal an den Nutzer senden möchten?')"
                                >
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">
                                    <input type="hidden" name="action" value="resend_verification_email">
                                    <input type="hidden" name="email" value="<?= htmlspecialchars($user->email) ?>">
                                    <button class="btn btn-small btn-warn" type="submit">Verifizierungsmail nochmal senden</button>
                                </form>
                                <form
                                    id="manually-verify-form"
                                    method="post"
                                    action="admin.php?page=users"
                                >
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($viewModel['csrfToken'] ?? '') ?>">
                                    <input type="hidden" name="action" value="manually_verify">
                                    <input type="hidden" name="email" value="<?= htmlspecialchars($user->email) ?>">
                                    <button class="btn btn-small btn-warn" type="submit">Manuell bestätigen</button>
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
                                        <button class="btn btn-small" type="submit">Admin-Rechte verleihen</button>
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
                                        <button class="btn btn-small btn-danger" type="submit">Admin-Rechte entziehen</button>
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