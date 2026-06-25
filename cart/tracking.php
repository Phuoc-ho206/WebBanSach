<?php
require_once '../includes/config.php';
$pageTitle = 'Theo dõi đơn hàng #WBS-954021';
$extraCss = ['css/cart.css'];
include '../includes/header.php';
?>

<!-- Header / Navbar
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
</header> -->

<!-- Main Content -->
<main class="order-container">
    <!-- Breadcrumbs -->
    <ul class="breadcrumbs">
        <li><a href="<?= url('/') ?>">Trang chủ</a></li>
        <li><a href="<?= url('auth/pages/profile.php') ?>">Tài khoản</a></li>
        <li><a href="history.php">Lịch sử đơn hàng</a></li>
        <li>Theo dõi đơn hàng #WBS-954021</li>
    </ul>

    <!-- Title Section -->
    <div class="order-title-section">
        <h1 class="order-title">Theo dõi hành trình đơn hàng</h1>
    </div>

    <!-- Shipping Info Bar -->
    <div class="tracking-info-header">
        <div class="tracking-info-grid">
            <div>
                <div class="tracking-info-item__label">Đơn vị vận chuyển</div>
                <div class="tracking-info-item__value">GHN Express (Giao Hàng Nhanh)</div>
            </div>
            <div>
                <div class="tracking-info-item__label">Mã vận đơn</div>
                <div class="tracking-info-item__value" style="color: var(--color-primary); font-family: monospace;">
                    GHN-492104820</div>
            </div>
            <div>
                <div class="tracking-info-item__label">Thời gian giao dự kiến</div>
                <div class="tracking-info-item__value">09/06/2026 (Ngày mai)</div>
            </div>
        </div>
        <div>
            <span class="badge badge--info badge--dot" style="font-size: var(--font-size-sm);">Đang vận chuyển</span>
        </div>
    </div>

    <!-- Detailed Journey Timeline -->
    <div class="timeline-card">
        <h2
            style="font-size: var(--font-size-lg); margin-top: 0; margin-bottom: var(--spacing-lg); font-weight: var(--font-weight-bold); border-bottom: 2px solid var(--color-background); padding-bottom: var(--spacing-sm);">
            📍 Trạng thái vận chuyển chi tiết
        </h2>

        <div class="timeline">

            <!-- Step 6: Delivered (Inactive) -->
            <div class="timeline-item">
                <div class="timeline-badge">✓</div>
                <div class="timeline-content">
                    <div class="timeline-header">
                        <h3 class="timeline-title">Giao hàng thành công</h3>
                        <span class="timeline-time">Dự kiến: 09/06/2026</span>
                    </div>
                    <p class="timeline-desc">Đơn hàng sẽ được giao thành công tới người mua Nguyễn Văn A.</p>
                </div>
            </div>

            <!-- Step 5: Out for delivery (Inactive) -->
            <div class="timeline-item">
                <div class="timeline-badge">🚚</div>
                <div class="timeline-content">
                    <div class="timeline-header">
                        <h3 class="timeline-title">Shipper đang giao hàng</h3>
                        <span class="timeline-time">Dự kiến: 09/06/2026</span>
                    </div>
                    <p class="timeline-desc">Nhân viên giao hàng sẽ nhận hàng từ bưu cục phát và tiến hành liên hệ, giao
                        đến tay khách hàng.</p>
                </div>
            </div>

            <!-- Step 4: In Transit (Active) -->
            <div class="timeline-item is-active">
                <div class="timeline-badge">●</div>
                <div class="timeline-content">
                    <div class="timeline-header">
                        <h3 class="timeline-title">Đơn hàng đang trung chuyển</h3>
                        <span class="timeline-time">08/06/2026 08:30</span>
                    </div>
                    <p class="timeline-desc">Hàng đã rời bưu cục trung chuyển Quận 1 SOC, đang được trung chuyển tới bưu
                        cục phát gần địa chỉ giao hàng.</p>
                </div>
            </div>

            <!-- Step 3: Arrived at Sorting hub (Completed) -->
            <div class="timeline-item is-completed">
                <div class="timeline-badge">✓</div>
                <div class="timeline-content">
                    <div class="timeline-header">
                        <h3 class="timeline-title">Đã nhập kho phân loại hàng</h3>
                        <span class="timeline-time">07/06/2026 18:20</span>
                    </div>
                    <p class="timeline-desc">Đơn hàng được tiếp nhận tại bưu cục gốc Quận 1 SOC và phân loại thành công.
                    </p>
                </div>
            </div>

            <!-- Step 2: Collected by Courier (Completed) -->
            <div class="timeline-item is-completed">
                <div class="timeline-badge">✓</div>
                <div class="timeline-content">
                    <div class="timeline-header">
                        <h3 class="timeline-title">Nhân viên vận chuyển đã lấy hàng</h3>
                        <span class="timeline-time">07/06/2026 16:00</span>
                    </div>
                    <p class="timeline-desc">Shipper GHN đã đến cửa hàng lấy gói sản phẩm và cập nhật trạng thái lên hệ
                        thống.</p>
                </div>
            </div>

            <!-- Step 1: Placed (Completed) -->
            <div class="timeline-item is-completed">
                <div class="timeline-badge">✓</div>
                <div class="timeline-content">
                    <div class="timeline-header">
                        <h3 class="timeline-title">Đặt hàng thành công</h3>
                        <span class="timeline-time">07/06/2026 14:30</span>
                    </div>
                    <p class="timeline-desc">Đơn hàng #WBS-954021 được ghi nhận thành công trên hệ thống. Phương thức
                        thanh toán được chọn là COD.</p>
                </div>
            </div>

        </div>

        <!-- Page actions -->
        <div
            style="display: flex; gap: var(--spacing-md); justify-content: center; margin-top: var(--spacing-xl); border-top: 1px solid var(--color-border); padding-top: var(--spacing-lg);">
            <a href="detail.php" class="btn btn--outline">
                📄 Xem chi tiết đơn hàng
            </a>
            <a href="history.php" class="btn btn--ghost">
                ← Quay lại danh sách đơn hàng
            </a>
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