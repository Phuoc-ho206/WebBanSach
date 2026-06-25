<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * ForgetPasswordController - Xử lý quên mật khẩu và reset password
 */

class ForgetPasswordController
{
    private $conn;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/db.php';
        $this->conn = $conn;
    }

    /**
     * Gửi OTP đến email
     * 
     * @return array ['success' => bool, 'message' => string]
     */
    public function sendOTP(string $email): array
    {
        // Bước 1: Validate email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Email không hợp lệ.'
            ];
        }

        // Bước 2: Kiểm tra email có tồn tại trong hệ thống không
        $sql = "SELECT id FROM users WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return [
                'success' => false,
                'message' => 'Email không tồn tại trong hệ thống.'
            ];
        }

        $stmt->close();

        // Bước 3: Tạo OTP (4 chữ số)
        $otp = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        // Bước 4: Xóa các token cũ của email này (nếu có)
        $deleteSql = "DELETE FROM password_reset_tokens WHERE email = ?";
        $deleteStmt = $this->conn->prepare($deleteSql);
        $deleteStmt->bind_param('s', $email);
        $deleteStmt->execute();
        $deleteStmt->close();

        // Bước 5: Lưu OTP vào database
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 minutes'));
        $insertSql = "INSERT INTO password_reset_tokens (email, token, expires_at, used, created_at) 
                      VALUES (?, ?, ?, 0, NOW())";
        $insertStmt = $this->conn->prepare($insertSql);
        $insertStmt->bind_param('sss', $email, $otp, $expiresAt);

        if (!$insertStmt->execute()) {
            $insertStmt->close();
            return [
                'success' => false,
                'message' => 'Lỗi hệ thống. Vui lòng thử lại sau.'
            ];
        }

        $insertStmt->close();

        // Bước 6: Gửi email OTP
        $emailSent = $this->sendOTPEmail($email, $otp);

        if (!$emailSent) {
            // Nếu gửi email thất bại, xóa token vừa tạo
            $this->deleteTokenByEmail($email);
            return [
                'success' => false,
                'message' => 'Không thể gửi email. Vui lòng thử lại sau.'
            ];
        }

        return [
            'success' => true,
            'message' => 'Mã OTP đã được gửi đến email của bạn.'
        ];
    }

    /**
     * Verify OTP
     * 
     * @return array ['success' => bool, 'message' => string]
     */
    public function verifyOTP(string $email, string $otp): array
    {
        // Bước 1: Kiểm tra OTP có rỗng không
        if (empty($otp)) {
            return [
                'success' => false,
                'message' => 'Vui lòng nhập mã OTP.'
            ];
        }

        // Bước 2: Lấy token từ database
        $sql = "SELECT id, token, expires_at, used 
                FROM password_reset_tokens 
                WHERE email = ? 
                ORDER BY created_at DESC 
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $stmt->close();
            return [
                'success' => false,
                'message' => 'Không tìm thấy yêu cầu reset password. Vui lòng thử lại.'
            ];
        }

        $tokenData = $result->fetch_assoc();
        $stmt->close();

        // Bước 3: Kiểm tra token đã được sử dụng chưa
        if ($tokenData['used']) {
            return [
                'success' => false,
                'message' => 'Mã OTP đã được sử dụng. Vui lòng yêu cầu mã mới.'
            ];
        }

        // Bước 4: Kiểm tra token có khớp không
        if ($tokenData['token'] !== $otp) {
            return [
                'success' => false,
                'message' => 'Mã OTP không đúng.'
            ];
        }

        // Bước 5: Kiểm tra token có hết hạn không
        if (strtotime($tokenData['expires_at']) < time()) {
            // Xóa token hết hạn
            $this->deleteToken($tokenData['id']);
            return [
                'success' => false,
                'message' => 'Mã OTP đã hết hạn. Vui lòng yêu cầu mã mới.'
            ];
        }

        // Bước 6: Đánh dấu token đã sử dụng
        $updateSql = "UPDATE password_reset_tokens SET used = 1 WHERE id = ?";
        $updateStmt = $this->conn->prepare($updateSql);
        $updateStmt->bind_param('i', $tokenData['id']);
        $updateStmt->execute();
        $updateStmt->close();

        return [
            'success' => true,
            'message' => 'Xác thực OTP thành công.'
        ];
    }

    /**
     * Reset password
     * 
     * @return array ['success' => bool, 'message' => string]
     */
    public function resetPassword(string $email, string $newPassword, string $confirmPassword): array
    {
        // Bước 1: Validate password mới
        if (empty($newPassword)) {
            return [
                'success' => false,
                'message' => 'Vui lòng nhập mật khẩu mới.'
            ];
        }

        if (strlen($newPassword) < 8) {
            return [
                'success' => false,
                'message' => 'Mật khẩu phải có ít nhất 8 ký tự.'
            ];
        }

        if (strlen($newPassword) > 255) {
            return [
                'success' => false,
                'message' => 'Mật khẩu không được vượt quá 255 ký tự.'
            ];
        }

        if (empty($confirmPassword)) {
            return [
                'success' => false,
                'message' => 'Vui lòng xác nhận mật khẩu.'
            ];
        }

        if ($newPassword !== $confirmPassword) {
            return [
                'success' => false,
                'message' => 'Mật khẩu xác nhận không khớp.'
            ];
        }

        // Bước 2: Kiểm tra email có tồn tại không
        $sql = "SELECT id FROM users WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $stmt->close();
            return [
                'success' => false,
                'message' => 'Email không tồn tại trong hệ thống.'
            ];
        }

        $user = $result->fetch_assoc();
        $stmt->close();

        // Bước 3: Hash password mới
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        // Bước 4: Cập nhật password
        $updateSql = "UPDATE users SET password = ? WHERE id = ?";
        $updateStmt = $this->conn->prepare($updateSql);
        $updateStmt->bind_param('si', $passwordHash, $user['id']);

        if (!$updateStmt->execute()) {
            $updateStmt->close();
            return [
                'success' => false,
                'message' => 'Cập nhật mật khẩu thất bại. Vui lòng thử lại sau.'
            ];
        }

        $updateStmt->close();

        // Bước 5: Xóa tất cả token của email này
        $this->deleteTokenByEmail($email);

        return [
            'success' => true,
            'message' => 'Đặt lại mật khẩu thành công! Bạn có thể đăng nhập với mật khẩu mới.'
        ];
    }

    /**
     * Gửi lại OTP
     * 
     * @return array ['success' => bool, 'message' => string]
     */
    public function resendOTP(string $email): array
    {
        // Xóa token cũ
        $this->deleteTokenByEmail($email);

        // Gửi OTP mới
        return $this->sendOTP($email);
    }

    /**
     * Xóa token theo email
     */
    private function deleteTokenByEmail(string $email): bool
    {
        $sql = "DELETE FROM password_reset_tokens WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Xóa token theo ID
     */
    private function deleteToken(int $tokenId): bool
    {
        $sql = "DELETE FROM password_reset_tokens WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $tokenId);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Gửi email OTP
     * 
     * @return bool true nếu gửi thành công, false nếu thất bại
     */
    private function sendOTPEmail(string $email, string $otp): bool
    {
        // Kiểm tra xem có PHPMailer không
        if (!file_exists(__DIR__ . '/../../vendor/PHPMailer/src/PHPMailer.php')) {
            // Nếu không có PHPMailer, log OTP và trả về true (cho development)
            error_log("OTP for {$email}: {$otp}");
            return true;
        }

        try {
            require_once __DIR__ . '/../../vendor/PHPMailer/src/PHPMailer.php';
            require_once __DIR__ . '/../../vendor/PHPMailer/src/SMTP.php';
            require_once __DIR__ . '/../../vendor/PHPMailer/src/Exception.php';

            $mail = new PHPMailer(true);

            // Cấu hình SMTP (cần thay đổi theo email thật của bạn)
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'your-email@gmail.com'; // Thay bằng email của bạn
            $mail->Password = 'your-app-password'; // Thay bằng app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom('your-email@gmail.com', 'WebBanSach');
            $mail->addAddress($email);
            $mail->Subject = 'Mã OTP Reset Mật Khẩu';
            $mail->Body = "
                <h2>Reset mật khẩu</h2>
                <p>Mã OTP của bạn:</p>
                <h3 style='color: red;'>{$otp}</h3>
                <p>Mã này có hiệu lực trong 30 phút</p>
                <p>Nếu bạn không yêu cầu reset mật khẩu, vui lòng bỏ qua email này.</p>
            ";
            $mail->isHTML(true);

            $mail->send();
            return true;

        } catch (Exception $e) {
            error_log("Lỗi gửi email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa các token đã hết hạn (dùng cho cron job)
     */
    public function cleanExpiredTokens(): bool
    {
        $sql = "DELETE FROM password_reset_tokens WHERE expires_at < NOW()";
        return $this->conn->query($sql);
    }
}