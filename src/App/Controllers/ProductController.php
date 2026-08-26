<?php

namespace App\Controllers;

use App\Models\Product;
use App\Config\Database;
use PDO;

class ProductController {
    public function index(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $q      = $_GET['q'] ?? '';
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = max(1, (int)($_GET['limit'] ?? 12));
        $offset = ($page - 1) * $limit;

        if (!empty($q)) {
            $products   = Product::searchRawSQL($q, $limit, $offset);
            $totalItems = Product::countSearchRawSQL($q);
        } else {
            $products   = Product::all($limit, $offset);
            $totalItems = Product::countAll();
        }

        $totalPages = max(1, (int)ceil($totalItems / $limit));

        $isJson = (isset($_GET['json']) && $_GET['json'] == 1) || 
                  (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'));

        if ($isJson) {
            header('Content-Type: application/json');
            echo json_encode([
                'status'      => 'success',
                'page'        => $page,
                'limit'       => $limit,
                'total_items' => $totalItems,
                'total_pages' => $totalPages,
                'count'       => count($products),
                'has_more'    => $page < $totalPages,
                'data'        => $products
            ]);
            return;
        }

        require_read_view('views/products/index.php', [
            'products'    => $products,
            'searchQuery' => $q,
            'page'        => $page,
            'limit'       => $limit,
            'totalItems'  => $totalItems,
            'totalPages'  => $totalPages,
            'hasMore'     => $page < $totalPages
        ]);
    }

    public function detail(string $id): void {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $product = Product::findById((int)$id);
        if (!$product) {
            http_response_code(404);
            echo "<h1>404 Product Not Found</h1>";
            return;
        }

        $db = Database::getPDO();
        $stmt = $db->prepare("SELECT * FROM reviews WHERE product_id = :pid ORDER BY id DESC");
        $stmt->execute(['pid' => $id]);
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_read_view('views/products/detail.php', [
            'product' => $product,
            'reviews' => $reviews
        ]);
    }

    public function addReview(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $productId = (int)($_POST['product_id'] ?? 0);
        $comment   = $_POST['comment'] ?? '';
        $rating    = (int)($_POST['rating'] ?? 5);

        $user = $_SESSION['user'] ?? ['id' => 0, 'display_name' => $_POST['author_name'] ?? 'Visitante'];

        if ($productId > 0 && !empty($comment)) {
            $db = Database::getPDO();
            $stmt = $db->prepare("INSERT INTO reviews (product_id, user_id, author_name, comment, rating) VALUES (:pid, :uid, :author, :comment, :rating)");
            $stmt->execute([
                'pid'     => $productId,
                'uid'     => $user['id'],
                'author'  => $user['display_name'],
                'comment' => $comment,
                'rating'  => $rating
            ]);
        }

        header("Location: /products/" . $productId);
        exit;
    }
}
