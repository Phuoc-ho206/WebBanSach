# Phân tích Database & Điểm cần thêm/sửa

## 📊 Database hiện tại

### Bảng `user` (QUAN TRỌNG - cần sửa)

**Cấu trúc hiện tại:**
```sql
- CustomerID (PK)
- RoleID (FK → role)
- LastName
- FirstName
- Email (UNIQUE)
- Password
- Phone
```

**Vấn đề phát hiện:**

### ❌ VẤN ĐỀ 1: Thiếu trường quan trọng
Bảng `user` đang thiếu các trường cần thiết cho auth system:

| Trường cần thêm | Lý do | Kiểu dữ liệu |
|----------------|-------|--------------|
| `username` | Để login bằng username | VARCHAR(50), UNIQUE |
| `full_name` | Hiển thị tên đầy đủ (hiện tại chỉ có LastName + FirstName) | VARCHAR(100) |
| `address` | Lưu địa chỉ giao hàng | TEXT |
| `facebook_id` | Hỗ trợ Facebook OAuth | VARCHAR(100), NULL |
| `google_id` | Hỗ trợ Google OAuth | VARCHAR(100), NULL |
| `last_login` | Theo dõi lần đăng nhập cuối | DATETIME, NULL |
| `created_at` | Thời gian tạo tài khoản | DATETIME, DEFAULT CURRENT_TIMESTAMP |
| `updated_at` | Thời gian cập nhật cuối | DATETIME, ON UPDATE CURRENT_TIMESTAMP |

### ❌ VẤN ĐỀ 2: Thiếu bảng cho Auth System

Cần thêm 2 bảng mới:

#### 1. Bảng `password_reset_tokens` (Quên mật khẩu)
```sql
- id (PK)
- email (INDEX)
- token (4-6 chữ số OTP)
- expires_at (30 phút)
- used (boolean)
- created_at
```

#### 2. Bảng `guest_users` (Guest tracking)
```sql
- id (PK)
- session_id (UNIQUE)
- created_at
- updated_at
```

### ❌ VẤN ĐỀ 3: Tên bảng không khớp

Auth system đang dùng tên bảng `users` (plural) nhưng database có bảng `user` (singular).

**Giải pháp:** Sửa code trong models để dùng đúng tên bảng `user`

### ❌ VẤN ĐỀ 4: Password hiện tại không phải hash

Dữ liệu mẫu có:
```sql
'hashed_pass_1', 'hashed_pass_2', ... (KHÔNG PHẢI HASH THẬT)
```

**Cần:** Đổi sang password_hash() thực tế

### ❌ VẤN ĐỀ 5: Cart system cần điều chỉnh

Bảng `cart` hiện tại:
```sql
- CartID (PK)
- CustomerID (FK → user)
- CreatedDate
- Status
```

**Auth system cần:**
- user_id trực tiếp (không phải CustomerID)
- product_id + quantity trực tiếp
- Không cần CartID phức tạp

**Giải pháp:** Giữ nguyên cart cũ, thêm logic merge trong Guest model

---

## ✅ ĐIỂM TỐT (Không cần sửa)

### ✅ Bảng `role` đã đầy đủ
- Có đủ các role: Admin, Customer, Manager, Staff, Shipper, Accountant, Editor, Marketing, Support, Guest
- Có thể dùng trực tiếp

### ✅ Các bảng khác ổn
- `address` - Đã có đầy đủ
- `order`, `order_detail` - OK
- `product`, `category` - OK
- `payment`, `delivery` - OK
- `review` - OK
- `voucher`, `promotion` - OK

---

## 🔧 CẦN LÀM GÌ?

### PHƯƠNG ÁN 1: Sửa database để khớp với Auth System (Đề xuất)

#### Bước 1: Sửa bảng `user`
```sql
-- Thêm các trường còn thiếu
ALTER TABLE `user`
ADD COLUMN `username` VARCHAR(50) UNIQUE AFTER `CustomerID`,
ADD COLUMN `full_name` VARCHAR(100) GENERATED ALWAYS AS (CONCAT(FirstName, ' ', LastName)) STORED,
ADD COLUMN `address` TEXT DEFAULT '' AFTER `Phone`,
ADD COLUMN `facebook_id` VARCHAR(100) DEFAULT NULL AFTER `address`,
ADD COLUMN `google_id` VARCHAR(100) DEFAULT NULL AFTER `facebook_id`,
ADD COLUMN `last_login` DATETIME DEFAULT NULL AFTER `google_id`,
ADD COLUMN `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP AFTER `last_login`,
ADD COLUMN `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- Thêm index cho username
CREATE UNIQUE INDEX idx_username ON `user` (username);
```

#### Bước 2: Tạo bảng `password_reset_tokens`
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

#### Bước 3: Tạo bảng `guest_users`
```sql
CREATE TABLE IF NOT EXISTS `guest_users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `session_id` VARCHAR(100) NOT NULL UNIQUE,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_session` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### Bước 4: Cập nhật password thành hash thật
```sql
-- Tạo hash mới cho password
UPDATE `user` SET `Password` = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE `CustomerID` = 1; -- admin@bookstore.vn -> password: password

-- Lưu ý: Cần tạo hash cho từng user
```

#### Bước 5: Sửa code trong Models
```php
// auth/models/Customer.php
private $table = 'user'; // Đổi từ 'users' sang 'user'

// Cập nhật các query để phù hợp với cấu trúc bảng user
// - Dùng CustomerID thay vì id
// - Dùng FirstName + LastName thay vì full_name (hoặc dùng generated column)
// - Thêm username vào các query
```

### PHƯƠNG ÁN 2: Giữ nguyên database, sửa code Auth System

#### Bước 1: Sửa Models
```php
// auth/models/Customer.php
private $table = 'user'; // Đổi tên bảng

// Cập nhật tất cả queries:
// - SELECT id → SELECT CustomerID
// - WHERE id = ? → WHERE CustomerID = ?
// - username (thêm trường mới)
// - full_name = CONCAT(FirstName, ' ', LastName)
```

#### Bước 2: Thêm các bảng mới
- Tạo `password_reset_tokens`
- Tạo `guest_users`

#### Bước 3: Cập nhật AuthController
```php
// Sửa để dùng CustomerID thay vì id
// Sửa query phù hợp với cấu trúc bảng user
```

---

## 📋 KHUYẾN NGHỊ

### Nên làm theo PHƯƠNG ÁN 1 vì:

1. **Tên bảng `users` (plural) là convention** trong Laravel, Symfony, và nhiều framework khác
2. **Dễ maintain** hơn trong tương lai
3. **Auth system đã xây dựng** dùng `users`, nên đổi database cho khớp là tốt nhất
4. **Thêm các trường cần thiết** cho OAuth, remember me, v.v.

### Tuy nhiên, nếu không muốn đổi tên bảng:

Dùng PHƯƠNG ÁN 2 - sửa code Auth System để phù hợp với database hiện tại.

---

## 🚀 NEXT STEPS

1. **Quyết định:** Chọn phương án 1 hay 2
2. **Backup database** trước khi sửa
3. **Chạy SQL migration** để thêm bảng và sửa bảng `user`
4. **Cập nhật Models** trong auth/models/ để phù hợp
5. **Test lại toàn bộ auth flow**

Bạn muốn tôi thực hiện theo phương án nào?