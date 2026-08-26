<?php require __DIR__ . '/../layouts/header.php'; ?>

<div style="margin-bottom: 1.5rem;">
    <a href="/products" style="color: var(--text-secondary); font-size: 0.9rem;">&larr; Voltar ao catálogo</a>
</div>

<div class="card" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2.5rem; align-items: start;">
    <div style="background: #1f2937; border-radius: var(--radius-md); overflow: hidden; height: 380px;">
        <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&auto=format&fit=crop&q=80';">
    </div>

    <div>
        <span class="badge badge-info" style="margin-bottom: 0.5rem;"><?= htmlspecialchars($product['category']) ?></span>
        <h1 style="font-size: 2rem; font-weight: 700; color: #fff; margin-bottom: 0.5rem;"><?= htmlspecialchars($product['name']) ?></h1>
        <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1rem;">SKU: <?= htmlspecialchars($product['sku']) ?> | Stock disponível: <?= $product['stock'] ?> un.</p>
        
        <div style="font-size: 2.2rem; font-weight: 800; color: #fff; margin-bottom: 1.5rem;">
            €<?= number_format($product['price'], 2) ?>
        </div>

        <p style="color: var(--text-secondary); line-height: 1.7; margin-bottom: 2rem;">
            <?= htmlspecialchars($product['description']) ?>
        </p>

        <form action="/cart/add" method="POST" style="display: flex; gap: 1rem; margin-bottom: 2rem;">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            <input type="number" name="quantity" value="1" min="1" max="10" class="form-control" style="width: 90px;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">🛒 Adicionar ao Carrinho</button>
        </form>

        <div style="border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
            <h4 style="font-size: 0.95rem; font-weight: 600; color: #fff; margin-bottom: 0.5rem;">
                🌐 Verificar Preço em Fornecedor Externo
            </h4>
            <div style="display: flex; gap: 0.5rem;">
                <input type="url" id="external-url-<?= $product['id'] ?>" class="form-control" placeholder="https://api.supplier.com/check-price" value="http://localhost:8080/api/v1/products/<?= $product['id'] ?>">
                <button type="button" class="btn btn-secondary btn-sm" onclick="checkExternalPrice(<?= $product['id'] ?>)">Verificar</button>
            </div>
            <div id="external-price-result-<?= $product['id'] ?>"></div>
        </div>
    </div>
</div>

<div class="card" style="margin-top: 2rem;">
    <h3 style="font-size: 1.25rem; font-weight: 700; color: #fff; margin-bottom: 1.5rem;">
        Avaliações dos Clientes (<?= count($reviews) ?>)
    </h3>

    <div style="margin-bottom: 2rem;">
        <?php if (empty($reviews)): ?>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Ainda não existem avaliações para este produto. Seja o primeiro a opinar!</p>
        <?php else: ?>
            <?php foreach ($reviews as $rev): ?>
                <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                        <strong style="color: #fff;">
                            <?= $rev['author_name'] ?>
                        </strong>
                        <span style="color: var(--warning);">★ <?= $rev['rating'] ?>/5</span>
                    </div>
                    <p style="color: var(--text-secondary); font-size: 0.9rem;">
                        <?= $rev['comment'] ?>
                    </p>
                    <span style="font-size: 0.75rem; color: var(--text-muted);"><?= $rev['created_at'] ?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div style="background: var(--bg-card-hover); padding: 1.25rem; border-radius: var(--radius-sm); border: 1px solid var(--border-light);">
        <h4 style="font-size: 1rem; font-weight: 600; color: #fff; margin-bottom: 1rem;">Deixar a sua Avaliação</h4>
        <form action="/products/review" method="POST">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            
            <?php if (!isset($_SESSION['user'])): ?>
                <div class="form-group">
                    <label class="form-label">O seu Nome</label>
                    <input type="text" name="author_name" class="form-control" placeholder="Seu nome" required>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label class="form-label">Classificação</label>
                <select name="rating" class="form-control" style="width: 150px;">
                    <option value="5">★★★★★ (5/5)</option>
                    <option value="4">★★★★☆ (4/5)</option>
                    <option value="3">★★★☆☆ (3/5)</option>
                    <option value="2">★★☆☆☆ (2/5)</option>
                    <option value="1">★☆☆☆☆ (1/5)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Comentário</label>
                <textarea name="comment" class="form-control" rows="3" placeholder="Escreva a sua opinião sobre o produto..." required></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-sm">Submeter Avaliação</button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
