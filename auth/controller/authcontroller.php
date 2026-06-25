<?php

/**
 * AuthController — Xử lý login / logout.
 */

class AuthController
{
    private $customerModel;

    public function __construct()
    {
        $this->customerModel = new Customer();
    }

    /**
     * Xử lý đăng nhập.
     *
     * @param string $identity  Username hoặc email
     * @param string $password  Mật khẩu gốc
     * @return array|null       Thông tin user nếu đúng, null nếu sai
     */
    public static function login(string $identity, string $password): ?array
    {
        $identity = trim($identity);

        $controller = new self();
        $user = $controller->customerModel->findByIdentity($identity);

        if (!$user) {
            return null;
        }

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Trả về thông tin user (bỏ password)
            unset($user['password']);
            return $user;
        }

        // Sai password
        return null;
    }

    /**
     * Xử lý đăng xuất.
     */
    public static function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $p['path'],
                $p['domain'],
                $p['secure'],
                $p['httponly']
            );
        }

        session_destroy();
    }

    /**
     * Tìm user theo identity (username hoặc email)
     */
    public function findUserByIdentity(string $identity): ?array
    {
        return $this->customerModel->findByIdentity($identity);
    }

    /**
     * Cập nhật thời gian login cuối
     */
    public function updateLastLogin(int $userId): bool
    {
        return $this->customerModel->updateLastLogin($userId);
    }
}
