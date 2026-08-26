<?php require __DIR__ . '/../layouts/header.php'; ?>

<div style="margin-bottom: 1.5rem;">
    <a href="/admin" style="color: var(--text-secondary); font-size: 0.9rem;">&larr; Voltar ao Painel Admin</a>
</div>

<h1 style="font-size: 1.8rem; font-weight: 700; color: #fff; margin-bottom: 1.5rem;">
    Relatório do Sistema
</h1>

<?php if (!empty($msg)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if (isset($output)): ?>
    <div class="card">
        <h3 style="font-size: 1.1rem; font-weight: 600; color: #fff; margin-bottom: 1rem;">Resultado da Geração do Relatório</h3>
        <pre style="background: #000; color: #0f0; padding: 1.25rem; border-radius: var(--radius-sm); font-family: monospace; overflow-x: auto; font-size: 0.9rem;"><?= htmlspecialchars($output) ?></pre>
    </div>
<?php endif; ?>

<?php if (isset($userOrders)): ?>
    <div class="card">
        <h3 style="font-size: 1.1rem; font-weight: 600; color: #fff; margin-bottom: 1rem;">
            Relatório de Vendas do Utilizador: <?= htmlspecialchars($user['full_name']) ?> (<?= htmlspecialchars($user['display_name']) ?>)
        </h3>

        <?php if (empty($userOrders)): ?>
            <p style="color: var(--text-secondary);">Nenhuma encomenda encontrada para este utilizador.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>N.º Encomenda</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($userOrders as $ord): ?>
                        <tr>
                            <td><?= $ord['id'] ?></td>
                            <td><strong style="color: #fff;"><?= htmlspecialchars($ord['order_number']) ?></strong></td>
                            <td><?= htmlspecialchars($ord['customer_name']) ?></td>
                            <td>€<?= number_format($ord['total_amount'], 2) ?></td>
                            <td><?= htmlspecialchars($ord['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
