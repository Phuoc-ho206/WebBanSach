<?php

// Bắt đầu session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$username = 'root'; // Mặc định của WAMP
$password = '';     // Mặc định của WAMP không có password
$dbname = 'bookstore';

// Tạo kết nối
$conn = new mysqli($host, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối CSDL thất bại: " . $conn->connect_error);
}

// Set charset UTF-8 để hiển thị tiếng Việt không bị lỗi
$conn->set_charset("utf8mb4");

// --- Các hàm tiện ích dùng chung ---

// Hàm tạo đường dẫn tĩnh cho assets (CSS, JS, Images)
if (!function_exists('asset')) {
    function asset($path) {
        return '/WebBanSach/assets/' . ltrim($path, '/');
    }
}

// Hàm tạo đường dẫn absolute cho các trang
if (!function_exists('url')) {
    function url($path = '') {
        return '/WebBanSach/' . ltrim($path, '/');
    }
}
?>
