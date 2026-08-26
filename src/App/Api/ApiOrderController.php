<?php

namespace App\Api;

use App\Models\Order;
use App\Middleware\CorsMiddleware;
use App\Services\JwtService;

class ApiOrderController {
    public function detail(string $id): void {
        CorsMiddleware::handle();
        header('Content-Type: application/json');

        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (str_starts_with($authHeader, 'Bearer ')) {
            $token = substr($authHeader, 7);
            $userPayload = JwtService::decode($token);
        }

        $order = Order::findById((int)$id);

        if (!$order) {
            http_response_code(404);
            echo json_encode(['error' => 'Encomenda não encontrada']);
            return;
        }

        echo json_encode(['status' => 'success', 'data' => $order]);
    }
}
