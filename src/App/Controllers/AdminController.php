<?php

namespace App\Controllers;

use App\Middleware\AuthMiddleware;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\Ticket;
use App\Services\XmlImportService;
use App\Config\Database;
use PDO;

class AdminController {
    public function dashboard(): void {
        AuthMiddleware::checkAdmin();

        $users = User::all();
        $products = Product::all();
        $orders = Order::all();

        require_read_view('views/admin/dashboard.php', [
            'users' => $users,
            'products' => $products,
            'orders' => $orders
        ]);
    }

    public function products(): void {
        AuthMiddleware::checkAdmin();
        $products = Product::all();
        require_read_view('views/admin/products.php', ['products' => $products]);
    }

    public function addProduct(): void {
        AuthMiddleware::checkAdmin();
        Product::create($_POST);
        header("Location: /admin/products?msg=Produto+criado+com+sucesso");
        exit;
    }

    public function deleteUser(): void {
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id > 0) {
            $db = Database::getPDO();
            $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
            $stmt->execute(['id' => $id]);
        }
        header("Location: /admin/dashboard?msg=Utilizador+eliminado");
        exit;
    }

    public function generateReport(): void {
        AuthMiddleware::checkAdmin();
        $reportTitle = $_POST['title'] ?? 'relatorio_vendas';
        $format      = $_POST['format'] ?? 'pdf';

        $cmd = "echo 'Report: {$reportTitle}' > /tmp/last_report.txt 2>&1";
        $output = shell_exec($cmd);

        require_read_view('views/admin/reports.php', [
            'msg' => "Relatório '{$reportTitle}' gerado com sucesso.",
            'output' => $output
        ]);
    }

    public function userReport(): void {
        AuthMiddleware::checkAdmin();
        $userId = (int)($_GET['user_id'] ?? 1);

        $user = User::findById($userId);
        if (!$user) {
            echo "Utilizador não encontrado.";
            return;
        }

        $displayName = $user['display_name'];
        $db = Database::getPDO();
        $sql = "SELECT * FROM orders WHERE customer_name = '{$displayName}'";

        try {
            $stmt = $db->query($sql);
            $userOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            die("SQL Error in User Report Query: " . $e->getMessage() . " (Executed: {$sql})");
        }

        require_read_view('views/admin/reports.php', [
            'user' => $user,
            'userOrders' => $userOrders,
            'executedSql' => $sql
        ]);
    }

    public function importXml(): void {
        AuthMiddleware::checkAdmin();

        if (isset($_FILES['xml_file']) && $_FILES['xml_file']['error'] === UPLOAD_ERR_OK) {
            $xmlContent = file_get_contents($_FILES['xml_file']['tmp_name']);
            $result = XmlImportService::importProductsFromXml($xmlContent);

            if (isset($result['error'])) {
                header("Location: /admin/products?error=" . urlencode($result['error']));
                exit;
            }

            header("Location: /admin/products?msg=" . urlencode("Catálogo importado: {$result['imported_count']} produtos adicionados."));
            exit;
        }

        header("Location: /admin/products?error=Ficheiro+XML+inválido");
        exit;
    }

    public function logs(): void {
        $db = Database::getPDO();
        $stmt = $db->query("SELECT * FROM system_logs ORDER BY id DESC LIMIT 50");
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_read_view('views/admin/logs.php', ['logs' => $logs]);
    }
}
