<?php require __DIR__ . '/../layouts/header.php'; ?>

<div style="margin-bottom: 1.5rem;">
    <a href="/support" style="color: var(--text-secondary); font-size: 0.9rem;">&larr; Voltar aos tickets</a>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.4rem; font-weight: 700; color: #fff;">
                Ticket: <?= htmlspecialchars($ticket['subject']) ?> (<?= htmlspecialchars($ticket['ticket_number']) ?>)
            </h1>
            <span style="font-size: 0.85rem; color: var(--text-secondary);">Cliente: <?= htmlspecialchars($ticket['user_name']) ?></span>
        </div>
        <span class="badge badge-success"><?= htmlspecialchars($ticket['status']) ?></span>
    </div>

    <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 2rem;">
        <?php foreach ($ticket['messages'] as $msg): 
            $isAdmin = ($msg['sender_role'] ?? '') === 'admin';
        ?>
            <div style="max-width: 80%; align-self: <?= $isAdmin ? 'flex-start' : 'flex-end' ?>; background: <?= $isAdmin ? '#1f2937' : 'var(--accent-hover)' ?>; padding: 1rem 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <div style="display: flex; justify-content: space-between; gap: 1rem; margin-bottom: 0.4rem; font-size: 0.8rem; color: rgba(255,255,255,0.7);">
                    <strong><?= htmlspecialchars($msg['sender_name']) ?> <?= $isAdmin ? '(Suporte ShopMe)' : '' ?></strong>
                    <span><?= $msg['created_at'] ?></span>
                </div>

                <div style="color: #fff; font-size: 0.95rem; line-height: 1.5;">
                    <?= $msg['message'] ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <form action="/support/reply" method="POST" style="border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
        <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
        
        <div class="form-group">
            <label class="form-label">Escrever Resposta</label>
            <textarea name="message" class="form-control" rows="3" placeholder="Digite a sua mensagem..." required></textarea>
        </div>

        <button type="submit" class="btn btn-primary btn-sm">Enviar Resposta</button>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
