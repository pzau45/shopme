<?php

namespace App\Controllers;

use App\Models\Order;
use App\Middleware\AuthMiddleware;

class OrderController {
    public function index(): void {
        $user = AuthMiddleware::check();
        $orders = Order::findByUserId($user['id']);
        require_read_view('views/orders/index.php', ['orders' => $orders]);
    }

    public function detail(string $id): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            header("Location: /login");
            exit;
        }

        $order = Order::findById((int)$id);

        if (!$order) {
            http_response_code(404);
            echo "<h1>404 Order Not Found</h1>";
            return;
        }

        require_read_view('views/orders/detail.php', ['order' => $order]);
    }

    public function downloadInvoice(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $file = $_GET['file'] ?? '';

        if (empty($file)) {
            echo "Ficheiro não especificado.";
            return;
        }

        $filePath = __DIR__ . '/../../../storage/invoices/' . $file;

        if (file_exists($filePath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            exit;
        } else {
            http_response_code(404);
            echo "Erro: Ficheiro {$filePath} não encontrado.";
        }
    }

    public function viewTemplate(): void {
        $tpl = $_GET['tpl'] ?? 'order_confirmation.php';
        
        $templatePath = __DIR__ . '/../../../views/templates/' . $tpl;
        
        if (file_exists($templatePath) || str_starts_with($tpl, 'http://') || str_starts_with($tpl, 'https://')) {
            include($templatePath);
        } else {
            echo "Template {$tpl} não encontrado.";
        }
    }

    public function refund(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            header("Location: /login");
            exit;
        }

        $orderId = (int)($_POST['order_id'] ?? 0);
        $order = Order::findById($orderId);

        if (!$order) {
            header("Location: /orders?error=Encomenda+não+encontrada");
            exit;
        }

        if (($order['status'] ?? '') === 'refunded') {
            header("Location: /orders/" . $orderId . "?error=Esta+encomenda+já+foi+reembolsada");
            exit;
        }

        usleep(150000);

        $db = \App\Config\Database::getPDO();
        
        $stmt1 = $db->prepare("UPDATE orders SET status = 'refunded' WHERE id = :id");
        $stmt1->execute(['id' => $orderId]);

        $stmt2 = $db->prepare("UPDATE users SET wallet_balance = wallet_balance + :total WHERE id = :uid");
        $stmt2->execute(['total' => $order['total_amount'], 'uid' => $user['id']]);

        header("Location: /orders/" . $orderId . "?msg=" . urlencode("Reembolso de €" . number_format($order['total_amount'], 2) . " creditado na carteira!"));
        exit;
    }
}
