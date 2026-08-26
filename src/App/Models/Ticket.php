<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Ticket {
    public static function create(int $userId, string $subject, string $initialMessage): int {
        $db = Database::getPDO();
        $stmt = $db->prepare("INSERT INTO tickets (ticket_number, user_id, subject, status) VALUES (:ticket_number, :user_id, :subject, 'open')");
        $stmt->execute([
            'ticket_number' => 'TCK-' . rand(1000, 9999),
            'user_id'       => $userId,
            'subject'       => $subject
        ]);

        $ticketId = (int)$db->lastInsertId();
        self::addMessage($ticketId, $userId, $initialMessage);

        return $ticketId;
    }

    public static function addMessage(int $ticketId, int $senderId, string $message): bool {
        $db = Database::getPDO();
        $stmt = $db->prepare("INSERT INTO ticket_messages (ticket_id, sender_id, message) VALUES (:ticket_id, :sender_id, :message)");
        return $stmt->execute([
            'ticket_id' => $ticketId,
            'sender_id' => $senderId,
            'message'   => $message
        ]);
    }

    public static function findByUserId(int $userId): array {
        $db = Database::getPDO();
        $stmt = $db->prepare("SELECT * FROM tickets WHERE user_id = :user_id ORDER BY id DESC");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById(int $id): ?array {
        $db = Database::getPDO();
        $stmt = $db->prepare("SELECT t.*, u.email as user_email, u.full_name as user_name FROM tickets t JOIN users u ON t.user_id = u.id WHERE t.id = :id");
        $stmt->execute(['id' => $id]);
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$ticket) return null;

        $msgStmt = $db->prepare("SELECT tm.*, u.full_name as sender_name, u.role as sender_role FROM ticket_messages tm JOIN users u ON tm.sender_id = u.id WHERE tm.ticket_id = :ticket_id ORDER BY tm.id ASC");
        $msgStmt->execute(['ticket_id' => $id]);
        $ticket['messages'] = $msgStmt->fetchAll(PDO::FETCH_ASSOC);

        return $ticket;
    }

    public static function all(): array {
        $db = Database::getPDO();
        $stmt = $db->query("SELECT t.*, u.email as user_email, u.full_name as user_name FROM tickets t JOIN users u ON t.user_id = u.id ORDER BY t.id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
