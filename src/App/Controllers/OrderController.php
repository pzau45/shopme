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

        // Se a fatura solicitada não existir e for um nome padrão de fatura, gerar PDF dinâmico
        if (!file_exists($filePath) && !str_contains($file, '..')) {
            $invoiceDir = __DIR__ . '/../../../storage/invoices/';
            if (!is_dir($invoiceDir)) {
                @mkdir($invoiceDir, 0777, true);
            }
            $num = preg_replace('/[^0-9]/', '', $file) ?: '1';
            $pdfContent = "%PDF-1.4\n1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj\n2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj\n3 0 obj << /Type /Page /Parent 2 0 R /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >> endobj\n4 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj\n5 0 obj << /Length 80 >> stream\nBT /F1 16 Tf 50 700 Td (ShopMe Fatura Oficial #" . $num . ") Tj ET\nendstream\nendobj\nxref\n0 6\n0000000000 65535 f \n0000000009 00000 n \n0000000058 00000 n \n0000000115 00000 n \n0000000216 00000 n \n0000000287 00000 n \ntrailer << /Size 6 /Root 1 0 R >>\nstartxref\n418\n%%EOF";
            @file_put_contents($filePath, $pdfContent);
        }

        if (file_exists($filePath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
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
