<?php

namespace App\Api;

use App\Models\User;
use App\Middleware\CorsMiddleware;

class ApiUserController {
    public function detail(string $id): void {
        CorsMiddleware::handle();
        header('Content-Type: application/json');

        $user = User::findById((int)$id);

        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'Utilizador não encontrado']);
            return;
        }

        echo json_encode(['status' => 'success', 'data' => $user]);
    }

    public function update(string $id): void {
        CorsMiddleware::handle();
        header('Content-Type: application/json');

        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true);

        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'JSON inválido']);
            return;
        }

        $success = User::updateMassAssignment((int)$id, $data);

        if ($success) {
            $updatedUser = User::findById((int)$id);
            echo json_encode(['status' => 'success', 'message' => 'Utilizador atualizado', 'data' => $updatedUser]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Falha ao atualizar utilizador']);
        }
    }
}
