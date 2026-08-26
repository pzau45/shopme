<?php

namespace App\Api;

use App\Models\Product;
use App\Middleware\CorsMiddleware;

class ApiProductController {
    public function index(): void {
        CorsMiddleware::handle();
        header('Content-Type: application/json');

        $products = Product::all();
        echo json_encode(['status' => 'success', 'data' => $products]);
    }

    public function detail(string $id): void {
        CorsMiddleware::handle();
        header('Content-Type: application/json');

        $product = Product::findById((int)$id);
        if (!$product) {
            http_response_code(404);
            echo json_encode(['error' => 'Produto não encontrado']);
            return;
        }

        echo json_encode(['status' => 'success', 'data' => $product]);
    }
}
