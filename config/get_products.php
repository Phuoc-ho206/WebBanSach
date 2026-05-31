<?php
// Dùng chung DatabaseConfig từ db.php ở cùng thư mục
require_once __DIR__ . '/db.php';

// Kết nối PDO (nhất quán với phần còn lại của dự án)
$cfg  = DatabaseConfig::get();
$dsn  = "mysql:host={$cfg['host']};dbname={$cfg['dbname']};charset={$cfg['charset']}";
$pdo  = new PDO($dsn, $cfg['username'], $cfg['password'], [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

// Lấy danh sách sách kèm tên danh mục và ảnh chính
// (tên bảng theo schema mới: bookstore_books, bookstore_categories, bookstore_book_images)
$sql = "
    SELECT
        b.book_id       AS ProductID,
        b.title         AS Name,
        b.price         AS Price,
        b.stock         AS Stock,
        c.category_name AS CategoryName,
        (SELECT image_url
         FROM bookstore_book_images bi
         WHERE bi.book_id = b.book_id
         LIMIT 1)       AS ImageURL
    FROM bookstore_books b
    LEFT JOIN bookstore_categories c ON b.category_id = c.category_id
    ORDER BY b.created_at DESC
";

$stmt     = $pdo->query($sql);
$products = $stmt->fetchAll();

header('Content-Type: application/json');
echo json_encode($products);
