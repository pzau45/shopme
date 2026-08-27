<?php require __DIR__ . '/../layouts/header.php'; ?>

<h1 style="font-size: 1.8rem; font-weight: 700; color: #fff; margin-bottom: 1.5rem;">
    Carteira Digital ShopMe Pay
</h1>

<?php if (!empty($_GET['msg'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
<?php endif; ?>

<?php if (!empty($_GET['error'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
    <!-- Wallet Card -->
    <div>
        <div class="card" style="background: linear-gradient(135deg, #1e1b4b, #311b92); border: 1px solid rgba(99,102,241,0.4);">
            <span style="color: #a5b4fc; font-size: 0.85rem; font-weight: 600;">Saldo Disponível</span>
            <div style="font-size: 2.5rem; font-weight: 800; color: #fff; margin: 0.75rem 0;">
                <?= format_currency($user['wallet_balance'] ?? 0) ?>
            </div>
            <p style="color: #c7d2fe; font-size: 0.8rem;">
                Utilize o seu saldo em compras na loja ou transfira fundos entre contas.
            </p>
        </div>
    </div>

    <!-- Transfer Funds Form (Race Condition Target) -->
    <div>
        <div class="card">
            <h3 style="font-size: 1.1rem; font-weight: 600; color: #fff; margin-bottom: 1rem;">Transferir Saldo Instantâneo</h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1.25rem;">
                Envie saldo da sua carteira para outro cliente ShopMe sem taxas.
            </p>

            <!-- Check-Then-Act Race Condition Form -->
            <form action="/wallet/transfer" method="POST">
                <div class="form-group">
                    <label class="form-label">Email do Destinatário</label>
                    <input type="email" name="recipient_email" class="form-control" placeholder="ana@example.com" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Montante a Transferir (€)</label>
                    <input type="number" step="0.01" name="amount" class="form-control" placeholder="100.00" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Transferir Fundos Instantaneamente</button>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
