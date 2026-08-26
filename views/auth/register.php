<?php require __DIR__ . '/../layouts/header.php'; ?>

<div style="max-width: 480px; margin: 3rem auto;">
    <div class="card">
        <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1.5rem; text-align: center; color: #fff;">
            Registar Nova Conta
        </h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="/register" method="POST">
            <div class="form-group">
                <label class="form-label">Nome Completo</label>
                <input type="text" name="full_name" class="form-control" placeholder="ex.: Carlos Silva" required>
            </div>

            <div class="form-group">
                <label class="form-label">Nome de Exibição / Alcunha (Público)</label>
                <input type="text" name="display_name" class="form-control" placeholder="ex.: CarlosS">
            </div>

            <div class="form-group">
                <label class="form-label">Endereço de Email</label>
                <input type="email" name="email" class="form-control" placeholder="carlos@example.com" required>
            </div>

            <div class="form-group">
                <label class="form-label">Palavra-passe</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Criar Conta ShopMe</button>
        </form>

        <div style="text-align: center; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border-color); font-size: 0.85rem; color: var(--text-secondary);">
            Já possui uma conta? <a href="/login">Entrar aqui</a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
