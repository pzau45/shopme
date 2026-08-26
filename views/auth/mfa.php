<?php require __DIR__ . '/../layouts/header.php'; ?>

<div style="max-width: 420px; margin: 3rem auto;">
    <div class="card">
        <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem; text-align: center; color: #fff;">
            Autenticação de Dois Fatores (MFA)
        </h2>
        <p style="font-size: 0.85rem; color: var(--text-secondary); text-align: center; margin-bottom: 1.5rem;">
            Insira o código de verificação de 4 dígitos enviado para a sua conta.
        </p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="/mfa/verify" method="POST">
            <div class="form-group">
                <label class="form-label">Código OTP</label>
                <input type="text" name="otp_code" class="form-control" placeholder="••••" maxlength="6" required autofocus style="text-align: center; font-size: 1.5rem; letter-spacing: 5px;">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Verificar Código OTP</button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
