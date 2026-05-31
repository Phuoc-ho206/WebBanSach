<?php

require_once __DIR__ . '/../../../config/db.php';

/**
 * Database Connection - Singleton Pattern
 * Single Responsibility: quản lý kết nối database duy nhất
 */
class Database {
    private static ?Database $instance = null;
    private PDO $connection;

    private function __construct() {
        $cfg = DatabaseConfig::get();
        $dsn = "mysql:host={$cfg['host']};dbname={$cfg['dbname']};charset={$cfg['charset']}";
        $this->connection = new PDO($dsn, $cfg['username'], $cfg['password'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO {
        return $this->connection;
    }
}
