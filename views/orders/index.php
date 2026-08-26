<?php require __DIR__ . '/../layouts/header.php'; ?>

<h1 style="font-size: 1.8rem; font-weight: 700; color: #fff; margin-bottom: 1.5rem;">
    Histórico de Encomendas
</h1>

<div class="card">
    <?php if (empty($orders)): ?>
        <p style="color: var(--text-secondary); text-align: center; padding: 2rem;">Ainda não efetuou qualquer encomenda.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>N.º Encomenda</th>
                    <th>Data</th>
                    <th>Destinatário</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td><strong style="color: #fff;"><?= htmlspecialchars($o['order_number']) ?></strong></td>
                        <td><?= htmlspecialchars($o['created_at']) ?></td>
                        <td><?= htmlspecialchars($o['customer_name']) ?></td>
                        <td><strong style="color: #fff;">€<?= number_format($o['total_amount'], 2) ?></strong></td>
                        <td><span class="badge badge-success"><?= htmlspecialchars($o['status']) ?></span></td>
                        <td>
                            <a href="/orders/<?= $o['id'] ?>" class="btn btn-secondary btn-sm">Ver Detalhe</a>
                            <a href="/orders/invoice/download?file=invoice_<?= $o['id'] ?>.pdf" class="btn btn-secondary btn-sm">📄 Fatura PDF</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
