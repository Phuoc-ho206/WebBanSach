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
    :root {
        --profile-primary: #ff6b1a;
        --profile-primary-hover: #e05600;
        --profile-bg: #f8f9fa;
        --profile-card-bg: #ffffff;
        --profile-text: #2d3748;
        --profile-text-muted: #718096;
        --profile-border: #e2e8f0;
        --profile-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        --profile-radius: 16px;
    }

    body {
        background-color: var(--profile-bg);
        color: var(--profile-text);
        font-family: var(--font-family-base), system-ui, -apple-system, sans-serif;
    }

    .profile-page-container {
        max-width: 1200px;
        margin: 40px auto 60px;
        padding: 0 20px;
        box-sizing: border-box;
    }

    .profile-layout {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 30px;
        align-items: start;
    }

    /* Left Sidebar */
    .profile-sidebar {
        background: var(--profile-card-bg);
        border-radius: var(--profile-radius);
        box-shadow: var(--profile-shadow);
        padding: 30px 24px;
        border: 1px solid var(--profile-border);
        text-align: center;
    }

    .profile-avatar-container {
        position: relative;
        width: 120px;
        height: 120px;
        margin: 0 auto 20px;
    }

    .profile-avatar-large {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--profile-primary), #ff9f68);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.8rem;
        font-weight: 700;
        box-shadow: 0 8px 20px rgba(255, 107, 26, 0.25);
        border: 4px solid #fff;
    }

    .profile-user-info h2 {
        margin: 0 0 6px;
        font-size: 1.35rem;
        font-weight: 700;
        color: var(--profile-text);
    }

    .profile-user-info p {
        margin: 0 0 24px;
        font-size: 0.9rem;
        color: var(--profile-text-muted);
        word-break: break-all;
    }

    .profile-nav-menu {
        list-style: none;
        padding: 0;
        margin: 0;
        text-align: left;
        border-top: 1px solid var(--profile-border);
        padding-top: 20px;
    }

    .profile-nav-item a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        color: var(--profile-text);
        text-decoration: none;
        font-weight: 500;
        font-size: 0.95rem;
        border-radius: 10px;
        transition: all 0.2s ease;
        margin-bottom: 8px;
    }

    .profile-nav-item a:hover {
        background-color: #fff0e6;
        color: var(--profile-primary);
    }

    .profile-nav-item.active a {
        background-color: var(--profile-primary);
        color: #ffffff;
    }

    .profile-nav-item.logout a {
        color: #e53e3e;
        border-top: 1px solid var(--profile-border);
        margin-top: 15px;
        padding-top: 15px;
        border-radius: 0;
    }

    .profile-nav-item.logout a:hover {
        background-color: #fff5f5;
        color: #c53030;
        border-radius: 10px;
    }

    /* Right Content */
    .profile-content-area {
        display: flex;
        flex-direction: column;
        gap: 30px;
    }

    .profile-card {
        background: var(--profile-card-bg);
        border-radius: var(--profile-radius);
        box-shadow: var(--profile-shadow);
        padding: 35px;
        border: 1px solid var(--profile-border);
    }

    .profile-card-title {
        margin: 0 0 25px;
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--profile-text);
        border-bottom: 2px solid var(--profile-border);
        padding-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .profile-card-title::before {
        content: '';
        display: inline-block;
        width: 4px;
        height: 20px;
        background-color: var(--profile-primary);
        border-radius: 2px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group-full {
        grid-column: span 2;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group label {
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--profile-text);
    }

    .form-control {
        padding: 12px 16px;
        border: 1px solid var(--profile-border);
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        background-color: #fff;
        color: var(--profile-text);
        width: 100%;
        box-sizing: border-box;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--profile-primary);
        box-shadow: 0 0 0 3px rgba(255, 107, 26, 0.15);
    }

    .form-control:disabled {
        background-color: #f7fafc;
        color: var(--profile-text-muted);
        cursor: not-allowed;
    }

    textarea.form-control {
        min-height: 100px;
        resize: vertical;
    }

    /* Buttons */
    .profile-btn-container {
        margin-top: 15px;
        display: flex;
        justify-content: flex-end;
    }

    .btn-save {
        background: linear-gradient(135deg, var(--profile-primary), #ff8c42);
        color: #ffffff;
        border: none;
        padding: 12px 30px;
        font-size: 1rem;
        font-weight: 600;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(255, 107, 26, 0.2);
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 107, 26, 0.3);
    }

    .btn-save:active {
        transform: translateY(0);
    }

    /* Alerts */
    .alert-box {
        padding: 14px 20px;
        border-radius: 8px;
        margin-bottom: 25px;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 500;
    }

    .alert-box-error {
        background-color: #fff5f5;
        color: #c53030;
        border: 1px solid #fed7d7;
    }

    .alert-box-success {
        background-color: #f0fff4;
        color: #2f855a;
        border: 1px solid #c6f6d5;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .profile-layout {
            grid-template-columns: 1fr;
        }

        .profile-sidebar {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .profile-nav-menu {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .profile-nav-item a {
            margin-bottom: 0;
        }

        .profile-nav-item.logout {
            border-top: none;
            margin-top: 0;
            padding-top: 0;
        }
    }

    @media (max-width: 576px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-group-full {
            grid-column: span 1;
        }

        .profile-card {
            padding: 20px;
        }
    }
</style>

<main>
    <div class="profile-page-container">

        <?php if ($error): ?>
            <div class="alert-box alert-box-error">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert-box alert-box-success">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <div class="profile-layout">

            <!-- Left Sidebar -->
            <aside class="profile-sidebar">
                <div class="profile-avatar-container">
                    <div class="profile-avatar-large">
                        <?php echo strtoupper(substr($userData['full_name'] ?? 'U', 0, 1)); ?>
                    </div>
                </div>
                <div class="profile-user-info">
                    <h2><?php echo htmlspecialchars($userData['full_name'] ?? 'User'); ?></h2>
                    <p><?php echo htmlspecialchars($userData['email'] ?? ''); ?></p>
                </div>

                <ul class="profile-nav-menu">
                    <li class="profile-nav-item active">
                        <a href="#">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            Thông tin cá nhân
                        </a>
                    </li>
                    <li class="profile-nav-item">
                        <a href="/WebBanSach/cart/history.php">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                <line x1="3" y1="6" x2="21" y2="6"></line>
                                <path d="M16 10a4 4 0 0 1-8 0"></path>
                            </svg>
                            Đơn hàng của tôi
                        </a>
                    </li>
                    <li class="profile-nav-item logout">
                        <a href="#"
                            onclick="event.preventDefault(); document.getElementById('logout-form-profile').submit();">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                            Đăng xuất
                        </a>
                    </li>
                </ul>

                <form id="logout-form-profile" action="/WebBanSach/auth/pages/login.php" method="POST"
                    style="display: none;">
                    <input type="hidden" name="action" value="logout">
                </form>
            </aside>

            <!-- Right Content Area -->
            <div class="profile-content-area">

                <form action="/WebBanSach/auth/pages/profile.php" method="POST">

                    <!-- Personal Info Card -->
                    <section class="profile-card">
                        <h2 class="profile-card-title">Thông tin cá nhân</h2>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="username">Tên đăng nhập (Email)</label>
                                <input type="text" id="username" name="username" class="form-control"
                                    value="<?php echo htmlspecialchars($userData['username'] ?? ''); ?>" required
                                    readonly>
                            </div>

                            <div class="form-group">
                                <label for="full_name">Họ và tên</label>
                                <input type="text" id="full_name" name="full_name" class="form-control"
                                    value="<?php echo htmlspecialchars($userData['full_name'] ?? ''); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="email">Email liên hệ</label>
                                <input type="email" id="email" name="email" class="form-control"
                                    value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="phone">Số điện thoại</label>
                                <input type="tel" id="phone" name="phone" class="form-control"
                                    value="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>">
                            </div>

                            <div class="form-group form-group-full">
                                <label for="address">Địa chỉ giao hàng</label>
                                <textarea id="address" name="address" class="form-control"
                                    placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố"><?php echo htmlspecialchars($userData['address'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </section>

                    <!-- Change Password Card -->
                    <section class="profile-card">
                        <h2 class="profile-card-title">Đổi mật khẩu</h2>

                        <div class="form-grid">
                            <div class="form-group form-group-full">
                                <label for="current_password">Mật khẩu hiện tại</label>
                                <div class="password-wrapper" style="position: relative;">
                                    <input type="password" id="current_password" name="current_password"
                                        class="form-control" autocomplete="current-password"
                                        placeholder="Nhập mật khẩu hiện tại nếu muốn đổi mật khẩu" style="padding-right: 40px;">
                                    <button type="button" class="toggle-password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--color-text-light); display: flex; align-items: center; justify-content: center; z-index: 10; padding: 0;" aria-label="Hiện mật khẩu">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="new_password">Mật khẩu mới</label>
                                <div class="password-wrapper" style="position: relative;">
                                    <input type="password" id="new_password" name="new_password" class="form-control"
                                        autocomplete="new-password" placeholder="Tối thiểu 8 ký tự" style="padding-right: 40px;">
                                    <button type="button" class="toggle-password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--color-text-light); display: flex; align-items: center; justify-content: center; z-index: 10; padding: 0;" aria-label="Hiện mật khẩu">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="confirm_password">Xác nhận mật khẩu mới</label>
                                <div class="password-wrapper" style="position: relative;">
                                    <input type="password" id="confirm_password" name="confirm_password"
                                        class="form-control" autocomplete="new-password"
                                        placeholder="Nhập lại mật khẩu mới" style="padding-right: 40px;">
                                    <button type="button" class="toggle-password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--color-text-light); display: flex; align-items: center; justify-content: center; z-index: 10; padding: 0;" aria-label="Hiện mật khẩu">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </section>

                    <div class="profile-btn-container">
                        <button type="submit" class="btn-save">Lưu thay đổi</button>
                    </div>

                </form>

            </div>

        </div>
    </div>
</main>

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
<?php include '../../includes/footer.php'; ?>