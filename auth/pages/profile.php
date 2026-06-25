<?php
session_start();

// Kiểm tra đã login chưa
if (!isset($_SESSION['user'])) {
    header('Location: /WebBanSach/auth/pages/login.php');
    exit;
}

require_once '../../config/db.php';
require_once '../../auth/controller/profilecontroller.php';

$pageTitle = 'Hồ sơ cá nhân';
include '../../includes/header.php';

$userId = $_SESSION['user']['id'];
$error = '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);

// Load user data từ DB
$profileController = new ProfileController();
$userData = $profileController->getUserProfile($userId);

// Nếu không load được data từ DB, dùng data từ session
if (!$userData) {
    $userData = $_SESSION['user'];
}

// Xử lý POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'username' => trim($_POST['username'] ?? ''),
        'full_name' => trim($_POST['full_name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
        'current_password' => $_POST['current_password'] ?? '',
        'new_password' => $_POST['new_password'] ?? '',
        'confirm_password' => $_POST['confirm_password'] ?? ''
    ];

    // Kiểm tra xem có đổi mật khẩu không
    if (!empty($data['current_password']) || !empty($data['new_password']) || !empty($data['confirm_password'])) {
        // Có đổi mật khẩu
        $result = $profileController->changePassword(
            $userId,
            $data['current_password'],
            $data['new_password'],
            $data['confirm_password']
        );
    } else {
        // Chỉ cập nhật thông tin cá nhân
        $result = $profileController->updateProfile($userId, $data);
    }

    if ($result['success']) {
        $_SESSION['success'] = $result['message'];
        // Reload user data
        $userData = $profileController->getUserProfile($userId);
        // Update session
        $_SESSION['user'] = array_merge($_SESSION['user'], $userData);
        header('Location: /WebBanSach/auth/pages/profile.php');
        exit;
    } else {
        $error = $result['message'];
    }
}
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

        <?php if ($error): ?>
            <div
                style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 16px; border: 1px solid #f5c6cb;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div
                style="background-color: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 16px; border: 1px solid #c3e6cb;">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <div class="profile-header">
            <div class="profile-avatar" aria-hidden="true">
                <?php echo strtoupper(substr($userData['full_name'] ?? 'U', 0, 2)); ?>
            </div>
            <h2 class="profile-name"><?php echo htmlspecialchars($userData['full_name'] ?? 'User'); ?></h2>
            <p class="profile-email"><?php echo htmlspecialchars($userData['email'] ?? ''); ?></p>
        </div>

        <form action="/WebBanSach/auth/pages/profile.php" method="POST">
            <section class="profile-section">
                <h3 class="section-title">Thông tin cá nhân</h3>
                <div class="form-group">
                    <label for="username">Tên đăng nhập</label>
                    <input type="text" id="username" name="username"
                        value="<?php echo htmlspecialchars($userData['username'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="full_name">Họ và tên</label>
                    <input type="text" id="full_name" name="full_name"
                        value="<?php echo htmlspecialchars($userData['full_name'] ?? ''); ?>" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email"
                            value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Số điện thoại</label>
                        <input type="tel" id="phone" name="phone"
                            value="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="address">Địa chỉ giao hàng</label>
                    <textarea id="address" name="address"
                        placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố"><?php echo htmlspecialchars($userData['address'] ?? ''); ?></textarea>
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
            <a href="/WebBanSach/auth/pages/login.php?action=logout"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Đăng xuất</a>
        </div>

        <form id="logout-form" action="/WebBanSach/auth/pages/login.php" method="POST" style="display: none;">
            <input type="hidden" name="action" value="logout">
        </form>
    </div>
</body>

</html>