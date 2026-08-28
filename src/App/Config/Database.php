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

            $hostsToTry = [$host];
            // If DB_HOST is 'db' but hostname 'db' cannot be resolved in DNS (e.g. running on VPS host outside Docker), try 127.0.0.1
            if ($host === 'db' && gethostbyname('db') === 'db') {
                $hostsToTry[] = '127.0.0.1';
            }

            $lastException = null;
            foreach ($hostsToTry as $currentHost) {
                $dsn = "mysql:host={$currentHost};port={$port};dbname={$db};charset=utf8mb4";
                try {
                    self::$pdo = new PDO($dsn, $user, $pass, [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => true
                    ]);
                    return self::$pdo;
                } catch (PDOException $e) {
                    $lastException = $e;
                }
            }

            if ($lastException !== null) {
                if (isset($_GET['debug']) || true) {
                    die("Database Connection Failure: " . $lastException->getMessage());
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

            $hostsToTry = [$host];
            if ($host === 'db' && gethostbyname('db') === 'db') {
                $hostsToTry[] = '127.0.0.1';
            }

            foreach ($hostsToTry as $currentHost) {
                $conn = @new mysqli($currentHost, $user, $pass, $db, $port);
                if (!$conn->connect_error) {
                    self::$mysqli = $conn;
                    self::$mysqli->set_charset("utf8mb4");
                    return self::$mysqli;
                }
            }

            die("MySQLi Connect Error: " . mysqli_connect_error());
        }
        return self::$mysqli;
    }
}
