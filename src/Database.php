<?php
namespace App;

use PDO;
use PDOException;

/**
 * Thin wrapper around PDO that returns a single shared connection.
 * Always uses prepared statements via PDO directly elsewhere — this class
 * just owns the connection.
 */
class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo === null) {
            $config = require __DIR__ . '/../config/config.php';
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=utf8mb4',
                $config['db_host'],
                $config['db_name']
            );

            try {
                self::$pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                // In production we'd log and show a generic page.
                http_response_code(500);
                die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
            }
        }
        return self::$pdo;
    }
}
