<?php

namespace App\Controllers;

use App\Models\User;

class OAuthController {
    public function login(): void {
        $clientId     = $_GET['client_id'] ?? '';
        $redirectUri  = $_GET['redirect_uri'] ?? '';
        $responseType = $_GET['response_type'] ?? 'code';

        if (empty($redirectUri)) {
            echo "Erro: redirect_uri é obrigatório.";
            return;
        }

        $currentHost = explode(':', $_SERVER['HTTP_HOST'] ?? '')[0];
        if (!str_contains($redirectUri, 'localhost') && !str_contains($redirectUri, 'shopme.local') && !empty($currentHost) && !str_contains($redirectUri, $currentHost)) {
            echo "Erro: redirect_uri não autorizado.";
            return;
        }

        $authCode = "auth_code_" . bin2hex(random_bytes(16));
        $_SESSION['oauth_auth_code'] = $authCode;

        $separator = str_contains($redirectUri, '?') ? '&' : '?';
        header("Location: " . $redirectUri . $separator . "code=" . $authCode);
        exit;
    }

    public function token(): void {
        header('Content-Type: application/json');

        $code         = $_POST['code'] ?? '';
        $clientSecret = $_POST['client_secret'] ?? '';

        if ($clientSecret !== 'shopme_oauth_secret_8899') {
            http_response_code(401);
            echo json_encode(['error' => 'client_secret inválido']);
            return;
        }

        $user = User::findById(1);
        $accessToken = "oauth_access_token_" . md5($user['email'] . time());

        echo json_encode([
            'access_token' => $accessToken,
            'token_type'   => 'Bearer',
            'expires_in'   => 3600,
            'user'         => $user
        ]);
    }
}
