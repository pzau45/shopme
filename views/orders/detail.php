<?php require __DIR__ . '/../layouts/header.php'; ?>

<div style="margin-bottom: 1.5rem;">
    <a href="/orders" style="color: var(--text-secondary); font-size: 0.9rem;">&larr; Voltar às encomendas</a>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 700; color: #fff;">
                Encomenda <?= htmlspecialchars($order['order_number']) ?>
            </h1>
            <p style="color: var(--text-secondary); font-size: 0.85rem;">Efetuada em: <?= htmlspecialchars($order['created_at']) ?></p>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            <!-- Path Traversal Vulnerable Invoice Download Link -->
            <a href="/orders/invoice/download?file=invoice_<?= $order['id'] ?>.pdf" class="btn btn-secondary btn-sm">
                📄 Descarregar Fatura PDF
            </a>

            <!-- LFI Vulnerable Template Link -->
            <a href="/orders/email/template?tpl=order_confirmation.php" class="btn btn-secondary btn-sm" target="_blank">
                ✉️ Ver Email de Confirmação
            </a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <div>
            <h3 style="font-size: 1.1rem; font-weight: 600; color: #fff; margin-bottom: 1rem;">Artigos da Encomenda</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Qtd</th>
                        <th>Preço Unit.</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order['items'] as $item): ?>
                        <tr>
                            <td><strong style="color: #fff;"><?= htmlspecialchars($item['product_name']) ?></strong></td>
                            <td><?= $item['quantity'] ?></td>
                            <td>€<?= number_format($item['unit_price'], 2) ?></td>
                            <td><strong style="color: #fff;">€<?= number_format($item['unit_price'] * $item['quantity'], 2) ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div>
            <div style="background: var(--bg-card-hover); padding: 1.25rem; border-radius: var(--radius-sm); border: 1px solid var(--border-light);">
                <h3 style="font-size: 1rem; font-weight: 600; color: #fff; margin-bottom: 0.75rem;">Resumo de Envio</h3>
                <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;">
                    <strong>Destinatário:</strong> <?= htmlspecialchars($order['customer_name']) ?>
                </p>
                <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1rem;">
                    <strong>Morada:</strong> <?= htmlspecialchars($order['shipping_address']) ?>
                </p>

                <div style="border-top: 1px solid var(--border-color); padding-top: 0.75rem;">
                    <div style="display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: 700; color: #fff;">
                        <span>Total Pago:</span>
                        <span>€<?= number_format($order['total_amount'], 2) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
