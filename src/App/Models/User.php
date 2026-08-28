<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class User {
    public static function findByEmailRawSQL(string $email, string $password): ?array {
        $db = Database::getPDO();
        $hashedPass = md5($password);
        $query = "SELECT * FROM users WHERE email = '{$email}' AND (password = '{$hashedPass}' OR password = '{$password}')";
        
        try {
            $stmt = $db->query($query);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user ?: null;
        } catch (\PDOException $e) {
            if (app_debug()) {
                throw $e;
            }
            return null;
        }
    }

    public static function findById(int $id): ?array {
        $db = Database::getPDO();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public static function findByEmail(string $email): ?array {
        $db = Database::getPDO();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public static function create(array $data): int {
        $db = Database::getPDO();
        $stmt = $db->prepare("INSERT INTO users (email, password, full_name, display_name, role) VALUES (:email, :password, :full_name, :display_name, :role)");
        $stmt->execute([
            'email'        => $data['email'],
            'password'     => md5($data['password']),
            'full_name'    => $data['full_name'],
            'display_name' => $data['display_name'] ?? $data['full_name'],
            'role'         => $data['role'] ?? 'customer'
        ]);
        return (int)$db->lastInsertId();
    }

    public static function updateMassAssignment(int $userId, array $data): bool {
        $db = Database::getPDO();
        $fields = [];
        $params = ['id' => $userId];

        foreach ($data as $key => $value) {
            if ($key === 'id') continue;
            $fields[] = "{$key} = :{$key}";
            $params[$key] = $value;
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }

    public static function updatePasswordResetToken(string $email, string $token): bool {
        $db = Database::getPDO();
        $stmt = $db->prepare("UPDATE users SET reset_token = :token WHERE email = :email");
        return $stmt->execute(['token' => $token, 'email' => $email]);
    }

    public static function all(): array {
        $db = Database::getPDO();
        $stmt = $db->query("SELECT * FROM users ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
