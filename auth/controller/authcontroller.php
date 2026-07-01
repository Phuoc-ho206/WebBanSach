<?php

/**
 * AuthController — Xử lý login / logout / JWT session.
 */

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../helpers/JwtHelper.php';
require_once __DIR__ . '/../models/Customer.php';

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
    public static function login(string $username, string $password): ?array
    {
        $username = trim($username);

        $controller = new self();
        $user = $controller->customerModel->findByIdentity($username);

        if (!$user) {
            return null;
        }

        if (password_verify($password, $user['password'])) {
            unset($user['password']);
            return $user;
        }

        return null;
    }

    /**
     * Thiết lập phiên đăng nhập: PHP session + token JWT lưu DB.
     */
    public static function establishSession(array $user, bool $remember = false): void
    {
        $controller = new self();
        $controller->customerModel->updateLastLogin((int) $user['id']);

        $jti = bin2hex(random_bytes(32));
        $expiresAt = $remember
            ? time() + (AUTH_REMEMBER_DAYS * 86400)
            : time() + (AUTH_SESSION_HOURS * 3600);

        $payload = [
            'sub' => (int) $user['id'],
            'jti' => $jti,
            'role' => $user['role'] ?? 'customer',
            'iat' => time(),
            'exp' => $expiresAt,
            'rm' => $remember ? 1 : 0,
        ];

        $jwt = JwtHelper::encode($payload, AUTH_JWT_SECRET);

        $_SESSION['user'] = $user;
        $_SESSION['auth_jti'] = $jti;

        if ($remember) {
            self::setAuthCookie($jwt, $expiresAt);
        } else {
            self::clearAuthCookie();
        }
    }

    /**
     * Khôi phục đăng nhập từ cookie JWT (remember-me).
     */
    public static function tryRestoreSession(): void
    {
        if (isset($_SESSION['user'])) {
            return;
        }

        $jwt = $_COOKIE[AUTH_COOKIE_NAME] ?? '';
        if ($jwt === '') {
            return;
        }

        $payload = JwtHelper::decode($jwt, AUTH_JWT_SECRET);
        if (!$payload || empty($payload['jti']) || empty($payload['sub'])) {
            self::clearAuthCookie();
            return;
        }

        if (empty($payload['rm'])) {
            self::clearAuthCookie();
            return;
        }

        $customerModel = new Customer();
        $user = $customerModel->findById((int) $payload['sub']);
        if (!$user) {
            self::clearAuthCookie();
            return;
        }

        $_SESSION['user'] = $user;
        $_SESSION['auth_jti'] = $payload['jti'];
    }

    /**
     * URL chuyển hướng sau khi đăng nhập thành công.
     */
    public static function getRedirectUrl(array $user): string
    {
        if (strtolower($user['role'] ?? '') === 'admin') {
            return '/WebBanSach/admin/index.php';
        }

        return '/WebBanSach/index.php';
    }

    /**
     * Xử lý đăng xuất — thu hồi token DB và xóa cookie.
     */
    public static function logout(): void
    {
        self::revokeStoredSession();
        self::clearAuthCookie();

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

    private static function revokeStoredSession(): void
    {
        // Phiên JWT không trạng thái (stateless), không cần xóa trong database.
    }

    private static function setAuthCookie(string $jwt, int $expiresAt): void
    {
        setcookie(
            AUTH_COOKIE_NAME,
            $jwt,
            [
                'expires' => $expiresAt,
                'path' => AUTH_COOKIE_PATH,
                'secure' => self::isSecureRequest(),
                'httponly' => true,
                'samesite' => 'Lax',
            ]
        );
    }

    public static function clearAuthCookie(): void
    {
        setcookie(
            AUTH_COOKIE_NAME,
            '',
            [
                'expires' => time() - 42000,
                'path' => AUTH_COOKIE_PATH,
                'secure' => self::isSecureRequest(),
                'httponly' => true,
                'samesite' => 'Lax',
            ]
        );
    }

    private static function isSecureRequest(): bool
    {
        return !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
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
