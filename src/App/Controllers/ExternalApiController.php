<?php

namespace App\Controllers;

class ExternalApiController {
    public function checkPrice(): void {
        header('Content-Type: application/json');

        $url = $_GET['url'] ?? $_POST['url'] ?? '';

        if (empty($url)) {
            http_response_code(400);
            echo json_encode(['error' => 'Parâmetro url é obrigatório']);
            return;
        }

        $ctx = stream_context_create([
            'http' => [
                'timeout' => 5,
                'user_agent' => 'ShopMe-PriceChecker/1.0'
            ]
        ]);

        $content = @file_get_contents($url, false, $ctx);

        if ($content === false) {
            http_response_code(500);
            echo json_encode(['error' => "Falha ao obter dados do URL: {$url}"]);
            return;
        }

        echo json_encode([
            'status' => 'success',
            'target' => $url,
            'response' => mb_substr($content, 0, 2000)
        ]);
    }
}
