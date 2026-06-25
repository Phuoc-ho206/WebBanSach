<?php
session_start();
require_once '../../../config/db.php';
require_once '../../../vendor/PHPMailer/src/PHPMailer.php';
require_once '../../../vendor/PHPMailer/src/SMTP.php';
require_once '../../../vendor/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    // 1. Validate email
    if (empty($email)) {
        $error = 'Vui lòng nhập email';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email không hợp lệ';
    } else {
        // 2. Check email tồn tại
        try {
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $error = 'Email không tồn tại trong hệ thống';
            } else {
                // 3. Tạo OTP
                $otp = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

                // 4. Lưu vào DB
                $expiresAt = date('Y-m-d H:i:s', strtotime('+30 minutes'));
                $stmt = $conn->prepare("INSERT INTO password_reset_tokens (email, token, expires_at) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $email, $otp, $expiresAt);

                if ($stmt->execute()) {
                    // 5. Gửi email
                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'phlminh666@gmail.com';
                        $mail->Password = 'jywn cgxe gbiv nahp'; // ← Thay mật khẩu
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;
                        $mail->CharSet = 'UTF-8';

                        $mail->setFrom('phlminh666@gmail.com', 'WebBanSach');
                        $mail->addAddress($email);
                        $mail->Subject = 'Mã OTP Reset Mật Khẩu';
                        $mail->Body = "
                            <h2>Reset mật khẩu</h2>
                            <p>Mã OTP của bạn:</p>
                            <h3 style='color: red;'>{$otp}</h3>
                            <p>Mã này có hiệu lực trong 30 phút</p>
                        ";
                        $mail->isHTML(true);

                        $mail->send();

                        // 6. Lưu email vào session + redirect
                        $_SESSION['reset_email'] = $email;
                        header('Location: /WebBanSach/auth/pages/Forgetpassword/verifyotp.php');
                        exit();

                    } catch (Exception $e) {
                        $error = 'Lỗi gửi email: ' . $mail->ErrorInfo;
                    }
                } else {
                    $error = 'Lỗi hệ thống';
                }
            }
            $stmt->close();
        } catch (Exception $e) {
            $error = 'Lỗi: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quên mật khẩu - Bước 1</title>
    <link rel="stylesheet" href="/WebBanSach/assets/css/variables.css">
    <link rel="stylesheet" href="/WebBanSach/assets/css/components/form.css">
    <link rel="stylesheet" href="/WebBanSach/assets/css/components/button.css">
    <link rel="stylesheet" href="/WebBanSach/assets/css/components/card.css">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--color-background);
            font-family: var(--font-family-base)
        }

        .auth-card {
            width: 100%;
            max-width: 420px;
            padding: 24px;
            box-sizing: border-box
        }

        .auth-card h1 {
            margin: 0 0 8px;
            text-align: center;
            color: var(--color-primary)
        }

        .form-group {
            margin-bottom: 12px
        }

        input {
            width: 100%;
            padding: 10px;
            box-sizing: border-box
        }

        .actions {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin-top: 16px
        }

        .steps {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 20px
        }

        .step {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
            background: #e0e0e0;
            color: #888
        }

        .step.active {
            background: var(--color-primary);
            color: #fff
        }

        .subtitle {
            text-align: center;
            margin: 0 0 16px;
            color: var(--color-text-light, #666)
        }

        .alert {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 12px;
            font-size: 0.9rem
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb
        }
    </style>
</head>

<body>
    <div class="card auth-card">
        <h1>Quên mật khẩu</h1>

        <div class="steps">
            <div class="step active">1</div>
            <div class="step-divider"></div>
            <div class="step">2</div>
            <div class="step-divider"></div>
            <div class="step">3</div>
        </div>

        <p class="subtitle">Nhập email để nhận mã OTP</p>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" placeholder="example@email.com" required
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            <div class="actions">
                <button type="submit" class="btn btn-primary">Gửi mã OTP</button>
                <a class="btn btn-secondary" href="/WebBanSach/auth/pages/login.php">Quay lại</a>
            </div>
        </form>
    </div>
</body>

</html>