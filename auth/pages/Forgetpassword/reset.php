<?php
session_start();
require_once '../../../config/db.php';
require_once '../../../auth/controller/forgetpasswordcontroller.php';

$error = '';
$email = $_SESSION['verified_email'] ?? '';

// Kiểm tra đã verify OTP chưa
if (empty($email)) {
    header('Location: /WebBanSach/auth/pages/Forgetpassword/index.php');
    exit;
}

// Xử lý reset password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    $forgetPasswordController = new ForgetPasswordController();
    $result = $forgetPasswordController->resetPassword($email, $password, $confirmPassword);

    if ($result['success']) {
        // Xóa session
        unset($_SESSION['reset_email']);
        unset($_SESSION['verified_email']);

        // Redirect về login với success message
        $_SESSION['success'] = $result['message'];
        header('Location: /WebBanSach/auth/pages/login.php');
        exit;
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quên mật khẩu - Bước 3</title>

    <link rel="stylesheet" href="/WebBanSach/assets/css/variables.css">
    <link rel="stylesheet" href="/WebBanSach/assets/css/components/form.css">
    <link rel="stylesheet" href="/WebBanSach/assets/css/components/button.css">
    <link rel="stylesheet" href="/WebBanSach/assets/css/components/card.css">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--color-background);
            font-family: var(--font-family-base);
        }

        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .auth-card {
            width: 100%;
            max-width: 420px;
            padding: 32px;
            box-sizing: border-box;
        }

        .auth-card h1 {
            margin: 0 0 12px;
            text-align: center;
            color: var(--color-primary);
        }

        .steps {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
        }

        .step {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            font-weight: bold;
            background: #e0e0e0;
            color: #888;
        }

        .step.active {
            background: var(--color-primary);
            color: #fff;
        }

        .step.done {
            background: var(--color-success, #4caf50);
            color: #fff;
        }

        .step-divider {
            width: 30px;
            height: 2px;
            background: #e0e0e0;
        }

        .subtitle {
            text-align: center;
            margin-bottom: 20px;
            color: var(--color-text-light, #666);
            line-height: 1.5;
        }


        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            box-sizing: border-box;
            transition: 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--color-primary);
        }

        .password-strength {
            margin-top: 8px;
            font-size: 0.85rem;
            padding: 8px;
            border-radius: 4px;
            background: #f0f0f0;
            color: #666;
        }

        .otp-inputs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 12px;
        }

        .otp-inputs input {
            width: 48px;
            height: 48px;
            text-align: center;
            font-size: 1.2rem;
            font-weight: bold;
            border: 2px solid #ddd;
            border-radius: 6px;
            padding: 0;
            transition: 0.3s;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 24px;
        }

        .actions .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            font-size: 1rem;
            font-weight: 500;
            transition: 0.3s;
        }

        .btn-primary {
            background: var(--color-primary, #0d6efd);
            color: white;
        }

        .btn-primary:hover {
            opacity: 0.9;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            opacity: 0.9;
        }
    </style>
</head>

<body>

    <div class="card auth-card">

        <h1>Quên mật khẩu</h1>

        <!-- Thanh tiến trình -->
        <div class="steps">
            <div class="step done">1</div>
            <div class="step-divider"></div>
            <div class="step done">2</div>
            <div class="step-divider"></div>
            <div class="step active">3</div>
        </div>

        <!-- Nội dung -->
        <p class="subtitle">
            Nhập mật khẩu mới của bạn
        </p>

        <?php if ($error): ?>
            <div
                style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 16px; border: 1px solid #f5c6cb; text-align: center;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">

            <div class="form-group">
                <label for="password">Mật khẩu mới:</label>
                <input type="password" id="password" name="password" placeholder="Nhập mật khẩu mới" required>
                <div class="password-strength">
                    Mật khẩu phải có ít nhất 8 ký tự
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Xác nhận mật khẩu:</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Nhập lại mật khẩu"
                    required>
            </div>


            <div class="actions">
                <button type="submit" class="btn btn-primary">
                    Cập nhật mật khẩu
                </button>

                <a href="/WebBanSach/auth/pages/login.php" class="btn btn-secondary">
                    Quay lại
                </a>
            </div>

        </form>

    </div>

</body>

</html>