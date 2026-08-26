<?php require __DIR__ . '/../layouts/header.php'; ?>

<div style="margin-bottom: 1.5rem;">
    <a href="/admin" style="color: var(--text-secondary); font-size: 0.9rem;">&larr; Voltar ao Painel Admin</a>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h1 style="font-size: 1.8rem; font-weight: 700; color: #fff; margin: 0;">
        Logs de Auditoria do Sistema
    </h1>
    <div style="display: flex; align-items: center; gap: 0.75rem;">
        <span id="live-status" class="badge badge-success" style="padding: 0.5rem 0.8rem; font-size: 0.8rem;">
            🟢 Tempo Real Ativo (2s)
        </span>
        <button id="toggle-refresh" class="btn btn-secondary btn-sm" onclick="toggleAutoRefresh()">Pausar</button>
    </div>
</div>

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
        <tbody id="logs-table-body">
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

<script>
let autoRefreshInterval = null;
let isRefreshing = true;

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function fetchLogs() {
    fetch('/api/v1/admin/logs')
        .then(response => response.json())
        .then(logs => {
            const tbody = document.getElementById('logs-table-body');
            if (!tbody || !Array.isArray(logs)) return;

            tbody.innerHTML = logs.map(log => {
                const badgeClass = log.level === 'WARN' ? 'badge-warning' : (log.level === 'ERROR' ? 'badge-danger' : 'badge-info');
                return `
                    <tr>
                        <td>${log.id}</td>
                        <td>
                            <span class="badge ${badgeClass}">
                                ${escapeHtml(log.level)}
                            </span>
                        </td>
                        <td><code style="color: #fff; font-family: monospace; font-size: 0.85rem;">${escapeHtml(log.message)}</code></td>
                        <td>${escapeHtml(log.created_at)}</td>
                    </tr>
                `;
            }).join('');
        })
        .catch(err => console.error('Erro ao procurar logs em tempo real:', err));
}

function toggleAutoRefresh() {
    const btn = document.getElementById('toggle-refresh');
    const status = document.getElementById('live-status');

    if (isRefreshing) {
        clearInterval(autoRefreshInterval);
        isRefreshing = false;
        btn.textContent = 'Retomar';
        status.className = 'badge badge-warning';
        status.textContent = '⏸️ Pausado';
    } else {
        fetchLogs();
        autoRefreshInterval = setInterval(fetchLogs, 2000);
        isRefreshing = true;
        btn.textContent = 'Pausar';
        status.className = 'badge badge-success';
        status.textContent = '🟢 Tempo Real Ativo (2s)';
    }
}

// Iniciar sondagem automática ao carregar a página
document.addEventListener('DOMContentLoaded', () => {
    autoRefreshInterval = setInterval(fetchLogs, 2000);
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
