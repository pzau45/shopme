<?php require __DIR__ . '/../layouts/header.php'; ?>

<div style="max-width: 440px; margin: 3rem auto;">
    <div class="card">
        <h2 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 0.5rem; text-align: center; color: #fff;">
            🏢 Login Corporativo SSO (LDAP Directory)
        </h2>
        <p style="font-size: 0.85rem; color: var(--text-secondary); text-align: center; margin-bottom: 1.5rem;">
            Autenticação unificada para funcionários e diretores ShopMe via OpenLDAP.
        </p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="/login/corporate" method="POST">
            <div class="form-group">
                <label class="form-label">Identificador Corporativo (UID / Username)</label>
                <input type="text" name="username" class="form-control" placeholder="ex.: corp_admin" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label">Palavra-passe Corporativa</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Autenticar via LDAP SSO</button>
        </form>

        <div style="text-align: center; margin-top: 1.5rem; font-size: 0.85rem;">
            <a href="/login">Voltar ao Login Normal de Cliente</a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
