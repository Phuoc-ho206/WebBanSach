<?php

class fbauthcontroller
{

    private static $appId = 'FACEBOOK_APP_ID';
    private static $appSecret = 'FACEBOOK_APP_SECRET';
    private static $redirectUri = 'http://localhost/WebBanSach/auth/pages/login.php'; // URL callback
    // Lấy URL đăng nhập Facebook
    public static function getLoginUrl()
    {
        $scope = 'public_profile'; // Quyền truy cập
        $authUrl = 'https://www.facebook.com/v18.0/dialog/oauth?'
            . 'client_id=' . self::$appId
            . '&redirect_uri=' . urlencode(self::$redirectUri)
            . '&scope=' . $scope
            . '&state=' . bin2hex(random_bytes(16)); // Bảo mật CSRF

        return $authUrl;
    }

    // Xử lý callback từ Facebook
    public static function handleCallback()
    {
        // Kiểm tra nếu có lỗi từ Facebook
        if (isset($_GET['error'])) {
            return false;
        }

        // Kiểm tra nếu có code từ Facebook
        if (!isset($_GET['code'])) {
            return false;
        }

        $code = $_GET['code'];

        // Bước 1: Dùng code để lấy access token từ Facebook
        $tokenUrl = 'https://graph.facebook.com/v18.0/oauth/access_token?'
            . 'client_id=' . self::$appId
            . '&client_secret=' . self::$appSecret
            . '&redirect_uri=' . urlencode(self::$redirectUri)
            . '&code=' . $code;

        $response = file_get_contents($tokenUrl);
        $tokenData = json_decode($response, true);

        if (!isset($tokenData['access_token'])) {
            return false;
        }

        $accessToken = $tokenData['access_token'];

        // Bước 2: Dùng access token để lấy thông tin user từ Facebook
        $userUrl = 'https://graph.facebook.com/me?fields=id&access_token=' . $accessToken;

        $userResponse = file_get_contents($userUrl);
        $userData = json_decode($userResponse, true);

        if (!isset($userData['id'])) {
            return false;
        }

        // Bước 3: Lưu/cập nhật user vào database
        return self::saveOrUpdateUser($userData);
    }

    // Lưu hoặc cập nhật user vào database MySQL
    private static function saveOrUpdateUser($userData)
    {
        // Kết nối database
        $conn = new mysqli('localhost', 'root', '', 'bookstore');

        if ($conn->connect_error) {
            return false;
        }

        $facebookId = $userData['id'];
        $name = 'User ' . $userData['id'];
        $email = $userData['email'] ?? '';
        $picture = '';

        // Kiểm tra user đã tồn tại chưa
        $checkSql = "SELECT id FROM users WHERE facebook_id = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param('s', $facebookId);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            // User đã tồn tại, cập nhật
            $updateSql = "UPDATE users SET name = ?, email = ? WHERE facebook_id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param('sss', $name, $email, $facebookId);
            $updateStmt->execute();
            $updateStmt->close();
        } else {
            // User mới, insert vào database
            $insertSql = "INSERT INTO users (facebook_id, name, email, created_at) VALUES (?, ?, ?, NOW())";
            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->bind_param('sss', $facebookId, $name, $email);
            $insertStmt->execute();
            $insertStmt->close();
        }

        $checkStmt->close();

        // Trả về thông tin user để lưu vào session
        $user = [
            'id' => $facebookId,
            'name' => $name,
            'email' => $email,
            'picture' => $picture,
            'login_type' => 'facebook'
        ];

        $conn->close();
        return $user;
    }
}

?>