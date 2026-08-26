<?php require __DIR__ . '/../layouts/header.php'; ?>

<h1 style="font-size: 1.8rem; font-weight: 700; color: #fff; margin-bottom: 1.5rem;">
    Catálogo XML Legado (B2B Infrastructure)
</h1>

<div class="card" style="margin-bottom: 2rem;">
    <h3 style="font-size: 1.1rem; font-weight: 600; color: #fff; margin-bottom: 1rem;">Pesquisa no Catálogo XML</h3>
    
    <form action="/legacy/catalog/search" method="GET" style="display: flex; gap: 1rem; align-items: flex-end;">
        <div class="form-group" style="flex: 1; margin: 0;">
            <label class="form-label">Categoria XML</label>
            <input type="text" name="cat" class="form-control" placeholder="Hardware" value="<?= htmlspecialchars($cat) ?>">
        </div>

        <div class="form-group" style="width: 160px; margin: 0;">
            <label class="form-label">Preço Máximo (€)</label>
            <input type="text" name="max_price" class="form-control" value="5000">
        </div>

        <button type="submit" class="btn btn-primary">Pesquisar XML</button>
    </form>
</div>

<div class="card">
    <h3 style="font-size: 1.1rem; font-weight: 600; color: #fff; margin-bottom: 1rem;">Resultados Encontrados (<?= count($results) ?>)</h3>

    <?php if (empty($results)): ?>
        <p style="color: var(--text-secondary);">Nenhum produto encontrado no ficheiro XML.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Nome do Equipamento</th>
                    <th>Categoria</th>
                    <th>Preço</th>
                    <th>Notas Internas</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $res): ?>
                    <tr>
                        <td><code style="color: var(--accent-primary);"><?= htmlspecialchars($res['sku']) ?></code></td>
                        <td><strong style="color: #fff;"><?= htmlspecialchars($res['name']) ?></strong></td>
                        <td><?= htmlspecialchars($res['category']) ?></td>
                        <td>€<?= number_format($res['price'], 2) ?></td>
                        <td><span class="badge badge-warning"><?= htmlspecialchars($res['secret_notes']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
