<?php
session_start();
require_once '../controller/authcontroller.php';
require_once '../controller/GoogleAuthController.php';
// require_once '../controller/fbauthcontroller.php';

// Xử lý logout
if (isset($_POST['action']) && $_POST['action'] === 'logout') {
    AuthController::logout();
    header('Location: /WebBanSach/index.php');
    exit;
}

// Nếu đã đăng nhập → chuyển theo vai trò
if (isset($_SESSION['user'])) {
    header('Location: ' . AuthController::getRedirectUrl($_SESSION['user']));
    exit;
}

// Xử lý đăng nhập bằng form
$error = '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Vui lòng nhập đầy đủ thông tin đăng nhập.';
    } else {
        $user = AuthController::login($username, $password);
        if ($user) {
            $remember = isset($_POST['remember']) && $_POST['remember'] === 'on';
            AuthController::establishSession($user, $remember);
            
            // Đồng bộ giỏ hàng khi đăng nhập thành công (từ nhánh Payment-cart)
            require_once '../../config/db.php';
            sync_cart_to_db($conn, $user['id']);
            write_user_log($conn, "Đăng nhập hệ thống");
            
            header('Location: ' . AuthController::getRedirectUrl($user));
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
        AuthController::establishSession($user, false);
        
        // Đồng bộ giỏ hàng khi đăng nhập bằng Google thành công (từ nhánh Payment-cart)
        require_once '../../config/db.php';
        sync_cart_to_db($conn, $user['id']);
        write_user_log($conn, "Đăng nhập hệ thống bằng Google");
        
        header('Location: ' . AuthController::getRedirectUrl($user));
        exit;
    } else {
        $error = 'Đăng nhập bằng tài khoản Google thất bại. Vui lòng kiểm tra lại cấu hình hoặc thử lại.';
    }
}

$google_login_url = googleauthcontroller::getLoginUrl();
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
            background: var(--color-background) url('/WebBanSach/assets/images/uploads/background_login.jpg') no-repeat center center fixed;
            background-size: cover;
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
        <form action="/WebBanSach/auth/pages/login.php" method="POST">
            <div class="form-group">
                <label for="username">Tên đăng nhập hoặc Email</label>
                <input id="username" name="username" type="text" required placeholder="Tên đăng nhập hoặc email">
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <div class="input-row" style="position: relative;">
                    <input id="password" name="password" type="password" required placeholder="Mật khẩu" style="padding-right: 40px;">
                    <button type="button" class="toggle-password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--color-text-light); display: flex; align-items: center; justify-content: center; z-index: 10; padding: 0;" aria-label="Hiện mật khẩu">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
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
            </div>
        </form>
        <div style="text-align:center; margin-top:12px">
            <a href="/WebBanSach/auth/pages/Forgetpassword/index.php">Quên mật khẩu?</a>
        </div>
    </div>
    <script>
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const icon = this.querySelector('svg');
            if (input.type === 'password') {
                input.type = 'text';
                this.setAttribute('aria-label', 'Ẩn mật khẩu');
                icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
            } else {
                input.type = 'password';
                this.setAttribute('aria-label', 'Hiện mật khẩu');
                icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
            }
        });
    });
    </script>
</body>

</html>