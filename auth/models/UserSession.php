<?php

/**
 * Quản lý phiên đăng nhập lưu trong DB (JWT jti + token hash).
 */
class UserSession
{
    private $conn;
    private $table = 'user_sessions';

    public function __construct()
    {
        if (!isset($GLOBALS['conn'])) {
            require_once __DIR__ . '/../../config/db.php';
        }
        $this->conn = $GLOBALS['conn'];
    }

    public function create(
        int $customerId,
        string $jti,
        string $tokenHash,
        bool $remember,
        string $expiresAt,
        ?string $userAgent = null,
        ?string $ipAddress = null
    ): bool {
        $sql = "INSERT INTO {$this->table}
                (CustomerID, Jti, TokenHash, RememberMe, UserAgent, IpAddress, ExpiresAt, CreatedAt, LastUsedAt)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

        $rememberValue = $remember ? 1 : 0;
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            'ississs',
            $customerId,
            $jti,
            $tokenHash,
            $rememberValue,
            $userAgent,
            $ipAddress,
            $expiresAt
        );

        return $stmt->execute();
    }

    public function findValidByJti(string $jti): ?array
    {
        $sql = "SELECT SessionID, CustomerID, Jti, TokenHash, RememberMe, ExpiresAt, RevokedAt
                FROM {$this->table}
                WHERE Jti = ?
                  AND RevokedAt IS NULL
                  AND ExpiresAt > NOW()
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $jti);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return null;
        }

        return $result->fetch_assoc();
    }

    public function touchLastUsed(string $jti): bool
    {
        $sql = "UPDATE {$this->table} SET LastUsedAt = NOW() WHERE Jti = ? AND RevokedAt IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $jti);

        return $stmt->execute();
    }

    public function revokeByJti(string $jti): bool
    {
        $sql = "UPDATE {$this->table} SET RevokedAt = NOW() WHERE Jti = ? AND RevokedAt IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $jti);

        return $stmt->execute();
    }

    public function revokeAllForUser(int $customerId): bool
    {
        $sql = "UPDATE {$this->table} SET RevokedAt = NOW() WHERE CustomerID = ? AND RevokedAt IS NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $customerId);

        return $stmt->execute();
    }
}
