<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\Coupon;
use App\Models\Order;

class CartController {
    public function showCart(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $cart = $_SESSION['cart'] ?? [];
        $appliedCoupon = $_SESSION['applied_coupon'] ?? null;
        $error = $_GET['error'] ?? null;
        $msg = $_GET['msg'] ?? null;

        require_read_view('views/cart/checkout.php', [
            'cart' => $cart,
            'appliedCoupon' => $appliedCoupon,
            'error' => $error,
            'msg' => $msg
        ]);
    }

    public function add(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity  = (int)($_POST['quantity'] ?? 1);

        $product = Product::findById($productId);
        if ($product) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$productId] = [
                    'id'       => $product['id'],
                    'name'     => $product['name'],
                    'price'    => $product['price'],
                    'image'    => $product['image_url'],
                    'quantity' => $quantity
                ];
            }
        }

        header("Location: /cart?msg=Item+adicionado+ao+carrinho");
        exit;
    }

    public function applyCoupon(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $code = trim($_POST['coupon_code'] ?? '');
        $result = Coupon::applyWithRaceCondition($code);

        if (isset($result['error'])) {
            header("Location: /cart?error=" . urlencode($result['error']));
            exit;
        }

        $_SESSION['applied_coupon'] = $result['coupon'];
        header("Location: /cart?msg=" . urlencode("Cupão " . $code . " aplicado com sucesso!"));
        exit;
    }

    public function checkout(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            header("Location: /login?redirect=/cart");
            exit;
        }

        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            header("Location: /cart?error=O+seu+carrinho+está+vazio");
            exit;
        }

        $shippingAddress = $_POST['address'] ?? 'Morada Principal';
        $coupon = $_SESSION['applied_coupon'] ?? null;

        $subtotal = 0;
        $items = [];
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
            $items[] = [
                'product_id'   => $item['id'],
                'product_name' => $item['name'],
                'quantity'     => $item['quantity'],
                'unit_price'   => $item['price']
            ];
        }

        $discount = 0;
        if ($coupon) {
            $discount = $subtotal * ($coupon['discount_percent'] / 100);
        }

        $totalAmount = max(0, $subtotal - $discount);

        $orderId = Order::create([
            'user_id'          => $user['id'],
            'customer_name'    => $user['full_name'],
            'shipping_address' => $shippingAddress,
            'total_amount'     => $totalAmount,
            'coupon_code'      => $coupon['code'] ?? null
        ], $items);

        unset($_SESSION['cart']);
        unset($_SESSION['applied_coupon']);

        header("Location: /orders/" . $orderId . "?msg=Encomenda+efetuada+com+sucesso");
        exit;
    }
}
