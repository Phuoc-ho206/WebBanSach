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

$isUpdated = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['profile']['username'] = $_POST['username'] ?? $_SESSION['profile']['username'];
    $_SESSION['profile']['full_name'] = $_POST['full_name'] ?? $_SESSION['profile']['full_name'];
    $_SESSION['profile']['email'] = $_POST['email'] ?? $_SESSION['profile']['email'];
    $_SESSION['profile']['phone'] = $_POST['phone'] ?? $_SESSION['profile']['phone'];
    $_SESSION['profile']['address'] = $_POST['address'] ?? $_SESSION['profile']['address'];
    $isUpdated = true;
}

$profile = $_SESSION['profile'];

// Tính từ viết tắt từ họ và tên
$words = explode(' ', $profile['full_name']);
$initials = '';
if (count($words) >= 2) {
    $initials = mb_substr($words[0], 0, 1) . mb_substr(end($words), 0, 1);
} else {
    $initials = mb_substr($profile['full_name'], 0, 2);
}
$initials = mb_strtoupper($initials);

$pageTitle = 'Hồ sơ cá nhân';
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

    .profile-layout {
        display: grid;
        grid-template-columns: 1fr 2.5fr;
        gap: var(--spacing-lg);
        align-items: start;
    }

    @media (max-width: 768px) {
        .profile-layout {
            grid-template-columns: 1fr;
        }
    }

    .profile-card {
        background-color: var(--color-surface);
        border: var(--border-width) solid var(--color-border);
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow-sm);
        padding: var(--spacing-lg);
        text-align: center;
    }

    .profile-avatar {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        background: var(--color-primary);
        color: var(--color-surface);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        font-weight: var(--font-weight-bold);
        margin: 0 auto var(--spacing-md);
        box-shadow: var(--box-shadow-md);
    }

    .profile-name {
        font-size: var(--font-size-lg);
        font-weight: var(--font-weight-bold);
        color: var(--color-text);
        margin: 0 0 var(--spacing-xxs) 0;
    }

    .profile-role {
        font-size: var(--font-size-sm);
        color: var(--color-primary);
        font-weight: var(--font-weight-bold);
        margin: 0 0 var(--spacing-md) 0;
    }

    .profile-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--spacing-sm);
        padding-top: var(--spacing-md);
        border-top: 1px solid var(--color-border);
        margin-top: var(--spacing-md);
    }

    .stat-item {
        text-align: center;
    }

    .stat-val {
        font-size: var(--font-size-md);
        font-weight: var(--font-weight-bold);
        color: var(--color-text);
    }

    .stat-label {
        font-size: 0.75rem;
        color: var(--color-text-light);
        margin-top: 2px;
    }

    .detail-card {
        background-color: var(--color-surface);
        border: var(--border-width) solid var(--color-border);
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow-sm);
        padding: var(--spacing-lg);
    }

    .detail-card-title {
        font-size: var(--font-size-lg);
        font-weight: var(--font-weight-bold);
        color: var(--color-text);
        margin: 0 0 var(--spacing-lg) 0;
        padding-bottom: var(--spacing-sm);
        border-bottom: 2px solid var(--color-background);
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
    }

    .detail-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .detail-item {
        display: flex;
        padding: var(--spacing-sm) 0;
        border-bottom: 1px solid var(--color-background);
    }

    .detail-item:last-child {
        border-bottom: none;
    }

    .detail-label {
        width: 180px;
        color: var(--color-text-light);
        font-size: var(--font-size-sm);
        flex-shrink: 0;
    }

    .detail-val {
        color: var(--color-text);
        font-size: var(--font-size-sm);
        font-weight: var(--font-weight-bold);
    }

    .actions-panel {
        display: flex;
        gap: var(--spacing-md);
        flex-wrap: wrap;
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
        <li>Tài khoản</li>
    </ul>

    <!-- Success Toast Alert -->
    <?php if ($isUpdated): ?>
        <div class="alert alert--success" style="margin-bottom: var(--spacing-lg);">
            <span class="alert__icon">✅</span>
            <div class="alert__content">
                <div class="alert__title">Cập nhật thành công!</div>
                <div>Thông tin cá nhân của bạn đã được thay đổi thành công.</div>
            </div>
            <button class="alert__close" onclick="this.parentElement.style.display='none';">×</button>
        </div>
    <?php endif; ?>

    <div class="profile-layout">
        <!-- Left Column: User Summary & Stats -->
        <div class="profile-sidebar">
            <div class="profile-card">
                <div class="profile-avatar"><?= htmlspecialchars($initials) ?></div>
                <h2 class="profile-name"><?= htmlspecialchars($profile['full_name']) ?></h2>
                <div class="profile-role">🥇 Thành viên Vàng</div>
                <p style="font-size: 0.8rem; color: var(--color-text-light); margin: 0 0 var(--spacing-sm);">Tham gia
                    từ: 01/01/2026</p>

                <div class="profile-stats">
                    <div class="stat-item">
                        <div class="stat-val">12</div>
                        <div class="stat-label">Đơn mua</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-val">3</div>
                        <div class="stat-label">Đang giao</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Personal Details & Actions -->
        <div class="profile-main">
            <div class="detail-card">
                <h2 class="detail-card-title">
                    📋 Thông tin cá nhân
                </h2>

                <ul class="detail-list">
                    <li class="detail-item">
                        <span class="detail-label">Tên đăng nhập</span>
                        <span class="detail-val"><?= htmlspecialchars($profile['username']) ?></span>
                    </li>
                    <li class="detail-item">
                        <span class="detail-label">Họ và tên</span>
                        <span class="detail-val"><?= htmlspecialchars($profile['full_name']) ?></span>
                    </li>
                    <li class="detail-item">
                        <span class="detail-label">Địa chỉ Email</span>
                        <span class="detail-val"><?= htmlspecialchars($profile['email']) ?></span>
                    </li>
                    <li class="detail-item">
                        <span class="detail-label">Số điện thoại</span>
                        <span class="detail-val"><?= htmlspecialchars($profile['phone']) ?></span>
                    </li>
                    <li class="detail-item">
                        <span class="detail-label">Địa chỉ giao hàng mặc định</span>
                        <span class="detail-val"><?= htmlspecialchars($profile['address']) ?></span>
                    </li>
                </ul>

                <div class="actions-panel">
                    <a href="profile_edit.php" class="btn btn--primary">
                        ✏️ Chỉnh sửa thông tin
                    </a>
                    <a href="<?= url('cart/history.php') ?>" class="btn btn--outline">
                        📦 Lịch sử đơn hàng
                    </a>
                    <a href="login.php" class="btn btn--ghost" style="color: var(--color-error); margin-left: auto;">
                        🚪 Đăng xuất
                    </a>
                </div>
            </div>
        </div>
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