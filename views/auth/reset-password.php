<?php require __DIR__ . '/../layouts/header.php'; ?>

<div style="max-width: 420px; margin: 3rem auto;">
    <div class="card">
        <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; text-align: center; color: #fff;">
            Recuperar Palavra-passe
        </h2>
        <p style="font-size: 0.85rem; color: var(--text-secondary); text-align: center; margin-bottom: 1.5rem;">
            Insira o seu email registado para gerar um token de recuperação de acesso.
        </p>

        <?php if (!empty($msg)): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="/reset-password" method="POST">
            <div class="form-group">
                <label class="form-label">Endereço de Email</label>
                <input type="email" name="email" class="form-control" placeholder="utilizador@example.com" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Solicitar Token de Recuperação</button>
        </form>

        <div style="text-align: center; margin-top: 1.5rem; font-size: 0.85rem;">
            <a href="/login">Voltar para o Login</a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
