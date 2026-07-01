<?php
header('Content-Type: text/plain; charset=utf-8');

echo "--- TIẾN HÀNH TỰ ĐỘNG CẬP NHẬT MẬT KHẨU ADMIN ---\n\n";

// 1. Kết nối DB
require_once '../config/db.php';
$conn = $conn ?? $GLOBALS['conn'] ?? null;

if (!$conn) {
    die("LỖI: Không thể kết nối cơ sở dữ liệu.\n");
}
echo "1. Kết nối cơ sở dữ liệu thành công.\n";

// 2. Tạo mã hash chuẩn cho mật khẩu '12345678'
$newPasswordPlain = '12345678';
$newPasswordHash = password_hash($newPasswordPlain, PASSWORD_DEFAULT);
echo "2. Đã tạo chuỗi mã hóa chuẩn cho mật khẩu '{$newPasswordPlain}':\n   -> {$newPasswordHash}\n";

// 3. Cập nhật vào database cho admin@bookstore.vn
$email = 'admin@example.com';
$updateSql = "UPDATE user SET Password = ? WHERE Email = ?";
$stmt = $conn->prepare($updateSql);
$stmt->bind_param('ss', $newPasswordHash, $email);

if ($stmt->execute()) {
    echo "3. Cập nhật database THÀNH CÔNG!\n";
    echo "   - Tài khoản: {$email}\n";
    echo "   - Mật khẩu mới của bạn là: {$newPasswordPlain}\n";
} else {
    echo "3. LỖI: Không thể cập nhật database: " . $stmt->error . "\n";
}
$stmt->close();

echo "\nBây giờ bạn hãy quay lại trang đăng nhập và đăng nhập với:\n";
echo "- Email: {$email}\n";
echo "- Mật khẩu: {$newPasswordPlain}\n";
echo "\n--- KẾT THÚC ---";
