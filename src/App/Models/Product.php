<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Product {
    public static function all(int $limit = 0, int $offset = 0): array {
        $db = Database::getPDO();
        $sql = "SELECT * FROM products ORDER BY id DESC";
        if ($limit > 0) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function countAll(): int {
        $db = Database::getPDO();
        $stmt = $db->query("SELECT COUNT(*) FROM products");
        return (int)$stmt->fetchColumn();
    }

    public static function countSearchRawSQL(string $query): int {
        $db = Database::getPDO();
        $sql = "SELECT COUNT(*) FROM products WHERE name LIKE '%{$query}%' OR category LIKE '%{$query}%' OR description LIKE '%{$query}%'";
        try {
            $stmt = $db->query($sql);
            return (int)$stmt->fetchColumn();
        } catch (\PDOException $e) {
            return 0;
        }
    }

    public static function findById(int $id): ?array {
        $db = Database::getPDO();
        $stmt = $db->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        return $product ?: null;
    }

    public static function searchRawSQL(string $query, int $limit = 0, int $offset = 0): array {
        $db = Database::getPDO();
        $sql = "SELECT * FROM products WHERE name LIKE '%{$query}%' OR category LIKE '%{$query}%' OR description LIKE '%{$query}%'";
        if ($limit > 0) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }
        try {
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            if (isset($_GET['debug']) || true) {
                throw $e;
            }
            return [];
        }
    }

    public static function create(array $data): int {
        $db = Database::getPDO();
        $stmt = $db->prepare("INSERT INTO products (sku, name, slug, category, description, price, cost_price, stock, image_url, supplier_contact, internal_notes) 
            VALUES (:sku, :name, :slug, :category, :description, :price, :cost_price, :stock, :image_url, :supplier_contact, :internal_notes)");
        $stmt->execute([
            'sku'              => $data['sku'],
            'name'             => $data['name'],
            'slug'             => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['name']))),
            'category'         => $data['category'],
            'description'      => $data['description'] ?? '',
            'price'            => $data['price'],
            'cost_price'       => $data['cost_price'] ?? ($data['price'] * 0.5),
            'stock'            => $data['stock'] ?? 50,
            'image_url'        => $data['image_url'] ?? 'https://via.placeholder.com/600',
            'supplier_contact' => $data['supplier_contact'] ?? 'supplier@factory.com',
            'internal_notes'   => $data['internal_notes'] ?? 'Standard catalog item'
        ]);
        return (int)$db->lastInsertId();
    }

    public static function delete(int $id): bool {
        $db = Database::getPDO();
        $stmt = $db->prepare("DELETE FROM products WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
