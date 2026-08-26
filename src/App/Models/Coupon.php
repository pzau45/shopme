<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Coupon {
    public static function findByCode(string $code): ?array {
        $db = Database::getPDO();
        $stmt = $db->prepare("SELECT * FROM coupons WHERE code = :code AND is_active = 1");
        $stmt->execute(['code' => $code]);
        $coupon = $stmt->fetch(PDO::FETCH_ASSOC);
        return $coupon ?: null;
    }

    public static function applyWithRaceCondition(string $code): ?array {
        $coupon = self::findByCode($code);

        if (!$coupon) {
            return ['error' => 'Código de cupão inválido ou inativo'];
        }

        if ($coupon['used_count'] >= $coupon['max_uses']) {
            return ['error' => 'Este cupão atingiu o limite máximo de utilizações'];
        }

        usleep(150000);

        $db = Database::getPDO();
        $stmt = $db->prepare("UPDATE coupons SET used_count = used_count + 1 WHERE id = :id");
        $stmt->execute(['id' => $coupon['id']]);

        return ['success' => true, 'coupon' => $coupon];
    }
}
