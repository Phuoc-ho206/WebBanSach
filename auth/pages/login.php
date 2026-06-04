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

        .actions {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin-top: 12px
        }
    </style>
</head>

<body>
    <div class="card auth-card">
        <h1>Đăng nhập</h1>
        <form action="/WebBanSach/auth/pages/login.php" method="POST">
            <div class="form-group">
                <label for="identity">Email hoặc tên đăng nhập</label>
                <input id="identity" name="identity" type="text" required>
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input id="password" name="password" type="password" required>
            </div>
            <div class="remember-group">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Ghi nhớ đăng nhập</label>
            </div>
            <div class="actions">
                <button type="submit" class="btn btn-primary">Đăng nhập</button>
                <a class="btn btn-secondary" href="/WebBanSach/auth/pages/register.php">Đăng ký</a>
            </div>
        </form>
        <div style="text-align:center; margin-top:12px">
            <a href="/WebBanSach/auth/pages/Forgetpassword/index.php">Quên mật khẩu?</a>
        </div>
    </div>
</body>

</html>