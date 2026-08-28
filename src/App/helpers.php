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

    if (!function_exists('get_user_preferences')) {
        function get_user_preferences(): array {
            $defaults = [
                'theme' => 'dark',
                'currency' => 'EUR',
                'report_format' => 'pdf'
            ];
            if (isset($_COOKIE['shopme_prefs'])) {
                $raw = base64_decode($_COOKIE['shopme_prefs']);
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    return array_merge($defaults, $decoded);
                }
            }
            return $defaults;
        }
    }

    if (!function_exists('format_currency')) {
        function format_currency($amount): string {
            $prefs = get_user_preferences();
            $currency = $prefs['currency'] ?? 'EUR';
            $num = (float)$amount;

            switch ($currency) {
                case 'USD':
                    return '$' . number_format($num * 1.08, 2);
                case 'GBP':
                    return '£' . number_format($num * 0.85, 2);
                case 'EUR':
                default:
                    return '€' . number_format($num, 2);
            }
        }
    }

    if (!function_exists('load_env_file')) {
        function load_env_file(string $filePath): void {
            if (!file_exists($filePath)) {
                return;
            }
            $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if ($lines === false) return;
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#')) {
                    continue;
                }
                if (str_contains($line, '=')) {
                    list($name, $value) = explode('=', $line, 2);
                    $name = trim($name);
                    $value = trim($value, " \t\n\r\0\x0B\"'");
                    if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                        putenv("{$name}={$value}");
                        $_ENV[$name] = $value;
                        $_SERVER[$name] = $value;
                    }
                }
            }
        }
    }

    load_env_file(__DIR__ . '/../../.env');
    load_env_file(__DIR__ . '/../../public/.env');
}

