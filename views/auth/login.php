<?php require __DIR__ . '/../layouts/header.php'; ?>

<div style="max-width: 420px; margin: 3rem auto;">
    <div class="card">
        <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1.5rem; text-align: center; color: #fff;">
            Iniciar Sessão no ShopMe
        </h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="/login" method="POST">
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect ?? '/') ?>">
            
            <div class="form-group">
                <label class="form-label">Endereço de Email</label>
                <input type="email" name="email" class="form-control" placeholder="exemplo@shopme.local" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label">Palavra-passe</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; font-size: 0.85rem;">
                <a href="/reset-password" style="color: var(--text-secondary);">Esqueceu-se da palavra-passe?</a>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Entrar na Conta</button>
        </form>

        <div style="text-align: center; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border-color); font-size: 0.85rem; color: var(--text-secondary);">
            Ainda não tem conta? <a href="/register">Criar nova conta</a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
