<?php

/**
 * Customer Model - Xử lý các thao tác với database cho khách hàng
 */

class Customer
{
    private $conn;
    private $table = 'users';

    public function __construct()
    {
        // Import database connection từ config/db.php
        require_once __DIR__ . '/../../config/db.php';
        $this->conn = $conn;
    }

    /**
     * Tìm user theo ID
     */
    public function findById(int $id): ?array
    {
        $sql = "SELECT id, username, email, full_name, phone, address, role, created_at 
                FROM {$this->table} 
                WHERE id = ? 
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
     * Tìm user theo email
     */
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT id, username, email, password, full_name, phone, address, role, created_at 
                FROM {$this->table} 
                WHERE email = ? 
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
     * Tìm user theo username
     */
    public function findByUsername(string $username): ?array
    {
        $sql = "SELECT id, username, email, password, full_name, phone, address, role, created_at 
                FROM {$this->table} 
                WHERE username = ? 
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
     * Tìm user theo username hoặc email (dùng cho login)
     */
    public function findByIdentity(string $identity): ?array
    {
        $sql = "SELECT id, username, email, password, full_name, phone, address, role, created_at 
                FROM {$this->table} 
                WHERE username = ? OR email = ? 
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ss', $identity, $identity);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return null;
    }

    /**
     * Tạo user mới
     */
    public function create(array $data): bool
    {
        $sql = "INSERT INTO {$this->table} 
                (username, email, password, full_name, phone, address, role, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            'sssssss',
            $data['username'],
            $data['email'],
            $data['password'],
            $data['full_name'] ?? '',
            $data['phone'] ?? '',
            $data['address'] ?? '',
            $data['role'] ?? 'customer'
        );

        return $stmt->execute();
    }

    /**
     * Cập nhật thông tin user
     */
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE {$this->table} 
                SET username = ?, email = ?, full_name = ?, phone = ?, address = ? 
                WHERE id = ?";

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
     * Cập nhật mật khẩu
     */
    public function updatePassword(int $id, string $newPasswordHash): bool
    {
        $sql = "UPDATE {$this->table} SET password = ? WHERE id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('si', $newPasswordHash, $id);

        return $stmt->execute();
    }

    /**
     * Kiểm tra password có đúng không
     */
    public function checkPassword(int $id, string $password): bool
    {
        $sql = "SELECT password FROM {$this->table} WHERE id = ? LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            return password_verify($password, $user['password']);
        }

        return false;
    }

    /**
     * Kiểm tra username đã tồn tại chưa
     */
    public function usernameExists(string $username): bool
    {
        $sql = "SELECT id FROM {$this->table} WHERE username = ? LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }

    /**
     * Kiểm tra email đã tồn tại chưa
     */
    public function emailExists(string $email): bool
    {
        $sql = "SELECT id FROM {$this->table} WHERE email = ? LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }

    /**
     * Cập nhật thời gian login cuối
     */
    public function updateLastLogin(int $id): bool
    {
        $sql = "UPDATE {$this->table} SET last_login = NOW() WHERE id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);

        return $stmt->execute();
    }

    /**
     * Lấy thông tin user để hiển thị (không bao gồm password)
     */
    public function getUserProfile(int $id): ?array
    {
        $sql = "SELECT id, username, email, full_name, phone, address, role, created_at 
                FROM {$this->table} 
                WHERE id = ? 
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
}