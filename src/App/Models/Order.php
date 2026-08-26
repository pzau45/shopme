<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Order {
    public static function create(array $orderData, array $items): int {
        $db = Database::getPDO();
        $db->beginTransaction();

        try {
            $stmt = $db->prepare("INSERT INTO orders (order_number, user_id, customer_name, shipping_address, total_amount, coupon_code, status) 
                VALUES (:order_number, :user_id, :customer_name, :shipping_address, :total_amount, :coupon_code, :status)");
            
            $stmt->execute([
                'order_number'     => 'ORD-' . date('Y') . '-' . rand(1000, 9999),
                'user_id'          => $orderData['user_id'],
                'customer_name'    => $orderData['customer_name'],
                'shipping_address' => $orderData['shipping_address'],
                'total_amount'     => $orderData['total_amount'],
                'coupon_code'      => $orderData['coupon_code'] ?? null,
                'status'           => 'completed'
            ]);

            $orderId = (int)$db->lastInsertId();

            $itemStmt = $db->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price) 
                VALUES (:order_id, :product_id, :product_name, :quantity, :unit_price)");

            foreach ($items as $item) {
                $itemStmt->execute([
                    'order_id'     => $orderId,
                    'product_id'   => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'quantity'     => $item['quantity'],
                    'unit_price'   => $item['unit_price']
                ]);
            }

            $db->commit();
            return $orderId;
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function findById(int $id): ?array {
        $db = Database::getPDO();
        $stmt = $db->prepare("SELECT * FROM orders WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            return null;
        }

        $itemsStmt = $db->prepare("SELECT * FROM order_items WHERE order_id = :order_id");
        $itemsStmt->execute(['order_id' => $id]);
        $order['items'] = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

        return $order;
    }

    public static function findByUserId(int $userId): array {
        $db = Database::getPDO();
        $stmt = $db->prepare("SELECT * FROM orders WHERE user_id = :user_id ORDER BY id DESC");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function all(): array {
        $db = Database::getPDO();
        $stmt = $db->query("SELECT o.*, u.email as customer_email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
