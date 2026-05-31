<?php
/**
 * Helper Functions
 * Định nghĩa các hàm tiện ích dùng chung trong views
 */

/**
 * Tạo URL tuyệt đối đến file assets (css, js, images)
 * Dùng trong includes/header.php: asset('css/variables.css')
 *
 * @param  string $path  Đường dẫn tương đối từ thư mục assets/
 * @return string        URL đầy đủ
 */
function asset(string $path): string {
    // Tự động phát hiện base URL từ SERVER_NAME + script location
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';

    // Root của project (thư mục chứa /assets)
    // Ví dụ: http://localhost/bookstore  →  /bookstore/assets/css/...
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    // Đi lên đến root project (bỏ /auth, /sangxuan/... nếu có)
    $root = rtrim($scriptDir, '/');

    return "{$protocol}://{$host}{$root}/assets/" . ltrim($path, '/');
}
