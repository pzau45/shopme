<?php require __DIR__ . '/../layouts/header.php'; ?>

<h1 style="font-size: 1.8rem; font-weight: 700; color: #fff; margin-bottom: 1.5rem;">
    Centro de Suporte & Mensagens
</h1>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
    <!-- Ticket List -->
    <div class="card">
        <h3 style="font-size: 1.1rem; font-weight: 600; color: #fff; margin-bottom: 1rem;">Os Seus Tickets de Suporte</h3>

        <?php if (empty($tickets)): ?>
            <p style="color: var(--text-secondary); text-align: center; padding: 2rem;">Nenhum ticket aberto de momento.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>N.º Ticket</th>
                        <th>Assunto</th>
                        <th>Estado</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $t): ?>
                        <tr>
                            <td><strong style="color: #fff;"><?= htmlspecialchars($t['ticket_number']) ?></strong></td>
                            <td><?= htmlspecialchars($t['subject']) ?></td>
                            <td><span class="badge badge-info"><?= htmlspecialchars($t['status']) ?></span></td>
                            <td><a href="/support/<?= $t['id'] ?>" class="btn btn-secondary btn-sm">Abrir Chat</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Create Ticket Form -->
    <div class="card">
        <h3 style="font-size: 1.1rem; font-weight: 600; color: #fff; margin-bottom: 1rem;">Abrir Novo Ticket</h3>
        <form action="/support/create" method="POST">
            <div class="form-group">
                <label class="form-label">Assunto</label>
                <input type="text" name="subject" class="form-control" placeholder="ex.: Dúvida sobre fatura" required>
            </div>

            <div class="form-group">
                <label class="form-label">Mensagem</label>
                <textarea name="message" class="form-control" rows="4" placeholder="Descreva a sua questão em detalhe..." required></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Enviar Ticket</button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
