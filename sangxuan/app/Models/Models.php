<?php

require_once __DIR__ . '/BaseModel.php';

class CategoryModel extends BaseModel {
    protected string $table      = 'bookstore_categories';
    protected string $primaryKey = 'category_id';

    public function findAllWithCount(): array {
        $stmt = $this->db->query("
            SELECT c.*, COUNT(b.book_id) AS book_count
            FROM bookstore_categories c
            LEFT JOIN bookstore_books b ON b.category_id = c.category_id
            GROUP BY c.category_id
            ORDER BY c.category_name
        ");
        return $stmt->fetchAll();
    }
}

// ─────────────────────────────────────────────
class CartModel extends BaseModel {
    protected string $table      = 'bookstore_cart';
    protected string $primaryKey = 'cart_id';

    public function findByUser(int $userId): ?array {
        $stmt = $this->db->prepare(
            "SELECT * FROM bookstore_cart WHERE user_id = :uid"
        );
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetch() ?: null;
    }

    public function getCartItems(int $cartId): array {
        $stmt = $this->db->prepare("
            SELECT ci.*, b.title, b.price, b.author,
                   (SELECT image_url FROM bookstore_book_images bi
                    WHERE bi.book_id = b.book_id LIMIT 1) AS image_url
            FROM bookstore_cart_items ci
            JOIN bookstore_books b ON b.book_id = ci.book_id
            WHERE ci.cart_id = :cid
        ");
        $stmt->execute([':cid' => $cartId]);
        return $stmt->fetchAll();
    }

    public function addItem(int $cartId, int $bookId, int $qty = 1): void {
        $stmt = $this->db->prepare(
            "INSERT INTO bookstore_cart_items (cart_id, book_id, quantity)
             VALUES (:cid, :bid, :qty)
             ON DUPLICATE KEY UPDATE quantity = quantity + :qty2"
        );
        $stmt->execute([':cid' => $cartId, ':bid' => $bookId, ':qty' => $qty, ':qty2' => $qty]);
    }

    public function updateItem(int $cartItemId, int $qty): bool {
        $stmt = $this->db->prepare(
            "UPDATE bookstore_cart_items SET quantity = :qty WHERE cart_item_id = :id"
        );
        return $stmt->execute([':qty' => $qty, ':id' => $cartItemId]);
    }

    public function removeItem(int $cartItemId): bool {
        $stmt = $this->db->prepare(
            "DELETE FROM bookstore_cart_items WHERE cart_item_id = :id"
        );
        return $stmt->execute([':id' => $cartItemId]);
    }

    public function clearCart(int $cartId): bool {
        $stmt = $this->db->prepare(
            "DELETE FROM bookstore_cart_items WHERE cart_id = :cid"
        );
        return $stmt->execute([':cid' => $cartId]);
    }
}

// ─────────────────────────────────────────────
class OrderModel extends BaseModel {
    protected string $table      = 'bookstore_orders';
    protected string $primaryKey = 'order_id';

    public function createOrder(int $userId, float $total, string $paymentMethod): int {
        return $this->create([
            'user_id'        => $userId,
            'total_price'    => $total,
            'status'         => 'pending',
            'payment_method' => $paymentMethod,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);
    }

    public function addOrderItem(int $orderId, int $bookId, int $qty, float $price): void {
        $stmt = $this->db->prepare(
            "INSERT INTO bookstore_order_items (order_id, book_id, quantity, price)
             VALUES (:oid, :bid, :qty, :price)"
        );
        $stmt->execute([':oid' => $orderId, ':bid' => $bookId, ':qty' => $qty, ':price' => $price]);
    }

    public function findByUser(int $userId): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM bookstore_orders WHERE user_id = :uid ORDER BY created_at DESC"
        );
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll();
    }
}

// ─────────────────────────────────────────────
class UserModel extends BaseModel {
    protected string $table      = 'bookstore_users';
    protected string $primaryKey = 'user_id';

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare(
            "SELECT * FROM bookstore_users WHERE email = :email"
        );
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function register(string $fullName, string $email, string $phone, string $password): int {
        return $this->create([
            'full_name'  => $fullName,
            'email'      => $email,
            'phone'      => $phone,
            'password'   => password_hash($password, PASSWORD_BCRYPT),
            'role'       => 'customer',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
