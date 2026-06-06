<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - Bước 2</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f5f8fa;
            font-family: Arial, sans-serif;
            margin: 0;
        }

        .auth-card {
            width: 420px;
            background: #ffffff;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 18px 40px rgba(50, 50, 93, 0.08);
            border: 1px solid #e8e8e8;
        }

        h1 {
            text-align: center;
            color: #ff7a3d;
            margin-bottom: 20px;
        }

        .steps {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        .step {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 700;
            background: #e6e6e6;
            color: #555;
        }

        .step.done {
            background: #4caf50;
            color: #fff;
        }

        .step.active {
            background: #ff7a3d;
            color: #fff;
        }

        .step-divider {
            width: 30px;
            height: 2px;
            background: #e8e8e8;
        }

        .subtitle {
            text-align: center;
            color: #6f6f6f;
            margin-bottom: 24px;
        }

        .otp-inputs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .otp-inputs input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 22px;
            border: 2px solid #e8e8e8;
            border-radius: 10px;
            transition: border-color 0.2s ease;
        }

        .otp-inputs input:focus {
            outline: none;
            border-color: #ff7a3d;
            box-shadow: 0 0 0 3px rgba(255, 122, 61, 0.12);
        }

        .resend-link {
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
            color: #5d5d5d;
        }

        .resend-link a {
            color: #ff7a3d;
            text-decoration: none;
        }

        .resend-link a:hover {
            text-decoration: underline;
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
            transition: filter 0.2s ease, transform 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: #ff7a3d;
            color: #ffffff;
        }

        .btn-primary:hover {
            filter: brightness(0.95);
        }

        .btn-secondary {
            background: #4a9b7f;
            color: #ffffff;
        }

        .btn-secondary:hover {
            filter: brightness(0.92);
        }
    </style>
</head>

<body>

    <div class="auth-card">

        <h1>Quên mật khẩu</h1>

        <div class="steps">
            <div class="step done">1</div>
            <div class="step-divider"></div>
            <div class="step active">2</div>
            <div class="step-divider"></div>
            <div class="step">3</div>
        </div>

        <p class="subtitle">
            Nhập mã OTP đã được gửi đến email của bạn
        </p>

        <p style="text-align:center; color:#555; margin-bottom:16px;">
            OTP ví dụ đã được cài sẵn: <strong>123456</strong><br>
            Nhập OTP này rồi nhấn Xác nhận để chuyển sang màn hình đặt lại mật khẩu.
        </p>

        <form action="reset.php" method="GET">
            <div class="otp-inputs">
                <input type="text" maxlength="1" name="otp1" value="1">
                <input type="text" maxlength="1" name="otp2" value="2">
                <input type="text" maxlength="1" name="otp3" value="3">
                <input type="text" maxlength="1" name="otp4" value="4">
                <input type="text" maxlength="1" name="otp5" value="5">
                <input type="text" maxlength="1" name="otp6" value="6">
            </div>

            <div class="resend-link">
                Không nhận được mã?
                <a href="#">Gửi lại OTP</a>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">Xác nhận</button>
                <a class="btn btn-secondary" href="/WebBanSach/auth/pages/login.php">Quay lại</a>
            </div>
        </form>

    </div>

</body>

</html>