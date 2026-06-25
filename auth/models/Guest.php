<?php

/**
 * Guest Model - Xử lý các thao tác cho khách chưa đăng nhập
 */

class Guest
{
    private $conn;
    private $table = 'guest_users';

    public function __construct()
    {
        require_once __DIR__ . '/../../config/db.php';
        $this->conn = $conn;
    }

    /**
     * Tạo hoặc lấy guest user ID
     * Tạo guest user mới nếu chưa có trong session
     */
    public function getGuestId(): int
    {
        // Kiểm tra nếu đã có guest_id trong session
        if (isset($_SESSION['guest_id'])) {
            return (int) $_SESSION['guest_id'];
        }

        // Tạo guest user mới
        $sql = "INSERT INTO {$this->table} (created_at) VALUES (NOW())";

        if ($this->conn->query($sql)) {
            $guestId = $this->conn->insert_id;
            $_SESSION['guest_id'] = $guestId;
            return $guestId;
        }

        // Fallback: trả về 0 nếu không tạo được
        return 0;
    }

    /**
     * Chuyển cart từ guest sang user khi đăng nhập
     */
    public function mergeGuestCartToUser(int $guestId, int $userId): bool
    {
        // Bước 1: Lấy tất cả items trong guest cart
        $sql = "SELECT product_id, quantity FROM cart WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $guestId);
        $stmt->execute();
        $result = $stmt->get_result();

        $guestItems = [];
        while ($row = $result->fetch_assoc()) {
            $guestItems[] = $row;
        }
        $stmt->close();

        // Bước 2: Merge với user cart
        foreach ($guestItems as $item) {
            // Kiểm tra xem sản phẩm đã có trong user cart chưa
            $checkSql = "SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?";
            $checkStmt = $this->conn->prepare($checkSql);
            $checkStmt->bind_param('ii', $userId, $item['product_id']);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows > 0) {
                // Đã có → cập nhật số lượng
                $existingItem = $checkResult->fetch_assoc();
                $newQuantity = $existingItem['quantity'] + $item['quantity'];

                $updateSql = "UPDATE cart SET quantity = ? WHERE id = ?";
                $updateStmt = $this->conn->prepare($updateSql);
                $updateStmt->bind_param('ii', $newQuantity, $existingItem['id']);
                $updateStmt->execute();
                $updateStmt->close();
            } else {
                // Chưa có → insert mới với user_id mới
                $insertSql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
                $insertStmt = $this->conn->prepare($insertSql);
                $insertStmt->bind_param('iii', $userId, $item['product_id'], $item['quantity']);
                $insertStmt->execute();
                $insertStmt->close();
            }

            $checkStmt->close();
        }

        // Bước 3: Xóa guest cart
        $deleteSql = "DELETE FROM cart WHERE user_id = ?";
        $deleteStmt = $this->conn->prepare($deleteSql);
        $deleteStmt->bind_param('i', $guestId);
        $deleteStmt->execute();
        $deleteStmt->close();

        // Bước 4: Xóa guest user
        $deleteGuestSql = "DELETE FROM {$this->table} WHERE id = ?";
        $deleteGuestStmt = $this->conn->prepare($deleteGuestSql);
        $deleteGuestStmt->bind_param('i', $guestId);
        $deleteGuestStmt->execute();
        $deleteGuestStmt->close();

        // Bước 5: Xóa guest_id khỏi session
        unset($_SESSION['guest_id']);

        return true;
    }

    /**
     * Lấy thông tin guest user
     */
    public function getGuestInfo(int $guestId): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $guestId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return null;
    }

    /**
     * Xóa guest user (khi logout hoặc merge)
     */
    public function deleteGuest(int $guestId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $guestId);

        return $stmt->execute();
    }
}