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

        /* Steps */
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

        .step.done {
            background: var(--color-success, #4caf50);
            color: #fff
        }

        .step-divider {
            align-self: center;
            height: 2px;
            width: 24px;
            background: #e0e0e0
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

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb
        }
    </style>
</head>

<body>
    <div class="card auth-card">
        <h1>Quên mật khẩu</h1>

        <!-- Step indicators -->
        <div class="steps">
            <div class="step active">1</div>
            <div class="step-divider"></div>
            <div class="step">2</div>
            <div class="step-divider"></div>
            <div class="step">3</div>
        </div>

        <!-- Bước 1: Nhập email -->
        <p class="subtitle">Nhập email để nhận mã OTP</p>

        <form method="POST" action="/WebBanSach/auth/pages/Forgetpassword/verifyotp.php">
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" placeholder="example@email.com" required>
            </div>
            <div class="actions">
                <button type="submit" class="btn btn-primary">Gửi mã OTP</button>
                <a class="btn btn-secondary" href="/WebBanSach/auth/pages/login.php">Quay lại</a>
            </div>
        </form>
    </div>
</body>

</html>