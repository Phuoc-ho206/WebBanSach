<?php

/**
 * Admin Model - Xử lý các thao tác với database cho admin
 */

class Admin
{
    private $conn;
    private $table = 'users';

    public function __construct()
    {
        require_once __DIR__ . '/../../config/db.php';
        $this->conn = $conn;
    }

    /**
     * Tìm admin theo ID
     */
    public function findById(int $id): ?array
    {
        $sql = "SELECT id, username, email, full_name, phone, address, role, created_at 
                FROM {$this->table} 
                WHERE id = ? AND role = 'admin'
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return null;
    }

    /**
     * Tìm admin theo email
     */
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT id, username, email, password, full_name, phone, address, role, created_at 
                FROM {$this->table} 
                WHERE email = ? AND role = 'admin'
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return null;
    }

    /**
     * Tìm admin theo username
     */
    public function findByUsername(string $username): ?array
    {
        $sql = "SELECT id, username, email, password, full_name, phone, address, role, created_at 
                FROM {$this->table} 
                WHERE username = ? AND role = 'admin'
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return null;
    }

    /**
     * Kiểm tra user có phải admin không
     */
    public function isAdmin(int $userId): bool
    {
        $sql = "SELECT role FROM {$this->table} WHERE id = ? LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            return $user['role'] === 'admin';
        }

        return false;
    }

    /**
     * Lấy tất cả admin users
     */
    public function getAllAdmins(): array
    {
        $sql = "SELECT id, username, email, full_name, phone, created_at 
                FROM {$this->table} 
                WHERE role = 'admin'
                ORDER BY created_at DESC";

        $result = $this->conn->query($sql);
        $admins = [];

        while ($row = $result->fetch_assoc()) {
            $admins[] = $row;
        }

        return $admins;
    }

    /**
     * Tạo admin mới
     */
    public function create(array $data): bool
    {
        $sql = "INSERT INTO {$this->table} 
                (username, email, password, full_name, phone, address, role, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, 'admin', NOW())";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            'ssssss',
            $data['username'],
            $data['email'],
            $data['password'],
            $data['full_name'] ?? '',
            $data['phone'] ?? '',
            $data['address'] ?? ''
        );

        return $stmt->execute();
    }

    /**
     * Cập nhật thông tin admin
     */
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE {$this->table} 
                SET username = ?, email = ?, full_name = ?, phone = ?, address = ? 
                WHERE id = ? AND role = 'admin'";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            'sssssi',
            $data['username'],
            $data['email'],
            $data['full_name'],
            $data['phone'],
            $data['address'],
            $id
        );

        return $stmt->execute();
    }

    /**
     * Xóa admin (chuyển role thành customer thay vì xóa)
     */
    public function delete(int $id): bool
    {
        $sql = "UPDATE {$this->table} SET role = 'customer' WHERE id = ? AND role = 'admin'";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);

        return $stmt->execute();
    }
}