<?php

/**
 * Database Configuration
 * Single Responsibility: chỉ chứa cấu hình database
 */
class DatabaseConfig {
    private static array $config = [
        'host'     => 'localhost',
        'dbname'   => 'bookstore',
        'username' => 'root',
        'password' => '',
        'charset'  => 'utf8mb4',
    ];

    public static function get(): array {
        return self::$config;
    }
}
