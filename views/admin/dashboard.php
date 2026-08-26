<?php require __DIR__ . '/../layouts/header.php'; ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 700; color: #fff;">Painel de Administração ShopMe</h1>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">Gestão de utilizadores, vendas, relatórios e auditoria do sistema.</p>
    </div>

    <div style="display: flex; gap: 0.75rem;">
        <a href="/admin/products" class="btn btn-secondary btn-sm">📦 Gerir Produtos</a>
        <a href="/admin/logs" class="btn btn-secondary btn-sm">📜 Ver Logs de Sistema</a>
    </div>
</div>

<?php if (!empty($_GET['msg'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="card" style="margin: 0;">
        <span style="color: var(--text-muted); font-size: 0.85rem;">Total de Utilizadores</span>
        <div style="font-size: 2rem; font-weight: 700; color: #fff; margin-top: 0.5rem;"><?= count($users) ?></div>
    </div>
    <div class="card" style="margin: 0;">
        <span style="color: var(--text-muted); font-size: 0.85rem;">Produtos Registados</span>
        <div style="font-size: 2rem; font-weight: 700; color: var(--accent-primary); margin-top: 0.5rem;"><?= count($products) ?></div>
    </div>
    <div class="card" style="margin: 0;">
        <span style="color: var(--text-muted); font-size: 0.85rem;">Total de Encomendas</span>
        <div style="font-size: 2rem; font-weight: 700; color: var(--success); margin-top: 0.5rem;"><?= count($orders) ?></div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
    <div class="card">
        <h3 style="font-size: 1.1rem; font-weight: 600; color: #fff; margin-bottom: 1rem;">Gestão de Utilizadores</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email / Nome</th>
                    <th>Role</th>
                    <th>Relatório</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td>
                            <strong style="color: #fff;"><?= $u['display_name'] ?? $u['full_name'] ?></strong>
                            <br><span style="font-size: 0.8rem; color: var(--text-muted);"><?= htmlspecialchars($u['email']) ?></span>
                        </td>
                        <td><span class="badge <?= $u['role'] === 'admin' ? 'badge-danger' : 'badge-info' ?>"><?= htmlspecialchars($u['role']) ?></span></td>
                        <td>
                            <a href="/admin/reports/user?user_id=<?= $u['id'] ?>" class="btn btn-secondary btn-sm" style="font-size: 0.75rem;">📊 Relatório</a>
                        </td>
                        <td>
                            <form action="/admin/users/delete" method="POST" style="margin: 0;" onsubmit="return confirm('Eliminar utilizador?');">
                                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h3 style="font-size: 1.1rem; font-weight: 600; color: #fff; margin-bottom: 1rem;">Gerar Relatório Executivo</h3>
        <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1.25rem;">
            Exporte o relatório consolidado de vendas via gerador de sistema.
        </p>

        <form action="/admin/reports/generate" method="POST">
            <div class="form-group">
                <label class="form-label">Título do Relatório</label>
                <input type="text" name="title" class="form-control" placeholder="Relatorio_Vendas_Q3" required>
            </div>

            <div class="form-group">
                <label class="form-label">Formato</label>
                <select name="format" class="form-control">
                    <option value="pdf">PDF Document</option>
                    <option value="csv">CSV Spreadsheet</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Gerar & Exportar</button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
