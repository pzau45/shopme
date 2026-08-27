<?php require __DIR__ . '/../layouts/header.php'; ?>

<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 700; color: #fff;">Catálogo de Produtos</h1>
        <p style="color: var(--text-secondary); font-size: 0.95rem;">Explore a nossa seleção de equipamentos tecnológicos premium (<?= $totalItems ?> produtos no total).</p>
    </div>
</div>

<?php if (!empty($searchQuery)): ?>
    <div style="margin-bottom: 1.5rem; font-size: 1rem; color: var(--text-secondary); background: var(--bg-card); padding: 0.75rem 1.25rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
        Resultados encontrados para a pesquisa: <strong><?= $searchQuery ?></strong>
    </div>
<?php endif; ?>

<div class="grid-catalog" id="products-grid">
    <?php foreach ($products as $p): ?>
        <div class="product-card">
            <div class="product-img-wrapper">
                <span class="product-category-tag"><?= htmlspecialchars($p['category']) ?></span>
                <img src="<?= htmlspecialchars($p['image_url']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="product-img" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&auto=format&fit=crop&q=80';">
            </div>
            
            <div class="product-info">
                <h3 class="product-title"><?= htmlspecialchars($p['name']) ?></h3>
                <p class="product-desc"><?= htmlspecialchars($p['description']) ?></p>
                
                <div class="product-bottom">
                    <div class="product-price"><?= format_currency($p['price']) ?></div>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="/products/<?= $p['id'] ?>" class="btn btn-secondary btn-sm">Ver Detalhes</a>
                        
                        <form action="/cart/add" method="POST" style="margin: 0;">
                            <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-cart-plus"></i> Adicionar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Standard Numbered Pagination Bar -->
<?php if ($totalPages > 1): ?>
    <?php
    $queryParams = [];
    if (!empty($searchQuery)) {
        $queryParams['q'] = $searchQuery;
    }
    
    function buildPageUrl(int $targetPage, array $queryParams): string {
        $queryParams['page'] = $targetPage;
        return '/products?' . http_build_query($queryParams);
    }
    ?>
    <div class="pagination-wrapper" style="display: flex; justify-content: center; align-items: center; gap: 0.5rem; margin-top: 3rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
        <!-- Previous Page Button -->
        <?php if ($page > 1): ?>
            <a href="<?= buildPageUrl($page - 1, $queryParams) ?>" class="btn btn-secondary btn-sm">&laquo; Anterior</a>
        <?php else: ?>
            <span class="btn btn-secondary btn-sm" style="opacity: 0.4; cursor: not-allowed;">&laquo; Anterior</span>
        <?php endif; ?>

        <!-- Page Numbers -->
        <?php
        $startPage = max(1, $page - 2);
        $endPage   = min($totalPages, $page + 2);

        if ($startPage > 1) {
            echo '<a href="' . buildPageUrl(1, $queryParams) . '" class="pagination-num">1</a>';
            if ($startPage > 2) {
                echo '<span style="color: var(--text-muted);">...</span>';
            }
        }

        for ($i = $startPage; $i <= $endPage; $i++) {
            $activeClass = ($i === $page) ? 'active' : '';
            echo '<a href="' . buildPageUrl($i, $queryParams) . '" class="pagination-num ' . $activeClass . '">' . $i . '</a>';
        }

        if ($endPage < $totalPages) {
            if ($endPage < $totalPages - 1) {
                echo '<span style="color: var(--text-muted);">...</span>';
            }
            echo '<a href="' . buildPageUrl($totalPages, $queryParams) . '" class="pagination-num">' . $totalPages . '</a>';
        }
        ?>

        <!-- Next Page Button -->
        <?php if ($page < $totalPages): ?>
            <a href="<?= buildPageUrl($page + 1, $queryParams) ?>" class="btn btn-secondary btn-sm">Próximo &raquo;</a>
        <?php else: ?>
            <span class="btn btn-secondary btn-sm" style="opacity: 0.4; cursor: not-allowed;">Próximo &raquo;</span>
        <?php endif; ?>
    </div>
<?php endif; ?>

<style>
.pagination-num {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border-radius: var(--radius-sm);
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    color: var(--text-secondary);
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.pagination-num:hover {
    background: var(--bg-card-hover);
    color: #fff;
    border-color: var(--border-light);
}

.pagination-num.active {
    background: var(--accent-primary);
    color: #ffffff;
    border-color: var(--accent-primary);
    font-weight: 700;
}
</style>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
