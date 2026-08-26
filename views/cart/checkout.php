<?php require __DIR__ . '/../layouts/header.php'; ?>

<h1 style="font-size: 1.8rem; font-weight: 700; color: #fff; margin-bottom: 1.5rem;">
    Carrinho de Compras & Checkout
</h1>

<?php if (!empty($msg)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if (empty($cart)): ?>
    <div class="card" style="text-align: center; padding: 3rem;">
        <p style="font-size: 1.1rem; color: var(--text-secondary); margin-bottom: 1.5rem;">O seu carrinho de compras está atualmente vazio.</p>
        <a href="/products" class="btn btn-primary">Ver Catálogo de Produtos</a>
    </div>
<?php else: ?>
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <!-- Cart Items List -->
        <div>
            <div class="card">
                <h3 style="font-size: 1.1rem; font-weight: 600; color: #fff; margin-bottom: 1rem;">Itens Selecionados</h3>
                
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Preço Unit.</th>
                            <th>Qtd</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $subtotal = 0;
                        foreach ($cart as $item): 
                            $itemTotal = $item['price'] * $item['quantity'];
                            $subtotal += $itemTotal;
                        ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <img src="<?= htmlspecialchars($item['image']) ?>" alt="" style="width: 40px; height: 40px; border-radius: 4px; object-fit: cover;">
                                        <strong style="color: #fff;"><?= htmlspecialchars($item['name']) ?></strong>
                                    </div>
                                </td>
                                <td>€<?= number_format($item['price'], 2) ?></td>
                                <td><?= $item['quantity'] ?></td>
                                <td><strong style="color: #fff;">€<?= number_format($itemTotal, 2) ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Shipping Address Form -->
            <div class="card">
                <h3 style="font-size: 1.1rem; font-weight: 600; color: #fff; margin-bottom: 1rem;">Dados de Envio</h3>
                <form action="/checkout" method="POST" id="checkout-form">
                    <div class="form-group">
                        <label class="form-label">Morada de Entrega</label>
                        <textarea name="address" class="form-control" rows="3" placeholder="Insira a sua morada completa..." required>Rua das Flores 123, Lisboa, Portugal</textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Método de Pagamento</label>
                        <select name="payment_method" class="form-control">
                            <option value="card">Cartão de Crédito / Débito</option>
                            <option value="mbway">MB WAY</option>
                            <option value="transfer">Transferência Bancária</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary & Coupon Form (Race Condition Vulnerability target) -->
        <div>
            <div class="card">
                <h3 style="font-size: 1.1rem; font-weight: 600; color: #fff; margin-bottom: 1rem;">Cupão de Desconto</h3>
                
                <!-- Race Condition Coupon Form -->
                <form action="/cart/apply-coupon" method="POST" style="margin-bottom: 1rem;">
                    <div style="display: flex; gap: 0.5rem;">
                        <input type="text" name="coupon_code" class="form-control" placeholder="ex.: VIP50" required style="text-transform: uppercase;">
                        <button type="submit" class="btn btn-secondary btn-sm">Aplicar</button>
                    </div>
                </form>

                <?php if (!empty($appliedCoupon)): ?>
                    <div class="badge badge-success" style="margin-bottom: 1rem; width: 100%; text-align: center; padding: 6px;">
                        Cupão <?= htmlspecialchars($appliedCoupon['code']) ?> ativo (-<?= $appliedCoupon['discount_percent'] ?>%)
                    </div>
                <?php endif; ?>

                <div style="border-top: 1px solid var(--border-color); padding-top: 1rem; margin-top: 1rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: var(--text-secondary);">
                        <span>Subtotal:</span>
                        <span>€<?= number_format($subtotal, 2) ?></span>
                    </div>

                    <?php 
                    $discountAmount = 0;
                    if (!empty($appliedCoupon)) {
                        $discountAmount = $subtotal * ($appliedCoupon['discount_percent'] / 100);
                    }
                    $finalTotal = max(0, $subtotal - $discountAmount);
                    ?>

                    <?php if ($discountAmount > 0): ?>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: var(--success);">
                            <span>Desconto (<?= $appliedCoupon['discount_percent'] ?>%):</span>
                            <span>-€<?= number_format($discountAmount, 2) ?></span>
                        </div>
                    <?php endif; ?>

                    <div style="display: flex; justify-content: space-between; font-size: 1.3rem; font-weight: 700; color: #fff; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                        <span>Total Final:</span>
                        <span>€<?= number_format($finalTotal, 2) ?></span>
                    </div>

                    <button type="submit" form="checkout-form" class="btn btn-primary" style="width: 100%; margin-top: 1.5rem;">
                        Finalizar Encomenda ➔
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
