<?php
require_once '../includes/config.php';
$pageTitle = 'Chi tiết đơn hàng #WBS-954021';
$extraCss = ['css/cart.css'];
include '../includes/header.php';
?>

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

<!-- Main Content -->
<main class="order-container">
    <!-- Breadcrumbs -->
    <ul class="breadcrumbs">
        <li><a href="<?= url('/') ?>">Trang chủ</a></li>
        <li><a href="<?= url('auth/pages/profile.php') ?>">Tài khoản</a></li>
        <li><a href="history.php">Lịch sử đơn hàng</a></li>
        <li>Chi tiết đơn hàng #WBS-954021</li>
    </ul>

    <!-- Title Section -->
    <div class="order-title-section">
        <div>
            <h1 class="order-title">Chi tiết đơn hàng #WBS-954021</h1>
            <p style="color: var(--color-text-light); margin: var(--spacing-xs) 0 0 0; font-size: var(--font-size-sm);">
                Đặt ngày: 07/06/2026 14:30 | Trạng thái: <span class="badge badge--info"
                    style="font-size: 0.7rem; vertical-align: middle;">Đang giao hàng</span>
            </p>
        </div>
        <div>
            <a href="tracking.php" class="btn btn--primary">
                📍 Theo dõi vận chuyển
            </a>
        </div>
    </div>

    <!-- Order Detail Layout -->
    <div class="order-detail-layout">
        <!-- Left Column: Details -->
        <div class="order-detail-main">

            <!-- Section 1: Customer info -->
            <div class="detail-section-card">
                <h2 class="detail-section-title">
                    👤 Thông tin nhận hàng
                </h2>
                <ul class="info-details-list">
                    <li class="info-details-item">
                        <span class="info-details-label">Họ và tên:</span>
                        <span class="info-details-value">Nguyễn Văn A</span>
                    </li>
                    <li class="info-details-item">
                        <span class="info-details-label">Số điện thoại:</span>
                        <span class="info-details-value">0901 234 567</span>
                    </li>
                    <li class="info-details-item">
                        <span class="info-details-label">Địa chỉ nhận hàng:</span>
                        <span class="info-details-value">123 Đường ABC, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh, Việt
                            Nam</span>
                    </li>
                </ul>
            </div>

            <!-- Section 2: Payment and Shipping info -->
            <div class="detail-section-card">
                <h2 class="detail-section-title">
                    💳 Thanh toán & Vận chuyển
                </h2>
                <ul class="info-details-list">
                    <li class="info-details-item">
                        <span class="info-details-label">Phương thức thanh toán:</span>
                        <span class="info-details-value">Thanh toán khi nhận hàng (COD)</span>
                    </li>
                    <li class="info-details-item">
                        <span class="info-details-label">Trạng thái thanh toán:</span>
                        <span class="info-details-value"><span class="badge badge--warning">Chờ thanh toán khi nhận
                                hàng</span></span>
                    </li>
                    <li class="info-details-item">
                        <span class="info-details-label">Đơn vị vận chuyển:</span>
                        <span class="info-details-value">GHN Express (Giao Hàng Nhanh)</span>
                    </li>
                    <li class="info-details-item">
                        <span class="info-details-label">Mã vận đơn:</span>
                        <span class="info-details-value"
                            style="color: var(--color-primary); font-family: monospace; font-size: var(--font-size-md);">GHN-492104820</span>
                    </li>
                </ul>
            </div>

            <!-- Section 3: Ordered Products Table -->
            <div class="detail-section-card">
                <h2 class="detail-section-title">
                    📦 Sản phẩm đã mua
                </h2>
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Đơn giá</th>
                                <th>Số lượng</th>
                                <th style="text-align: right;">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="detail-product-link">
                                        <svg class="detail-product-img" width="50" height="68" viewBox="0 0 64 86"
                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect width="64" height="86" rx="4" fill="url(#paint0_linear_d1)" />
                                            <rect x="5" y="6" width="2" height="74" fill="#fff" opacity="0.3" />
                                            <text x="12" y="32" fill="#fff" font-family="Arial" font-size="5"
                                                font-weight="bold">SÚNG, VI TRÙNG</text>
                                            <text x="12" y="38" fill="#fff" font-family="Arial" font-size="5"
                                                font-weight="bold">VÀ THÉP</text>
                                            <defs>
                                                <linearGradient id="paint0_linear_d1" x1="0" y1="0" x2="64" y2="86"
                                                    gradientUnits="userSpaceOnUse">
                                                    <stop stop-color="#1565c0" />
                                                    <stop offset="1" stop-color="#0d47a1" />
                                                </linearGradient>
                                            </defs>
                                        </svg>
                                        <div>
                                            <div class="detail-product-name">Súng, Vi Trùng Và Thép (Tái Bản 2023)</div>
                                            <div
                                                style="font-size: 0.75rem; color: var(--color-text-light); margin-top: 2px;">
                                                Tác giả: Jared Diamond</div>
                                        </div>
                                    </div>
                                </td>
                                <td>185.000 đ</td>
                                <td>1</td>
                                <td
                                    style="text-align: right; font-weight: var(--font-weight-bold); color: var(--color-text);">
                                    185.000 đ</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Right Column: Summary Card -->
        <div class="order-detail-sidebar">
            <div class="detail-summary-card">
                <h2 class="detail-summary-title">Tóm tắt thanh toán</h2>

                <div class="detail-summary-row">
                    <span>Giá trị sản phẩm</span>
                    <span>185.000 đ</span>
                </div>

                <div class="detail-summary-row">
                    <span>Phí vận chuyển</span>
                    <span>30.000 đ</span>
                </div>

                <div class="detail-summary-row">
                    <span>Voucher giảm giá</span>
                    <span style="color: var(--color-success);">-0 đ</span>
                </div>

                <div class="detail-summary-row detail-summary-row--total">
                    <span>Tổng tiền</span>
                    <span class="detail-summary-value">215.000 đ</span>
                </div>

                <div class="detail-summary-actions">
                    <a href="tracking.php" class="btn btn--primary btn--block">
                        📍 Theo dõi đơn hàng
                    </a>
                    <button class="btn btn--outline btn--block" onclick="alert('Tính năng liên hệ đang phát triển!')">
                        💬 Yêu cầu hỗ trợ
                    </button>
                    <a href="history.php" class="btn btn--ghost btn--block" style="text-align: center;">
                        ← Quay lại lịch sử đơn
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>

<!-- Script to toggle navbar on mobile -->
<script>
    document.querySelector('.navbar__toggle').addEventListener('click', function () {
        document.querySelector('.navbar').classList.toggle('is-open');
    });
</script>
</body>

</html>