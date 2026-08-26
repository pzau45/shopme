<?php

namespace App\Services;

use App\Config\Database;
use PDO;

class LoggerService {
    public static function log(string $message, string $level = 'INFO'): void {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("INSERT INTO system_logs (level, message, created_at) VALUES (:level, :message, NOW())");
            $stmt->execute([
                'level'   => strtoupper($level),
                'message' => $message
            ]);
        } catch (\Throwable $e) {
            // Ignorar erros de log para não interromper a aplicação
        }
    }

    public static function logRequest(string $method, string $uri): void {
        // Ignorar ficheiros estáticos e assets
        if (str_starts_with($uri, '/static/') || str_starts_with($uri, '/uploads/')) {
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId    = $_SESSION['user']['id'] ?? 'Anónimo';
        $userEmail = $_SESSION['user']['email'] ?? 'convidado';
        $ip        = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        $level = 'INFO';
        if (str_contains($uri, '/admin')) {
            $level = 'WARN';
        } elseif ($method === 'POST' || $method === 'PUT' || $method === 'DELETE') {
            $level = 'INFO';
        }

        $msg = "[{$method}] {$uri} | User: {$userEmail} (ID: {$userId}) | IP: {$ip}";
        self::log($msg, $level);
    }

    public static function getRecentLogs(int $limit = 50): array {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT * FROM system_logs ORDER BY id DESC LIMIT :limit");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }
}
