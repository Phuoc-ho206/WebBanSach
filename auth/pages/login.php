<?php
session_start();
require_once '../controller/GoogleAuthController.php';
require_once '../controller/fbauthcontroller.php';

// Xử lý callback từ Google
if (isset($_GET['code'])) {
    $user = googleauthcontroller::handleCallback();
    if ($user) {
        $_SESSION['user'] = $user;
        header('Location: /WebBanSach/index.php');
        exit;
    }
}

$google_login_url = googleauthcontroller::getLoginUrl();
$facebook_login_url = fbauthcontroller::getLoginUrl();
?>




<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập</title>
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

        .input-row {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .input-row input {
            width: auto;
            flex: 1;
        }

        .input-row .btn,
        .input-row a.btn {
            white-space: nowrap;
        }

        .actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
            align-items: stretch;
            justify-content: center;
            margin-top: 12px;
        }

        .actions .btn,
        .actions a.btn {
            width: 100%;
            box-sizing: border-box;
        }

        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-top: 12px;
        }

        .remember-row .btn,
        .remember-row a.btn {
            width: auto;
            min-width: 0;
        }

        .remember-row .register-link {
            margin-left: auto;
        }
    </style>
</head>

<body>
    <div class="card auth-card">
        <h1>Đăng nhập</h1>
        <div
            style="background-color: #e3f2fd; color: #0277bd; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 14px; text-align: center; border: 1px solid #81d4fa;">
            <strong>Tài khoản Demo:</strong><br>
            Tên đăng nhập: <code>nguyenvana</code><br>
            Mật khẩu: <code>123456</code>
        </div>
        <form action="/WebBanSach/auth/pages/login.php" method="POST">
            <div class="form-group">
                <label for="identity">Email hoặc tên đăng nhập</label>
                <input id="identity" name="identity" type="text" required>
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <div class="input-row">
                    <input id="password" name="password" type="password" required>

                </div>
            </div>
            <div class="remember-row">
                <div class="remember-group">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">Ghi nhớ đăng nhập</label>
                </div>
                <a class="btn btn-secondary register-link" href="/WebBanSach/auth/pages/register.php">Đăng ký</a>
            </div>
            <div class="actions">
                <a href="/WebBanSach/auth/pages/profile.php" class="btn btn-primary"
                    style="display:flex; align-items:center; justify-content:center; text-decoration: none; font-weight: bold; color: white; background-color: #ff6b1a;">
                    Đăng nhập</a>
                <a href="<?= $google_login_url ?>" class="btn btn-primary"
                    style="text-align:center; text-decoration:none; font-weight: bold; color: white; display:flex; align-items:center; justify-content:center; background-color: #ef3030ff; gap: 8px;">
                    <img src="/WebBanSach/assets/images/icon/gg.png" alt="Google"
                        style="width: 24px; height: 24px; object-fit: contain; background: white; border-radius: 50%; padding: 2px;">
                    Đăng nhập với Google
                </a>

                <a href="<?= $facebook_login_url ?>" class="btn btn-primary"
                    style="text-align:center; text-decoration:none; font-weight: bold; color: white; display:flex; align-items:center; justify-content:center; background-color: #3e24eaff; gap: 8px;">
                    <img src="/WebBanSach/assets/images/icon/fb.png" alt="Facebook"
                        style="width: 24px; height: 24px; object-fit: contain;">
                    Đăng nhập với Facebook
                </a>
            </div>
        </form>
        <div style="text-align:center; margin-top:12px">
            <a href="/WebBanSach/auth/pages/Forgetpassword/index.php">Quên mật khẩu?</a>
        </div>
    </div>
</body>

</html>