<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quên mật khẩu</title>
    <link rel="stylesheet" href="/WebBanSach/assets/css/variables.css">
    <link rel="stylesheet" href="/WebBanSach/assets/css/components/form.css">
    <link rel="stylesheet" href="/WebBanSach/assets/css/components/button.css">
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

        .step-panel {
            display: none
        }

        .step-panel.active {
            display: block
        }

        .subtitle {
            text-align: center;
            margin: 0 0 16px;
            color: var(--color-text-light, #666)
        }

        .otp-inputs {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin-bottom: 4px
        }

        .otp-inputs input {
            width: 44px;
            height: 44px;
            text-align: center;
            font-size: 1.2rem;
            padding: 0
        }
    </style>
</head>

<body>
    <div class="card auth-card">
        <h1>Quên mật khẩu</h1>

        <!-- Step indicators -->
        <div class="steps">
            <div class="step active" id="dot-1">1</div>
            <div class="step-divider"></div>
            <div class="step" id="dot-2">2</div>
            <div class="step-divider"></div>
            <div class="step" id="dot-3">3</div>
        </div>

        <!-- Bước 1: Nhập email -->
        <div class="step-panel active" id="panel-1">
            <p class="subtitle">Nhập email để nhận mã OTP</p>
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" placeholder="example@email.com" required>
            </div>
            <div class="actions">
                <button type="button" class="btn btn-primary" onclick="goStep(2)">Gửi mã OTP</button>
                <a class="btn btn-secondary" href="/WebBanSach/auth/login.php">Quay lại</a>
            </div>
        </div>

        <!-- Bước 2: Nhập OTP -->
        <div class="step-panel" id="panel-2">
            <p class="subtitle">Nhập mã OTP đã gửi đến email của bạn</p>
            <div class="otp-inputs">
                <input type="text" maxlength="1" class="otp-digit" inputmode="numeric">
                <input type="text" maxlength="1" class="otp-digit" inputmode="numeric">
                <input type="text" maxlength="1" class="otp-digit" inputmode="numeric">
                <input type="text" maxlength="1" class="otp-digit" inputmode="numeric">
                <input type="text" maxlength="1" class="otp-digit" inputmode="numeric">
                <input type="text" maxlength="1" class="otp-digit" inputmode="numeric">
            </div>
            <p style="text-align:center; font-size:0.85rem; color:#888; margin-top:8px">
                Không nhận được mã? <a href="#" onclick="goStep(1)">Gửi lại</a>
            </p>
            <div class="actions">
                <button type="button" class="btn btn-primary" onclick="goStep(3)">Xác nhận</button>
                <button type="button" class="btn btn-secondary" onclick="goStep(1)">Quay lại</button>
            </div>
        </div>

        <!-- Bước 3: Đặt mật khẩu mới -->
        <div class="step-panel" id="panel-3">
            <p class="subtitle">Tạo mật khẩu mới của bạn</p>
            <form action="/WebBanSach/auth/fogetpassword.php" method="POST">
                <div class="form-group">
                    <label for="new_password">Mật khẩu mới</label>
                    <input id="new_password" name="new_password" type="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Xác nhận mật khẩu</label>
                    <input id="confirm_password" name="confirm_password" type="password" required>
                </div>
                <div class="actions">
                    <button type="submit" class="btn btn-primary">Đặt lại mật khẩu</button>
                    <button type="button" class="btn btn-secondary" onclick="goStep(2)">Quay lại</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Chuyển step
        function goStep(step) {
            document.querySelectorAll('.step-panel').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.step').forEach(d => d.classList.remove('active', 'done'));

            document.getElementById('panel-' + step).classList.add('active');

            for (let i = 1; i <= 3; i++) {
                const dot = document.getElementById('dot-' + i);
                if (i < step) dot.classList.add('done');
                else if (i === step) dot.classList.add('active');
            }
        }

        // Auto focus ô OTP tiếp theo
        document.querySelectorAll('.otp-digit').forEach((input, idx, all) => {
            input.addEventListener('input', () => {
                if (input.value && idx < all.length - 1) all[idx + 1].focus();
            });
            input.addEventListener('keydown', e => {
                if (e.key === 'Backspace' && !input.value && idx > 0) all[idx - 1].focus();
            });
        });
    </script>
</body>

</html>