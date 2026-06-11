<?php
require_once '../../includes/config.php';
$pageTitle = 'Hồ sơ cá nhân';
include '../../includes/header.php';
// if (!isset($_SESSION['profile'])) {
//     $_SESSION['profile'] = [
//         'username' => 'nguyenvana',
//         'full_name' => 'Nguyễn Văn A',
//         'email' => 'nguyenvana@email.com',
//         'phone' => '090 123 4567',
//         'address' => '123 Đường ABC, Quận 1, TP. Hồ Chí Minh'
//     ];
// }

// $isUpdated = false;
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $_SESSION['profile']['username'] = $_POST['username'] ?? $_SESSION['profile']['username'];
//     $_SESSION['profile']['full_name'] = $_POST['full_name'] ?? $_SESSION['profile']['full_name'];
//     $_SESSION['profile']['email'] = $_POST['email'] ?? $_SESSION['profile']['email'];
//     $_SESSION['profile']['phone'] = $_POST['phone'] ?? $_SESSION['profile']['phone'];
//     $_SESSION['profile']['address'] = $_POST['address'] ?? $_SESSION['profile']['address'];
//     $isUpdated = true;
// }

// $profile = $_SESSION['profile'];

// // Tính từ viết tắt từ họ và tên
// $words = explode(' ', $profile['full_name']);
// $initials = '';
// if (count($words) >= 2) {
//     $initials = mb_substr($words[0], 0, 1) . mb_substr(end($words), 0, 1);
// } else {
//     $initials = mb_substr($profile['full_name'], 0, 2);
// }
// $initials = mb_strtoupper($initials);
?>

<style>
    body {
        margin: 0;
        background-color: var(--color-background);
        font-family: var(--font-family-base);
        color: var(--color-text);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    main {
        flex: 1;
    }

    .profile-container {
        max-width: 1200px;
        margin: var(--spacing-lg) auto var(--spacing-xl);
        padding: 0 var(--spacing-md);
        box-sizing: border-box;
    }

    .profile-card h1 {
        margin: 0 0 8px;
        font-size: 1.4rem;
        color: var(--color-primary);
        text-align: center;
    }

    .profile-card>p {
        margin: 0 0 20px;
        text-align: center;
        color: var(--color-text-light);
    }

    .profile-header {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        margin-bottom: 24px;
        padding-bottom: 20px;
        border-bottom: var(--border-width) solid var(--color-border);
    }

    .profile-avatar {
        width: 96px;
        height: 96px;
        border-radius: 50%;
        background: var(--color-primary);
        color: var(--color-surface);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: var(--font-weight-bold);
    }

    .profile-name {
        margin: 0;
        font-size: var(--font-size-lg);
        font-weight: var(--font-weight-bold);
        color: var(--color-text);
    }

    .profile-email {
        margin: 0;
        font-size: var(--font-size-sm);
        color: var(--color-text-light);
    }

    .section-title {
        margin: 0 0 12px;
        font-size: var(--font-size-md);
        font-weight: var(--font-weight-bold);
        color: var(--color-primary);
    }

    .profile-section {
        margin-bottom: 24px;
    }

    .form-group {
        margin-bottom: 12px;
    }

    label {
        display: block;
        margin-bottom: 6px;
        font-size: var(--font-size-sm);
        color: var(--color-text);
    }

    input[type="text"],
    input[type="email"],
    input[type="tel"],
    input[type="password"],
    textarea {
        width: 100%;
        padding: 10px;
        box-sizing: border-box;
        border: var(--border-width) solid var(--color-border);
        border-radius: var(--border-radius-sm);
        font-family: inherit;
        font-size: var(--font-size-md);
    }

    textarea {
        min-height: 80px;
        resize: vertical;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: center;
        margin-top: 12px;
    }

    .profile-links {
        display: flex;
        justify-content: center;
        gap: 16px;
        margin-top: 16px;
        font-size: var(--font-size-sm);
    }

    @media (max-width: 480px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>
</head>

<body>
    <div class="card profile-card">
        <h1>Hồ sơ cá nhân</h1>
        <p>Quản lý thông tin tài khoản của bạn</p>

        <div class="profile-header">
            <div class="profile-avatar" aria-hidden="true">NP</div>
            <h2 class="profile-name">Nguyễn Văn A</h2>
            <p class="profile-email">nguyenvana@email.com</p>
        </div>

        <form action="/WebBanSach/auth/pages/profile.php" method="POST">
            <section class="profile-section">
                <h3 class="section-title">Thông tin cá nhân</h3>
                <div class="form-group">
                    <label for="username">Tên đăng nhập</label>
                    <input type="text" id="username" name="username" value="nguyenvana" required>
                </div>
                <div class="form-group">
                    <label for="full_name">Họ và tên</label>
                    <input type="text" id="full_name" name="full_name" value="Nguyễn Văn A" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="nguyenvana@email.com" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Số điện thoại</label>
                        <input type="tel" id="phone" name="phone" value="0901234567">
                    </div>
                </div>
                <div class="form-group">
                    <label for="address">Địa chỉ giao hàng</label>
                    <textarea id="address" name="address"
                        placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố">123 Đường ABC, Quận 1, TP. Hồ Chí Minh</textarea>
                </div>
            </section>

            <section class="profile-section">
                <h3 class="section-title">Đổi mật khẩu</h3>
                <div class="form-group">
                    <label for="current_password">Mật khẩu hiện tại</label>
                    <input type="password" id="current_password" name="current_password"
                        autocomplete="current-password">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="new_password">Mật khẩu mới</label>
                        <input type="password" id="new_password" name="new_password" autocomplete="new-password">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Xác nhận mật khẩu mới</label>
                        <input type="password" id="confirm_password" name="confirm_password"
                            autocomplete="new-password">
                    </div>
                </div>
            </section>

            <div class="actions">
                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                <a class="btn btn-secondary" href="/WebBanSach/">Về trang chủ</a>
            </div>
        </form>

        <div class="profile-links">
            <a href="/WebBanSach/auth/pages/login.php">Đăng xuất</a>
        </div>
    </div>
</body>

</html>