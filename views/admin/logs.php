<?php require __DIR__ . '/../layouts/header.php'; ?>

<div style="margin-bottom: 1.5rem;">
    <a href="/admin" style="color: var(--text-secondary); font-size: 0.9rem;">&larr; Voltar ao Painel Admin</a>
</div>

<h1 style="font-size: 1.8rem; font-weight: 700; color: #fff; margin-bottom: 1.5rem;">
    Logs de Auditoria do Sistema
</h1>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nível</th>
                <th>Mensagem</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= $log['id'] ?></td>
                    <td>
                        <span class="badge <?= $log['level'] === 'WARN' ? 'badge-warning' : ($log['level'] === 'ERROR' ? 'badge-danger' : 'badge-info') ?>">
                            <?= htmlspecialchars($log['level']) ?>
                        </span>
                    </td>
                    <td><code style="color: #fff; font-family: monospace; font-size: 0.85rem;"><?= htmlspecialchars($log['message']) ?></code></td>
                    <td><?= htmlspecialchars($log['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
