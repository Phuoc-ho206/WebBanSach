<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - Bước 2</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f5f5f5;
            font-family: Arial, sans-serif;
            box-sizing: border-box;
        }

        .auth-card {
            width: 420px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #0d6efd;
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
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            background: #ddd;
            color: #666;
        }

        .step.done {
            background: #28a745;
            color: white;
        }

        .step.active {
            background: #0d6efd;
            color: white;
        }

        .step-divider {
            width: 30px;
            height: 2px;
            background: #ddd;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 25px;
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
            border: 2px solid #ddd;
            border-radius: 6px;
        }

        .otp-inputs input:focus {
            outline: none;
            border-color: #0d6efd;
        }

        .resend-link {
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .resend-link a {
            color: #0d6efd;
            text-decoration: none;
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            font-size: 14px;
        }

        .btn-primary {
            background: #0d6efd;
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
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