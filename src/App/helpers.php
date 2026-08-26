<?php

namespace App\Controllers {
    if (!function_exists('App\Controllers\require_read_view')) {
        function require_read_view(string $viewPath, array $data = []): void {
            extract($data);
            require_once __DIR__ . '/../../' . $viewPath;
        }
    }
}

namespace {
    if (!function_exists('require_read_view')) {
        function require_read_view(string $viewPath, array $data = []): void {
            \App\Controllers\require_read_view($viewPath, $data);
        }
    }

    if (!function_exists('base_url')) {
        function base_url(string $path = ''): string {
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            return $scheme . '://' . $host . '/' . ltrim($path, '/');
        }
    }
}
