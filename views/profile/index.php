<?php require __DIR__ . '/../layouts/header.php'; ?>

<h1 style="font-size: 1.8rem; font-weight: 700; color: #fff; margin-bottom: 1.5rem;">
    Perfil do Utilizador
</h1>

<?php if (!empty($_GET['msg'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
<?php endif; ?>

<?php if (!empty($_GET['error'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
    <div>
        <div class="card" style="text-align: center;">
            <img src="/uploads/avatars/<?= htmlspecialchars($user['avatar'] ?? 'default.png') ?>" alt="Avatar" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid var(--accent-primary); margin-bottom: 1rem;" onerror="this.onerror=null; this.src='/uploads/avatars/default.png'">
            
            <h3 style="font-size: 1.1rem; font-weight: 600; color: #fff;"><?= htmlspecialchars($user['display_name'] ?? $user['full_name']) ?></h3>
            <p style="color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 1.5rem;"><?= htmlspecialchars($user['email']) ?></p>

            <span class="badge badge-info" style="margin-bottom: 1.5rem;">Role: <?= htmlspecialchars($user['role'] ?? 'customer') ?></span>

            <form action="/profile/avatar" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label" style="font-size: 0.8rem;">Alterar Fotografia de Perfil</label>
                    <input type="file" name="avatar" class="form-control" style="font-size: 0.8rem;">
                </div>
                <button type="submit" class="btn btn-secondary btn-sm" style="width: 100%;">Carregar Novo Avatar</button>
            </form>
        </div>
    </div>

    <div>
        <div class="card">
            <h3 style="font-size: 1.1rem; font-weight: 600; color: #fff; margin-bottom: 1.5rem;">Informações Pessoais</h3>
            
            <form action="/profile/update" method="POST">
                <div class="form-group">
                    <label class="form-label">Nome Completo</label>
                    <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Nome de Exibição / Alcunha (Público)</label>
                    <input type="text" name="display_name" class="form-control" value="<?= htmlspecialchars($user['display_name'] ?? $user['full_name']) ?>">
                    <span style="font-size: 0.75rem; color: var(--text-muted);">Nome utilizado em relatórios e avaliações.</span>
                </div>

                <div class="form-group">
                    <label class="form-label">Endereço de Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>

                <button type="submit" class="btn btn-primary">Guardar Alterações</button>
            </form>
        </div>

        <div class="card">
            <h3 style="font-size: 1.1rem; font-weight: 600; color: #fff; margin-bottom: 1rem;">Preferências da Conta</h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1rem;">
                Configure a formatação de relatórios e tema de exibição.
            </p>

            <form action="/profile/preferences" method="POST">
                <div class="form-group">
                    <label class="form-label">Tema de Exibição</label>
                    <select name="theme" class="form-control">
                        <option value="dark" <?= ($preferences['theme'] ?? 'dark') === 'dark' ? 'selected' : '' ?>>Escuro (Padrão)</option>
                        <option value="light" <?= ($preferences['theme'] ?? '') === 'light' ? 'selected' : '' ?>>Claro</option>
                        <option value="system" <?= ($preferences['theme'] ?? '') === 'system' ? 'selected' : '' ?>>Sistema</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Moeda Preferida</label>
                    <select name="currency" class="form-control">
                        <option value="EUR" <?= ($preferences['currency'] ?? 'EUR') === 'EUR' ? 'selected' : '' ?>>Euro (€)</option>
                        <option value="USD" <?= ($preferences['currency'] ?? '') === 'USD' ? 'selected' : '' ?>>Dólar US ($)</option>
                        <option value="GBP" <?= ($preferences['currency'] ?? '') === 'GBP' ? 'selected' : '' ?>>Libra Sterling (£)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Formato de Relatórios</label>
                    <select name="report_format" class="form-control">
                        <option value="pdf" <?= ($preferences['report_format'] ?? 'pdf') === 'pdf' ? 'selected' : '' ?>>PDF</option>
                        <option value="csv" <?= ($preferences['report_format'] ?? '') === 'csv' ? 'selected' : '' ?>>CSV</option>
                        <option value="json" <?= ($preferences['report_format'] ?? '') === 'json' ? 'selected' : '' ?>>JSON</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-secondary btn-sm">Guardar Preferências</button>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
