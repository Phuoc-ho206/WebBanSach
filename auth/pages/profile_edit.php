<?php
require_once '../../includes/config.php';

if (!isset($_SESSION['profile'])) {
    $_SESSION['profile'] = [
        'username' => 'nguyenvana',
        'full_name' => 'Nguyễn Văn A',
        'email' => 'nguyenvana@email.com',
        'phone' => '090 123 4567',
        'address' => '123 Đường ABC, Quận 1, TP. Hồ Chí Minh'
    ];
}
$profile = $_SESSION['profile'];
$pageTitle = 'Chỉnh sửa thông tin';
include '../../includes/header.php';
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

    .edit-card {
        background-color: var(--color-surface);
        border: var(--border-width) solid var(--color-border);
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow-sm);
        padding: var(--spacing-lg);
        max-width: 680px;
        margin: 0 auto;
    }

    .edit-card-title {
        font-size: var(--font-size-lg);
        font-weight: var(--font-weight-bold);
        color: var(--color-text);
        margin: 0 0 var(--spacing-lg) 0;
        padding-bottom: var(--spacing-sm);
        border-bottom: 2px solid var(--color-background);
    }

    .section-title {
        margin: var(--spacing-lg) 0 var(--spacing-sm);
        font-size: var(--font-size-md);
        font-weight: var(--font-weight-bold);
        color: var(--color-primary);
    }

    .section-title:first-of-type {
        margin-top: 0;
    }

    .form-group {
        margin-bottom: var(--spacing-md);
    }

    label {
        display: block;
        margin-bottom: var(--spacing-xs);
        font-size: var(--font-size-sm);
        font-weight: var(--font-weight-bold);
        color: var(--color-text);
    }

    input[type="text"],
    input[type="email"],
    input[type="tel"],
    input[type="password"],
    textarea {
        width: 100%;
        padding: 10px var(--spacing-sm);
        box-sizing: border-box;
        border: var(--border-width) solid var(--color-border);
        border-radius: var(--border-radius-sm);
        font-family: inherit;
        font-size: var(--font-size-md);
        transition: border-color var(--transition-fast);
    }

    input:focus,
    textarea:focus {
        outline: none;
        border-color: var(--color-primary);
    }

    textarea {
        min-height: 80px;
        resize: vertical;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--spacing-md);
    }

    @media (max-width: 480px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }

    .actions-panel {
        display: flex;
        gap: var(--spacing-md);
        margin-top: var(--spacing-lg);
        padding-top: var(--spacing-md);
        border-top: 1px solid var(--color-background);
    }

    /* Breadcrumbs */
    .breadcrumbs {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: var(--spacing-xs);
        font-size: var(--font-size-sm);
        color: var(--color-text-light);
        margin-bottom: var(--spacing-lg);
        list-style: none;
        padding: 0;
    }

    .breadcrumbs li {
        display: flex;
        align-items: center;
        gap: var(--spacing-xs);
    }

    .breadcrumbs a {
        color: var(--color-text-light);
        text-decoration: none;
        transition: color var(--transition-fast);
    }

    .breadcrumbs a:hover {
        color: var(--color-primary);
    }

    .breadcrumbs li:not(:last-child)::after {
        content: "/";
        color: var(--color-border);
        margin-left: var(--spacing-xs);
    }
</style>

<!-- Header / Navbar -->
<header class="navbar">
    <a href="<?= url('/') ?>" class="navbar__brand">
        <span style="color: var(--color-primary); font-size: 1.8rem; font-weight: 800;">📚 WebBanSach</span>
    </a>
    <button class="navbar__toggle" aria-label="Toggle Navigation">☰</button>
    <ul class="navbar__menu">
        <li class="navbar__item"><a href="<?= url('/') ?>" class="navbar__link">Trang chủ</a></li>
        <li class="navbar__item"><a href="#" class="navbar__link">Cửa hàng</a></li>
        <li class="navbar__item"><a href="#" class="navbar__link">Tin tức</a></li>
        <li class="navbar__item"><a href="#" class="navbar__link">Giới thiệu</a></li>
        <li class="navbar__item"><a href="#" class="navbar__link">Liên hệ</a></li>
    </ul>
    <div class="navbar__actions">
        <a href="#" class="btn btn--ghost btn--sm" style="position: relative;">
            🛒 Giỏ hàng
            <span class="badge badge--primary" style="position: absolute; top: -5px; right: -5px;">3</span>
        </a>
        <a href="<?= url('auth/pages/profile.php') ?>" class="btn btn--primary btn--sm">Tài khoản</a>
    </div>
</header>

<main class="profile-container">
    <!-- Breadcrumbs -->
    <ul class="breadcrumbs">
        <li><a href="<?= url('/') ?>">Trang chủ</a></li>
        <li><a href="profile.php">Tài khoản</a></li>
        <li>Chỉnh sửa thông tin</li>
    </ul>

    <div class="edit-card">
        <h1 class="edit-card-title">✏️ Chỉnh sửa hồ sơ cá nhân</h1>

        <form action="profile.php" method="POST">
            <!-- Section 1 -->
            <h3 class="section-title">Thông tin cá nhân</h3>
            <div class="form-group">
                <label for="username">Tên đăng nhập</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($profile['username']) ?>" required>
            </div>
            <div class="form-group">
                <label for="full_name">Họ và tên</label>
                <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($profile['full_name']) ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">Địa chỉ Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($profile['email']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Số điện thoại</label>
                    <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($profile['phone']) ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="address">Địa chỉ giao hàng mặc định</label>
                <textarea id="address" name="address"
                    placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành..."><?= htmlspecialchars($profile['address']) ?></textarea>
            </div>

            <!-- Section 2 -->
            <h3 class="section-title">🔒 Bảo mật & Đổi mật khẩu</h3>
            <div class="form-group">
                <label for="current_password">Mật khẩu hiện tại</label>
                <input type="password" id="current_password" name="current_password" autocomplete="current-password"
                    placeholder="Nhập mật khẩu hiện tại nếu muốn đổi mật khẩu">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="new_password">Mật khẩu mới</label>
                    <input type="password" id="new_password" name="new_password" autocomplete="new-password"
                        placeholder="Mật khẩu mới">
                </div>
                <div class="form-group">
                    <label for="confirm_password">Xác nhận mật khẩu</label>
                    <input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password"
                        placeholder="Xác nhận mật khẩu mới">
                </div>
            </div>

            <div class="actions-panel">
                <button type="submit" class="btn btn--primary">
                    💾 Lưu thay đổi
                </button>
                <a href="profile.php" class="btn btn--outline">
                    ❌ Hủy bỏ
                </a>
            </div>
        </form>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>

<script>
    document.querySelector('.navbar__toggle').addEventListener('click', function () {
        document.querySelector('.navbar').classList.toggle('is-open');
    });
</script>
</body>

</html>