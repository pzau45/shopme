<?php require __DIR__ . '/../layouts/header.php'; ?>

<div style="margin-bottom: 1.5rem;">
    <a href="/admin" style="color: var(--text-secondary); font-size: 0.9rem;">&larr; Voltar ao Painel Admin</a>
</div>

<h1 style="font-size: 1.8rem; font-weight: 700; color: #fff; margin-bottom: 1.5rem;">
    Gestão de Catálogo de Produtos
</h1>

<?php if (!empty($_GET['msg'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
<?php endif; ?>

<?php if (!empty($_GET['error'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
    <!-- Products Table -->
    <div class="card">
        <h3 style="font-size: 1.1rem; font-weight: 600; color: #fff; margin-bottom: 1rem;">Lista de Produtos (<?= count($products) ?>)</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Nome</th>
                    <th>Categoria</th>
                    <th>Preço</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                    <tr>
                        <td><code style="color: var(--accent-primary);"><?= htmlspecialchars($p['sku']) ?></code></td>
                        <td><strong style="color: #fff;"><?= htmlspecialchars($p['name']) ?></strong></td>
                        <td><?= htmlspecialchars($p['category']) ?></td>
                        <td>€<?= number_format($p['price'], 2) ?></td>
                        <td><?= $p['stock'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- XML Import Form (XXE Flaw Target) -->
    <div>
        <div class="card">
            <h3 style="font-size: 1.1rem; font-weight: 600; color: #fff; margin-bottom: 0.75rem;">Importação de Catálogo XML</h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1.25rem;">
                Carregue um ficheiro XML com novos produtos para lote automático.
            </p>

            <!-- XXE Import Form -->
            <form action="/admin/products/import-xml" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label">Ficheiro XML</label>
                    <input type="file" name="xml_file" class="form-control" accept=".xml" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">📥 Importar Ficheiro XML</button>
            </form>
        </div>

        <!-- Manual Add Product Form -->
        <div class="card">
            <h3 style="font-size: 1.1rem; font-weight: 600; color: #fff; margin-bottom: 1rem;">Adicionar Novo Produto</h3>
            <form action="/admin/products/add" method="POST">
                <div class="form-group">
                    <label class="form-label">SKU</label>
                    <input type="text" name="sku" class="form-control" placeholder="PROD-999" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nome do Produto</label>
                    <input type="text" name="name" class="form-control" placeholder="Ex.: Rato Gamer" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Categoria</label>
                    <input type="text" name="category" class="form-control" placeholder="Periféricos" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Preço (€)</label>
                    <input type="number" step="0.01" name="price" class="form-control" placeholder="49.99" required>
                </div>
                <button type="submit" class="btn btn-secondary btn-sm" style="width: 100%;">Guardar Produto</button>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
