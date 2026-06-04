<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <link rel="stylesheet" href="/WebBanSach/assets/css/variables.css">
    <link rel="stylesheet" href="/WebBanSach/assets/css/components/button.css">
    <link rel="stylesheet" href="/WebBanSach/assets/css/components/form.css">
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
            font-family: var(--font-family-base);
            background: var(--color-background);
            box-sizing: border-box;
        }

        .auth-card {
            width: 100%;
            max-width: 420px;
            padding: 24px;
            box-sizing: border-box;
        }

        .auth-card h1 {
            margin: 0 0 8px;
            font-size: 1.4rem;
            color: var(--color-primary);
            text-align: center
        }

        .auth-card p {
            margin: 0 0 16px;
            text-align: center;
            color: var(--color-text-light)
        }

        .form-group {
            margin-bottom: 12px
        }

        label {
            display: block;
            margin-bottom: 6px
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            box-sizing: border-box
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
    </style>
</head>

<body>
    <div class="card auth-card">
        <h1>Đăng ký</h1>
        <p>Tạo tài khoản mới của bạn</p>
        <form action="/WebBanSach/auth/pages/register.php" method="POST">
            <div class="form-group">
                <label for="username">Tên đăng nhập</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Xác nhận mật khẩu</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="actions">
                <button type="submit" class="btn btn-primary">Đăng ký</button>
                <a class="btn btn-secondary" href="/WebBanSach/auth/pages/login.php">Quay lại</a>
            </div>
        </form>
        <div style="text-align:center; margin-top:12px">
            <a href="/WebBanSach/auth/pages/Forgetpassword/index.php">Quên mật khẩu?</a>
        </div>
    </div>
</body>

</html>