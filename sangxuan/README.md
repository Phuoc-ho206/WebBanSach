# 📚 BookVerse – Hệ Thống Cửa Hàng Sách

**Stack:** PHP 8.1+ OOP | MySQL 8 | Bootstrap 5 | jQuery 3 | HTML5/CSS3

---

## Cấu Trúc Dự Án

```
bookstore/
├── api/
│   └── index.php              ← Router / Front Controller
├── app/
│   ├── Controllers/
│   │   └── Controllers.php    ← BookController, CartController, OrderController, AuthController
│   ├── Core/
│   │   ├── BaseController.php ← JsonResponse + BaseController
│   │   └── Database.php       ← Singleton PDO connection
│   ├── Interfaces/
│   │   └── RepositoryInterface.php  ← DIP: Interface CRUD
│   └── Models/
│       ├── BaseModel.php      ← OCP: Base CRUD implements RepositoryInterface
│       ├── BookModel.php      ← SRP: Book queries
│       └── Models.php         ← Category, Cart, Order, User models
├── config/
│   └── database.php           ← SRP: DB config only
├── database/
│   └── schema.sql             ← DDL + seed data
└── public/
    └── index.html             ← Frontend SPA
```

---

## SOLID Principles Áp Dụng

| Principle | Áp dụng |
|-----------|---------|
| **S** – Single Responsibility | Mỗi class chỉ một nhiệm vụ: `DatabaseConfig` chỉ chứa config, `JsonResponse` chỉ trả JSON, `BookModel` chỉ xử lý books |
| **O** – Open/Closed | `BaseModel` và `BaseController` không sửa – chỉ kế thừa để mở rộng |
| **L** – Liskov Substitution | Mọi Model đều thay thế được `BaseModel` mà không phá vỡ hành vi |
| **I** – Interface Segregation | `RepositoryInterface` chỉ khai báo 5 phương thức CRUD cần thiết |
| **D** – Dependency Inversion | `BaseModel` phụ thuộc `RepositoryInterface` (abstraction), không phụ thuộc concrete |

---

## Cài Đặt

### 1. Database
```sql
mysql -u root -p < database/schema.sql
```

### 2. Cấu hình DB
Sửa file `config/database.php`:
```php
'host'     => 'localhost',
'dbname'   => 'bookstore',
'username' => 'root',
'password' => 'your_password',
```

### 3. Web Server (XAMPP / WAMP)
```
htdocs/bookstore/   ← copy toàn bộ project vào đây
```

Truy cập: `http://localhost/bookstore/public/index.html`

### 4. Apache .htaccess (đặt trong `api/`)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ index.php [QSA,L]
```

---

## API Endpoints

| Method | URL | Mô tả |
|--------|-----|-------|
| GET | `/api/books` | Danh sách sách (có phân trang, lọc category) |
| GET | `/api/books/best-sellers` | Top 8 bán chạy |
| GET | `/api/books/promotions` | Sách đang khuyến mãi |
| GET | `/api/books/search?q=` | Tìm kiếm |
| GET | `/api/books/{id}` | Chi tiết sách |
| GET | `/api/categories` | Danh sách danh mục |
| GET | `/api/cart` | Lấy giỏ hàng |
| POST | `/api/cart` | Thêm sách vào giỏ |
| PUT | `/api/cart` | Cập nhật số lượng |
| DELETE | `/api/cart` | Xóa item |
| POST | `/api/orders/checkout` | Đặt hàng |
| GET | `/api/orders/mine` | Đơn hàng của tôi |
| POST | `/api/auth/login` | Đăng nhập |
| POST | `/api/auth/register` | Đăng ký |
| POST | `/api/auth/logout` | Đăng xuất |
| GET | `/api/auth/me` | Thông tin user hiện tại |

---

## Tài Khoản Mặc Định
- **Admin:** `admin@book.vn` / `password`
- **Customer:** `a@example.com` / `password`
