<?php

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/../Core/ImageResolver.php';

/**
 * BookModel - Single Responsibility: chỉ xử lý logic Books
 */
class BookModel extends BaseModel {
    protected string $table      = 'bookstore_books';
    protected string $primaryKey = 'book_id';

    /** Thư mục gốc web (nơi chứa assets/) */
    private string $docRoot;

    public function __construct() {
        parent::__construct();
        // BookModel nằm tại: WebBanSach_updated/sangxuan/app/Models/
        // Đi lên 3 cấp: Models → app → sangxuan → WebBanSach_updated (gốc project)
        $this->docRoot = realpath(__DIR__ . '/../../..') . DIRECTORY_SEPARATOR;
    }

    private function resolveImages(array $rows): array {
        return ImageResolver::resolveMany($rows, $this->docRoot);
    }

    private function resolveImage(?array $row): ?array {
        if (!$row) return null;
        return ImageResolver::resolve($row, $this->docRoot);
    }

    /** Lấy sách kèm tên category và ảnh đầu tiên */
    public function findAllWithDetails(int $limit = 20, int $offset = 0): array {
        $stmt = $this->db->prepare("
            SELECT b.*, c.category_name,
                   (SELECT image_url FROM bookstore_book_images bi
                    WHERE bi.book_id = b.book_id LIMIT 1) AS image_url
            FROM bookstore_books b
            LEFT JOIN bookstore_categories c ON b.category_id = c.category_id
            ORDER BY b.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $this->resolveImages($stmt->fetchAll());
    }

    public function findByCategory(int $categoryId, int $limit = 20, int $offset = 0): array {
        $stmt = $this->db->prepare("
            SELECT b.*, c.category_name,
                   (SELECT image_url FROM bookstore_book_images bi
                    WHERE bi.book_id = b.book_id LIMIT 1) AS image_url
            FROM bookstore_books b
            LEFT JOIN bookstore_categories c ON b.category_id = c.category_id
            WHERE b.category_id = :cid
            ORDER BY b.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':cid',    $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':limit',  $limit,      PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,     PDO::PARAM_INT);
        $stmt->execute();
        return $this->resolveImages($stmt->fetchAll());
    }

    public function search(string $keyword, int $limit = 20): array {
        $stmt = $this->db->prepare("
            SELECT b.*, c.category_name,
                   (SELECT image_url FROM bookstore_book_images bi
                    WHERE bi.book_id = b.book_id LIMIT 1) AS image_url
            FROM bookstore_books b
            LEFT JOIN bookstore_categories c ON b.category_id = c.category_id
            WHERE b.title LIKE :kw OR b.author LIKE :kw OR b.publisher LIKE :kw
            LIMIT :limit
        ");
        $stmt->bindValue(':kw',    "%{$keyword}%");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $this->resolveImages($stmt->fetchAll());
    }

    public function findBestSellers(int $limit = 8): array {
        $stmt = $this->db->prepare("
            SELECT b.*, c.category_name,
                   COALESCE(SUM(oi.quantity), 0) AS total_sold,
                   (SELECT image_url FROM bookstore_book_images bi
                    WHERE bi.book_id = b.book_id LIMIT 1) AS image_url
            FROM bookstore_books b
            LEFT JOIN bookstore_categories c ON b.category_id = c.category_id
            LEFT JOIN bookstore_order_items oi ON oi.book_id = b.book_id
            GROUP BY b.book_id
            ORDER BY total_sold DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $this->resolveImages($stmt->fetchAll());
    }

    public function findWithPromotion(): array {
        $today = date('Y-m-d');
        $stmt = $this->db->prepare("
            SELECT b.*, c.category_name, p.discount_percent,
                   (b.price * (1 - p.discount_percent/100)) AS sale_price,
                   (SELECT image_url FROM bookstore_book_images bi
                    WHERE bi.book_id = b.book_id LIMIT 1) AS image_url
            FROM bookstore_books b
            LEFT JOIN bookstore_categories c ON b.category_id = c.category_id
            INNER JOIN bookstore_promotion_books pb ON pb.book_id = b.book_id
            INNER JOIN bookstore_promotions p ON p.promotion_id = pb.promotion_id
            WHERE p.start_date <= :today AND p.end_date >= :today
            GROUP BY b.book_id
            LIMIT 12
        ");
        $stmt->execute([':today' => $today]);
        return $this->resolveImages($stmt->fetchAll());
    }

    /** Override findById để cũng resolve ảnh */
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT b.*, c.category_name,
                   (SELECT image_url FROM bookstore_book_images bi
                    WHERE bi.book_id = b.book_id LIMIT 1) AS image_url
            FROM bookstore_books b
            LEFT JOIN bookstore_categories c ON b.category_id = c.category_id
            WHERE b.book_id = :id
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch() ?: null;
        return $this->resolveImage($row);
    }
}
