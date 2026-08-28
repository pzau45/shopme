<?php

namespace App\Services;

use App\Config\Database;
use PDO;

class OrmService {
    public static function findProductsWhere(array $conditions, string $sort = 'id ASC'): array {
        $db = Database::getPDO();
        $whereClauses = [];

        foreach ($conditions as $column => $value) {
            $whereClauses[] = "{$column} = '{$value}'";
        }

        $sql = "SELECT * FROM products";
        if (!empty($whereClauses)) {
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }

        $sql .= " ORDER BY {$sort}";

        try {
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            if (app_debug()) {
                die("ORM Query Exception: " . $e->getMessage() . " (Executed SQL: {$sql})");
            }
            return [];
        }
    }
}
