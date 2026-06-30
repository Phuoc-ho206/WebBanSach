<?php

class fbauthcontroller
{
    private static $appId = 'FACEBOOK_APP_ID';
    private static $appSecret = 'FACEBOOK_APP_SECRET';
    private static $redirectUri = 'http://localhost/WebBanSach/auth/pages/login.php'; // URL callback

    // Lấy URL đăng nhập Facebook
    public static function getLoginUrl()
    {
        $scope = 'public_profile,email'; // Quyền truy cập
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

        $response = @file_get_contents($tokenUrl);
        if ($response === false) {
            return false;
        }
        $tokenData = json_decode($response, true);

        if (!isset($tokenData['access_token'])) {
            return false;
        }

        $accessToken = $tokenData['access_token'];

        // Bước 2: Dùng access token để lấy thông tin user từ Facebook
        $userUrl = 'https://graph.facebook.com/me?fields=id,name,email&access_token=' . $accessToken;

        $userResponse = @file_get_contents($userUrl);
        if ($userResponse === false) {
            return false;
        }
        $userData = json_decode($userResponse, true);

        if (!isset($userData['id'])) {
            return false;
        }

        // Bước 3: Lưu/cập nhật user vào database
        return self::saveOrUpdateUser($userData, $accessToken);
    }

    // Lưu hoặc cập nhật user vào database MySQL
    private static function saveOrUpdateUser($userData, $accessToken)
    {
        // Kết nối database dùng chung
        require_once __DIR__ . '/../../config/db.php';
        $conn = $conn ?? $GLOBALS['conn'] ?? null;

        $facebookId = $userData['id'];
        $name = $userData['name'] ?? ('User ' . $facebookId);
        $email = $userData['email'] ?? '';

        // 1. Kiểm tra xem tài khoản đã được liên kết trong user_provider chưa
        $checkSql = "SELECT User_ID FROM user_provider WHERE ProviderName = 'Facebook' AND Provider_userID = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param('s', $facebookId);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $userId = $row['User_ID'];
            
            // Cập nhật access token mới
            $updateTokenSql = "UPDATE user_provider SET access_token = ? WHERE User_ID = ? AND ProviderName = 'Facebook'";
            $updateTokenStmt = $conn->prepare($updateTokenSql);
            $updateTokenStmt->bind_param('si', $accessToken, $userId);
            $updateTokenStmt->execute();
            $updateTokenStmt->close();

            // Lấy thông tin người dùng từ bảng user
            $userSql = "SELECT CustomerID, LastName, FirstName, Email, Phone, Address, RoleID, CreatedDate FROM user WHERE CustomerID = ?";
            $userStmt = $conn->prepare($userSql);
            $userStmt->bind_param('i', $userId);
            $userStmt->execute();
            $userResult = $userStmt->get_result();
            if ($userResult->num_rows > 0) {
                $userRow = $userResult->fetch_assoc();
                $checkStmt->close();
                $userStmt->close();
                
                return [
                    'id' => (int)$userRow['CustomerID'],
                    'username' => $userRow['Email'],
                    'email' => $userRow['Email'],
                    'full_name' => trim($userRow['LastName'] . ' ' . $userRow['FirstName']),
                    'phone' => $userRow['Phone'] ?? '',
                    'address' => $userRow['Address'] ?? '',
                    'role' => 'customer',
                    'login_type' => 'facebook'
                ];
            }
            $userStmt->close();
        }
        $checkStmt->close();

        // 2. Nếu chưa liên kết, kiểm tra xem đã có user nào trùng email trong bảng user chưa
        $userId = null;
        if (!empty($email)) {
            $userSql = "SELECT CustomerID FROM user WHERE Email = ? LIMIT 1";
            $userStmt = $conn->prepare($userSql);
            $userStmt->bind_param('s', $email);
            $userStmt->execute();
            $userResult = $userStmt->get_result();
            if ($userResult->num_rows > 0) {
                $userRow = $userResult->fetch_assoc();
                $userId = $userRow['CustomerID'];
            }
            $userStmt->close();
        }

        // 3. Nếu chưa có user trong bảng user, tạo mới
        if (!$userId) {
            $parts = explode(' ', $name);
            if (count($parts) > 1) {
                $firstName = array_pop($parts);
                $lastName = implode(' ', $parts);
            } else {
                $firstName = $name;
                $lastName = '';
            }

            $insertSql = "INSERT INTO user (LastName, FirstName, Email, Password, RoleID, CreatedDate) VALUES (?, ?, ?, '', 2, NOW())";
            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->bind_param('sss', $lastName, $firstName, $email);
            $insertStmt->execute();
            $userId = $insertStmt->insert_id;
            $insertStmt->close();
        }

        // 4. Lưu liên kết vào bảng user_provider
        $linkSql = "INSERT INTO user_provider (User_ID, ProviderName, Provider_userID, access_token, CreatedAt) VALUES (?, 'Facebook', ?, ?, NOW())";
        $linkStmt = $conn->prepare($linkSql);
        $linkStmt->bind_param('isss', $userId, $facebookId, $accessToken);
        $linkStmt->execute();
        $linkStmt->close();

        // Lấy thông tin user cuối cùng để trả về
        $userSql = "SELECT CustomerID, LastName, FirstName, Email, Phone, Address, RoleID, CreatedDate FROM user WHERE CustomerID = ?";
        $userStmt = $conn->prepare($userSql);
        $userStmt->bind_param('i', $userId);
        $userStmt->execute();
        $userResult = $userStmt->get_result();
        $userRow = $userResult->fetch_assoc();
        $userStmt->close();

        return [
            'id' => (int)$userRow['CustomerID'],
            'username' => $userRow['Email'],
            'email' => $userRow['Email'],
            'full_name' => trim($userRow['LastName'] . ' ' . $userRow['FirstName']),
            'phone' => $userRow['Phone'] ?? '',
            'address' => $userRow['Address'] ?? '',
            'role' => 'customer',
            'login_type' => 'facebook'
        ];
    }
}