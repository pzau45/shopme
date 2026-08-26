<?php

// Front Controller — ShopMe Application Entrypoint

// Autoload via Composer PSR-4
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    // Fallback PSR-4 autoloader if vendor directory is not yet generated
    spl_autoload_register(function ($class) {
        $prefix = 'App\\';
        $baseDir = __DIR__ . '/../src/App/';
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }
        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    });
}

// Require global helpers
require_once __DIR__ . '/../src/App/helpers.php';

use App\Router;
use App\Controllers\AuthController;
use App\Controllers\ProductController;
use App\Controllers\CartController;
use App\Controllers\OrderController;
use App\Controllers\ProfileController;
use App\Controllers\SupportController;
use App\Controllers\AdminController;
use App\Controllers\ExternalApiController;
use App\Api\ApiProductController;
use App\Api\ApiOrderController;
use App\Api\ApiUserController;

$router = new Router();

// Auth Routes
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/login/corporate', [AuthController::class, 'showCorporateLogin']);
$router->post('/login/corporate', [AuthController::class, 'corporateLogin']);
$router->get('/mfa', [\App\Controllers\MfaController::class, 'showMfa']);
$router->post('/mfa/verify', [\App\Controllers\MfaController::class, 'verify']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/reset-password', [AuthController::class, 'showResetPassword']);
$router->post('/reset-password', [AuthController::class, 'requestResetPassword']);
$router->get('/logout', [AuthController::class, 'logout']);

// OAuth Routes
$router->get('/auth/oauth/login', [\App\Controllers\OAuthController::class, 'login']);
$router->post('/api/v1/oauth/token', [\App\Controllers\OAuthController::class, 'token']);

// Digital Wallet Routes
$router->get('/wallet', [\App\Controllers\WalletController::class, 'index']);
$router->post('/wallet/transfer', [\App\Controllers\WalletController::class, 'transfer']);

// Legacy XML Catalog Routes (XPath Injection)
$router->get('/legacy/catalog/search', [\App\Controllers\LegacyCatalogController::class, 'search']);

// Order Refund Route (Race Condition)
$router->post('/orders/refund', [OrderController::class, 'refund']);

// Catalog Routes
$router->get('/', [ProductController::class, 'index']);
$router->get('/products', [ProductController::class, 'index']);
$router->get('/products/{id}', [ProductController::class, 'detail']);
$router->post('/products/review', [ProductController::class, 'addReview']);

// Cart & Checkout Routes
$router->get('/cart', [CartController::class, 'showCart']);
$router->post('/cart/add', [CartController::class, 'add']);
$router->post('/cart/apply-coupon', [CartController::class, 'applyCoupon']);
$router->post('/checkout', [CartController::class, 'checkout']);

// User Profile Routes
$router->get('/profile', [ProfileController::class, 'index']);
$router->post('/profile/update', [ProfileController::class, 'updateProfile']);
$router->post('/profile/avatar', [ProfileController::class, 'uploadAvatar']);
$router->post('/profile/preferences', [ProfileController::class, 'savePreferences']);

// Orders Routes
$router->get('/orders', [OrderController::class, 'index']);
$router->get('/orders/{id}', [OrderController::class, 'detail']);
$router->get('/orders/invoice/download', [OrderController::class, 'downloadInvoice']);
$router->get('/orders/email/template', [OrderController::class, 'viewTemplate']);

// Support Tickets Routes
$router->get('/support', [SupportController::class, 'index']);
$router->get('/support/{id}', [SupportController::class, 'detail']);
$router->post('/support/create', [SupportController::class, 'create']);
$router->post('/support/reply', [SupportController::class, 'reply']);

// Admin Dashboard Routes
$router->get('/admin', [AdminController::class, 'dashboard']);
$router->get('/admin/dashboard', [AdminController::class, 'dashboard']);
$router->get('/admin/products', [AdminController::class, 'products']);
$router->post('/admin/products/add', [AdminController::class, 'addProduct']);
$router->post('/admin/products/import-xml', [AdminController::class, 'importXml']);
$router->post('/admin/users/delete', [AdminController::class, 'deleteUser']);
$router->get('/admin/users/delete', [AdminController::class, 'deleteUser']);
$router->post('/admin/reports/generate', [AdminController::class, 'generateReport']);
$router->get('/admin/reports/user', [AdminController::class, 'userReport']);
$router->get('/admin/logs', [AdminController::class, 'logs']);

// External Utility / SSRF Endpoint
$router->get('/api/v1/external/check-price', [ExternalApiController::class, 'checkPrice']);
$router->post('/api/v1/external/check-price', [ExternalApiController::class, 'checkPrice']);

// REST API v1 Routes
$router->get('/api/v1/products', [ApiProductController::class, 'index']);
$router->get('/api/v1/products/{id}', [ApiProductController::class, 'detail']);
$router->get('/api/v1/orders/{id}', [ApiOrderController::class, 'detail']);
$router->get('/api/v1/users/{id}', [ApiUserController::class, 'detail']);
$router->put('/api/v1/users/{id}', [ApiUserController::class, 'update']);
$router->patch('/api/v1/users/{id}', [ApiUserController::class, 'update']);

// Dispatch HTTP request
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
