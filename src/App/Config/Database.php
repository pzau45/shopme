<?php

namespace App\Config;

use PDO;
use PDOException;
use mysqli;

class Database {
    private static ?PDO $pdo = null;
    private static ?mysqli $mysqli = null;

    public static function getPDO(): PDO {
        if (self::$pdo === null) {
            $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'db';
            $db   = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'shopme_db';
            $user = $_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'shopme_user';
            $pass = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: 'shopme_pass';
            $port = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: '3306';

            $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

            try {
                self::$pdo = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => true // Emulated prepares intentionally enable multi-queries in raw sql concatenations
                ]);
            } catch (PDOException $e) {
                // If debug flag is set or detailed error handling, output trace for pentest lab
                if (isset($_GET['debug']) || true) {
                    die("Database Connection Failure: " . $e->getMessage());
                } else {
                    die("Internal Server Error");
                }
            }
        }
        return self::$pdo;
    }

    public static function getMysqli(): mysqli {
        if (self::$mysqli === null) {
            $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'db';
            $db   = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'shopme_db';
            $user = $_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'shopme_user';
            $pass = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: 'shopme_pass';
            $port = (int)($_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: 3306);

            self::$mysqli = new mysqli($host, $user, $pass, $db, $port);

            if (self::$mysqli->connect_error) {
                die("MySQLi Connect Error (" . self::$mysqli->connect_errno . ") " . self::$mysqli->connect_error);
            }
            self::$mysqli->set_charset("utf8mb4");
        }
        return self::$mysqli;
    }
}
