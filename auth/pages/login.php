<?php
session_start();
require_once '../controller/authcontroller.php';
require_once '../controller/GoogleAuthController.php';
require_once '../controller/fbauthcontroller.php';

// Xử lý logout
if (isset($_POST['action']) && $_POST['action'] === 'logout') {
    AuthController::logout();
    header('Location: /WebBanSach/index.php');
    exit;
}

// Nếu đã đăng nhập → về trang chủ
if (isset($_SESSION['user'])) {
    header('Location: /WebBanSach/index.php');
    exit;
}

// Xử lý đăng nhập bằng form
$error = '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identity = trim($_POST['identity'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($identity) || empty($password)) {
        $error = 'Vui lòng nhập đầy đủ thông tin đăng nhập.';
    } else {
        $user = AuthController::login($identity, $password);
        if ($user) {
            $_SESSION['user'] = $user;
            header('Location: /WebBanSach/index.php');
            exit;
        } else {
            $error = 'Tên đăng nhập hoặc mật khẩu không đúng.';
        }
    }
}

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

        .remember-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember-group input[type="checkbox"] {
            width: 16px;
            height: 16px;
            margin: 0;
            padding: 0;
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
        <?php if ($error): ?>
            <div
                style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 14px; text-align: center; border: 1px solid #f5c6cb;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div
                style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 14px; text-align: center; border: 1px solid #c3e6cb;">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
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
                <button type="submit" class="btn btn-primary"
                    style="display:flex; align-items:center; justify-content:center; font-weight: bold; color: white; background-color: #ff6b1a; border: none; cursor: pointer;">
                    Đăng nhập</button>
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