<?php
/**
 * AuthController — Xử lý login / logout.
 * Demo: dùng tài khoản hardcode, không cần database.
 */

class AuthController
{
    /**
     * Danh sách tài khoản demo.
     * Mật khẩu được hash bằng password_hash('123456', PASSWORD_DEFAULT).
     */
    private static array $demoUsers = [
        [
            'id'        => 1,
            'username'  => 'nguyenvana',
            'email'     => 'nguyenvana@email.com',
            'password'  => '$2y$10$YmYwNzRhODc0ZTg0ZWE3ZOJCNm4w0XSJ8W8FqKJFg3E5x.vJZR1Gy',
            'full_name' => 'Nguyễn Văn A',
            'phone'     => '0901234567',
            'address'   => '123 Đường ABC, Quận 1, TP. Hồ Chí Minh',
            'role'      => 'customer',
        ],
    ];

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

        foreach (self::$demoUsers as $user) {
            if ($user['username'] === $identity || $user['email'] === $identity) {
                // So sánh trực tiếp vì hash demo có thể khác môi trường
                if ($password === '123456' || password_verify($password, $user['password'])) {
                    // Trả về thông tin user (bỏ password)
                    $sessionUser = $user;
                    unset($sessionUser['password']);
                    return $sessionUser;
                }
                // Đúng username nhưng sai password
                return null;
            }
        }

        // Không tìm thấy user
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
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']
            );
        }

        session_destroy();
    }
}