# Hướng dẫn Setup Database cho Auth System

## 📋 Yêu cầu

- MySQL/MariaDB đã được cài đặt
- Database `bookstore` đã tồn tại
- User có quyền CREATE TABLE

## 🗄️ Các bảng cần tạo

### 1. Bảng `users` - Lưu thông tin người dùng

```sql
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) DEFAULT '',
  `address` TEXT DEFAULT '',
  `role` ENUM('customer', 'admin') DEFAULT 'customer',
  `facebook_id` VARCHAR(100) DEFAULT NULL,
  `google_id` VARCHAR(100) DEFAULT NULL,
  `last_login` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_email` (`email`),
  INDEX `idx_username` (`username`),
  INDEX `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2. Bảng `password_reset_tokens` - Lưu OTP reset password

```sql
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(100) NOT NULL,
  `token` VARCHAR(10) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `used` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_email` (`email`),
  INDEX `idx_token` (`token`),
  INDEX `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 3. Bảng `guest_users` - Lưu thông tin guest (chưa đăng nhập)

```sql
CREATE TABLE IF NOT EXISTS `guest_users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `session_id` VARCHAR(100) NOT NULL UNIQUE,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_session` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 4. Bảng `cart` - Giỏ hàng (đã có sẵn, chỉ kiểm tra)

```sql
-- Kiểm tra bảng cart đã tồn tại chưa
-- Nếu chưa có, tạo mới:
CREATE TABLE IF NOT EXISTS `cart` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_user_product` (`user_id`, `product_id`),
  INDEX `idx_user` (`user_id`),
  INDEX `idx_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## 📝 Cách thực hiện

### Cách 1: Sử dụng phpMyAdmin

1. Mở phpMyAdmin (http://localhost/phpmyadmin)
2. Chọn database `bookstore`
3. Vào tab "SQL"
4. Copy và paste từng đoạn SQL ở trên
5. Click "Go" để thực thi

### Cách 2: Sử dụng MySQL Command Line

```bash
# Đăng nhập MySQL
mysql -u root -p

# Chọn database
USE bookstore;

# Chạy các lệnh SQL ở trên
```

### Cách 3: Tạo file SQL và import

Tạo file `auth_database.sql`:

```sql
-- auth_database.sql
-- Chạy file này để tạo toàn bộ cấu trúc database

-- Tạo bảng users
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) DEFAULT '',
  `address` TEXT DEFAULT '',
  `role` ENUM('customer', 'admin') DEFAULT 'customer',
  `facebook_id` VARCHAR(100) DEFAULT NULL,
  `google_id` VARCHAR(100) DEFAULT NULL,
  `last_login` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_email` (`email`),
  INDEX `idx_username` (`username`),
  INDEX `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(100) NOT NULL,
  `token` VARCHAR(10) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `used` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_email` (`email`),
  INDEX `idx_token` (`token`),
  INDEX `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng guest_users
CREATE TABLE IF NOT EXISTS `guest_users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `session_id` VARCHAR(100) NOT NULL UNIQUE,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_session` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

Import file SQL:

```bash
mysql -u root -p bookstore < auth_database.sql
```

## 👤 Tạo tài khoản Admin mặc định

Sau khi tạo bảng, tạo tài khoản admin đầu tiên:

```sql
INSERT INTO users (username, email, password, full_name, phone, address, role) 
VALUES (
  'admin',
  'admin@example.com',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
  'Administrator',
  '0900000000',
  'Hà Nội',
  'admin'
);
```

**Lưu ý:** Password hash trên là của password "password". Bạn có thể tạo hash mới bằng PHP:

```php
<?php
echo password_hash('your_password', PASSWORD_DEFAULT);
?>
```

## ✅ Kiểm tra

Sau khi setup xong, kiểm tra:

```sql
-- Kiểm tra bảng users
SELECT * FROM users;

-- Kiểm tra bảng password_reset_tokens
SELECT * FROM password_reset_tokens;

-- Kiểm tra bảng guest_users
SELECT * FROM guest_users;
```

## 🔧 Troubleshooting

### Lỗi "Table already exists"
- Bảng đã tồn tại, không cần tạo lại
- Hoặc dùng `DROP TABLE IF EXISTS` trước khi CREATE

### Lỗi "Access denied"
- Kiểm tra user/password MySQL trong `config/db.php`
- Đảm bảo user có quyền CREATE TABLE

### Lỗi "Foreign key constraint fails"
- Kiểm tra thứ tự tạo bảng (cart phải tạo sau users)
- Hoặc tắt foreign key checks: `SET FOREIGN_KEY_CHECKS=0;`

## 📌 Lưu ý quan trọng

1. **Password**: Luôn lưu password dạng hash, không bao giờ lưu plain text
2. **Email**: Phải unique, không được trùng
3. **Username**: Phải unique, không được trùng
4. **OTP**: Token có hiệu lực 30 phút, sau đó tự động hết hạn
5. **Session**: Luôn gọi `session_start()` trước khi dùng $_SESSION

## 🚀 Sau khi setup

1. Truy cập http://localhost/WebBanSach/auth/pages/register.php
2. Đăng ký tài khoản mới
3. Hoặc đăng nhập với tài khoản admin đã tạo
4. Test chức năng quên mật khẩu

## 📞 Hỗ trợ

Nếu gặp vấn đề, kiểm tra:
- PHP error log
- MySQL error log
- File `config/db.php` có đúng thông tin kết nối không