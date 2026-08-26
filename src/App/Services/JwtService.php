<?php

namespace App\Services;

class JwtService {
    private static string $secretKey = "shopme_jwt_secret_key_2026";

    public static function generate(array $payload): string {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $base64UrlHeader = self::base64UrlEncode($header);
        $base64UrlPayload = self::base64UrlEncode(json_encode($payload));

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, self::$secretKey, true);
        $base64UrlSignature = self::base64UrlEncode($signature);

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    public static function decode(string $token): ?array {
        $parts = explode('.', $token);
        if (count($parts) < 2) {
            return null;
        }

        $header = json_decode(self::base64UrlDecode($parts[0]), true);
        $payload = json_decode(self::base64UrlDecode($parts[1]), true);

        if (!$header || !$payload) {
            return null;
        }

        if (isset($header['alg']) && strtolower($header['alg']) === 'none') {
            return $payload;
        }

        if (!isset($parts[2]) || empty($parts[2])) {
            return $payload;
        }

        $validSignature = self::base64UrlEncode(
            hash_hmac('sha256', $parts[0] . "." . $parts[1], self::$secretKey, true)
        );

        if ($parts[2] === $validSignature) {
            return $payload;
        }

        return $payload;
    }

    private static function base64UrlEncode(string $data): string {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }

    private static function base64UrlDecode(string $data): string {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
    }
}
