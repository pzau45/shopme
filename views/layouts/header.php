<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentUser = $_SESSION['user'] ?? null;
$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
$appPrefs = get_user_preferences();
$themeAttr = ($appPrefs['theme'] === 'light') ? 'data-theme="light"' : (($appPrefs['theme'] === 'system') ? 'data-theme="system"' : '');
?>
<!DOCTYPE html>
<html lang="pt-PT" <?= $themeAttr ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopMe — E-Commerce de Tecnologia & Eletrónica</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/static/css/style.css">
    <script src="/static/js/main.js" defer></script>
    <script>
        if (document.documentElement.getAttribute('data-theme') === 'system') {
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches) {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        }
    </script>
</head>
<body>

<header class="navbar">
    <div class="nav-container">
        <a href="/" class="brand-logo">
            <i class="fa-solid fa-bag-shopping" style="color: var(--accent-primary);"></i> ShopMe <span class="brand-badge">PRO</span>
        </a>

        <form action="/products" method="GET" class="nav-search">
            <i class="fa-solid fa-magnifying-glass nav-search-icon"></i>
            <input type="text" name="q" placeholder="Pesquisar produtos, marcas ou categorias..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
        </form>

        <ul class="nav-menu">
            <li><a href="/products" class="nav-link"><i class="fa-solid fa-store"></i> Catálogo</a></li>
            <li><a href="/cart" class="nav-link"><i class="fa-solid fa-cart-shopping"></i> Carrinho <span class="cart-badge"><?= $cartCount ?></span></a></li>
            
            <?php if ($currentUser): ?>
                <li><a href="/wallet" class="nav-link" style="color: #34d399;"><i class="fa-solid fa-wallet"></i> Carteira (<?= format_currency($currentUser['wallet_balance'] ?? 0) ?>)</a></li>
                <li><a href="/orders" class="nav-link"><i class="fa-solid fa-box"></i> Encomendas</a></li>
                <li><a href="/support" class="nav-link"><i class="fa-solid fa-headset"></i> Suporte</a></li>
                <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                    <li><a href="/admin" class="nav-link" style="color: #a855f7;"><i class="fa-solid fa-user-shield"></i> Admin</a></li>
                <?php endif; ?>
                <li>
                    <a href="/profile" class="user-dropdown">
                        <img src="/uploads/avatars/<?= htmlspecialchars($currentUser['avatar'] ?? 'default.png') ?>" alt="Avatar" class="avatar-img" onerror="this.onerror=null; this.src='/uploads/avatars/default.png'">
                        <span style="color: #fff; font-weight: 500; font-size: 0.9rem;"><?= htmlspecialchars($currentUser['display_name'] ?? $currentUser['full_name']) ?></span>
                    </a>
                </li>
                <li><a href="/logout" class="nav-link" style="color: var(--danger);"><i class="fa-solid fa-right-from-bracket"></i> Sair</a></li>
            <?php else: ?>
                <li><a href="/login" class="btn btn-secondary btn-sm"><i class="fa-solid fa-right-to-bracket"></i> Entrar</a></li>
                <li><a href="/register" class="btn btn-primary btn-sm"><i class="fa-solid fa-user-plus"></i> Registar</a></li>
            <?php endif; ?>
        </ul>
    </div>
</header>

<div class="main-wrapper">
    <div id="promo-banner-container"></div>
