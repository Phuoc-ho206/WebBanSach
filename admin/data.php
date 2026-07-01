<?php
// Bắt đầu session và kết nối cơ sở dữ liệu
require_once __DIR__ . '/../config/db.php';

// Kiểm tra quyền truy cập Admin
if (!isset($_SESSION['admin'])) {
    $_SESSION['error'] = 'Vui lòng đăng nhập tài khoản Quản trị viên.';
    header('Location: /WebBanSach/auth/pages/login.php');
    exit;
}

if (strtolower($_SESSION['admin']['role'] ?? '') !== 'admin') {
    $_SESSION['log_toast'] = 'Cảnh báo: Bạn không có quyền truy cập trang quản trị.';
    header('Location: /WebBanSach/trangchu/index.php');
    exit;
}
?>
