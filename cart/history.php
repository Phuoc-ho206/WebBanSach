<?php
require_once '../includes/config.php';
$pageTitle = 'Lịch sử đơn hàng';
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
        <li>Lịch sử đơn hàng</li>
    </ul>

    <!-- Title Section -->
    <div class="order-title-section">
        <h1 class="order-title">Lịch sử đơn hàng</h1>
    </div>

    <!-- Filter Tabs -->
    <div class="order-tabs">
        <a href="?status=all" class="order-tab-item is-active">Tất cả</a>
        <a href="?status=pending_payment" class="order-tab-item">Chờ thanh toán</a>
        <a href="?status=pending" class="order-tab-item">Chờ xác nhận</a>
        <a href="?status=shipping" class="order-tab-item">Đang giao</a>
        <a href="?status=completed" class="order-tab-item">Đã giao</a>
        <a href="?status=cancelled" class="order-tab-item">Đã hủy</a>
    </div>

    <!-- Order List -->
    <div class="order-card-list">

        <!-- Order 1: Delivered -->
        <div class="order-card">
            <div class="order-card__header">
                <div class="order-card__header-left">
                    <span class="order-card__id">Đơn hàng: #WBS-982103</span>
                    <span class="order-card__date">Đặt ngày: 08/06/2026 09:15</span>
                </div>
                <div style="display: flex; gap: 8px;">
                    <span class="badge badge--success badge--dot">Đã giao hàng thành công</span>
                    <span class="badge badge--secondary">Đã thanh toán</span>
                </div>
            </div>
            <div class="order-card__products">
                <!-- Product 1 -->
                <a href="detail.php" class="order-card__product">
                    <svg class="order-card__product-img" width="64" height="86" viewBox="0 0 64 86" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="64" height="86" rx="4" fill="url(#paint0_linear_o1)"/>
                        <rect x="5" y="6" width="2" height="74" fill="#fff" opacity="0.3"/>
                        <text x="12" y="36" fill="#fff" font-family="Arial" font-size="6" font-weight="bold">ĐẮC NHÂN</text>
                        <text x="12" y="44" fill="#fff" font-family="Arial" font-size="6" font-weight="bold">TÂM</text>
                        <defs>
                            <linearGradient id="paint0_linear_o1" x1="0" y1="0" x2="64" y2="86" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#4A9B7F"/>
                                <stop offset="1" stop-color="#2D5F5D"/>
                            </linearGradient>
                        </defs>
                    </svg>
                    <div class="order-card__product-info">
                        <h3 class="order-card__product-title">Đắc Nhân Tâm (Bìa Mềm)</h3>
                        <span class="order-card__product-meta">Tác giả: Dale Carnegie</span>
                    </div>
                    <div class="order-card__product-price">
                        <span class="order-card__product-price-current">86.000 đ</span>
                        <div class="order-card__product-price-qty">x 1</div>
                    </div>
                </a>

                <!-- Product 2 -->
                <a href="detail.php" class="order-card__product">
                    <svg class="order-card__product-img" width="64" height="86" viewBox="0 0 64 86" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="64" height="86" rx="4" fill="url(#paint0_linear_o2)"/>
                        <rect x="5" y="6" width="2" height="74" fill="#fff" opacity="0.3"/>
                        <text x="12" y="36" fill="#fff" font-family="Arial" font-size="6" font-weight="bold">NHÀ GIẢ</text>
                        <text x="12" y="44" fill="#fff" font-family="Arial" font-size="6" font-weight="bold">KIM</text>
                        <defs>
                            <linearGradient id="paint0_linear_o2" x1="0" y1="0" x2="64" y2="86" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#FF7A3D"/>
                                <stop offset="1" stop-color="#E05B1E"/>
                            </linearGradient>
                        </defs>
                    </svg>
                    <div class="order-card__product-info">
                        <h3 class="order-card__product-title">Nhà Giả Kim</h3>
                        <span class="order-card__product-meta">Tác giả: Paulo Coelho</span>
                    </div>
                    <div class="order-card__product-price">
                        <span class="order-card__product-price-current">79.000 đ</span>
                        <div class="order-card__product-price-qty">x 2</div>
                    </div>
                </a>
            </div>
            <div class="order-card__footer">
                <span class="order-card__summary">
                    Tổng số lượng: <strong>3 sản phẩm</strong>
                    <span class="order-card__total-price">Tổng cộng: 244.000 đ</span>
                </span>
                <div class="btn-group">
                    <a href="detail.php" class="btn btn--outline btn--sm">Xem chi tiết</a>
                    <button class="btn btn--primary btn--sm">Mua lại</button>
                </div>
            </div>
        </div>

        <!-- Order 2: Shipping -->
        <div class="order-card">
            <div class="order-card__header">
                <div class="order-card__header-left">
                    <span class="order-card__id">Đơn hàng: #WBS-954021</span>
                    <span class="order-card__date">Đặt ngày: 07/06/2026 14:30</span>
                </div>
                <div style="display: flex; gap: 8px;">
                    <span class="badge badge--info badge--dot">Đang giao hàng</span>
                    <span class="badge badge--warning">Thanh toán COD (Chưa trả)</span>
                </div>
            </div>
            <div class="order-card__products">
                <!-- Product 1 -->
                <a href="detail.php" class="order-card__product">
                    <svg class="order-card__product-img" width="64" height="86" viewBox="0 0 64 86" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="64" height="86" rx="4" fill="url(#paint0_linear_o3)"/>
                        <rect x="5" y="6" width="2" height="74" fill="#fff" opacity="0.3"/>
                        <text x="12" y="32" fill="#fff" font-family="Arial" font-size="5" font-weight="bold">SÚNG, VI TRÙNG</text>
                        <text x="12" y="38" fill="#fff" font-family="Arial" font-size="5" font-weight="bold">VÀ THÉP</text>
                        <defs>
                            <linearGradient id="paint0_linear_o3" x1="0" y1="0" x2="64" y2="86" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#1565c0"/>
                                <stop offset="1" stop-color="#0d47a1"/>
                            </linearGradient>
                        </defs>
                    </svg>
                    <div class="order-card__product-info">
                        <h3 class="order-card__product-title">Súng, Vi Trùng Và Thép</h3>
                        <span class="order-card__product-meta">Tác giả: Jared Diamond</span>
                    </div>
                    <div class="order-card__product-price">
                        <span class="order-card__product-price-current">185.000 đ</span>
                        <div class="order-card__product-price-qty">x 1</div>
                    </div>
                </a>
            </div>
            <div class="order-card__footer">
                <span class="order-card__summary">
                    Tổng số lượng: <strong>1 sản phẩm</strong>
                    <span class="order-card__total-price">Tổng cộng: 215.000 đ</span>
                </span>
                <div class="btn-group">
                    <a href="detail.php" class="btn btn--outline btn--sm">Xem chi tiết</a>
                    <a href="tracking.php" class="btn btn--primary btn--sm">Theo dõi vận chuyển</a>
                </div>
            </div>
        </div>

        <!-- Order 3: Cancelled -->
        <div class="order-card">
            <div class="order-card__header">
                <div class="order-card__header-left">
                    <span class="order-card__id">Đơn hàng: #WBS-912803</span>
                    <span class="order-card__date">Đặt ngày: 01/06/2026 10:20</span>
                </div>
                <div style="display: flex; gap: 8px;">
                    <span class="badge badge--error badge--dot">Đã hủy đơn</span>
                    <span class="badge badge--outline">Đã hoàn tiền</span>
                </div>
            </div>
            <div class="order-card__products">
                <!-- Product 1 -->
                <a href="detail.php" class="order-card__product">
                    <svg class="order-card__product-img" width="64" height="86" viewBox="0 0 64 86" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="64" height="86" rx="4" fill="url(#paint0_linear_o4)"/>
                        <rect x="5" y="6" width="2" height="74" fill="#fff" opacity="0.3"/>
                        <text x="12" y="36" fill="#fff" font-family="Arial" font-size="6" font-weight="bold">TƯ DUY NHANH</text>
                        <text x="12" y="44" fill="#fff" font-family="Arial" font-size="6" font-weight="bold">& CHẬM</text>
                        <defs>
                            <linearGradient id="paint0_linear_o4" x1="0" y1="0" x2="64" y2="86" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#E63946"/>
                                <stop offset="1" stop-color="#9A031E"/>
                            </linearGradient>
                        </defs>
                    </svg>
                    <div class="order-card__product-info">
                        <h3 class="order-card__product-title">Tư Duy Nhanh Và Chậm</h3>
                        <span class="order-card__product-meta">Tác giả: Daniel Kahneman</span>
                    </div>
                    <div class="order-card__product-price">
                        <span class="order-card__product-price-current">150.000 đ</span>
                        <div class="order-card__product-price-qty">x 1</div>
                    </div>
                </a>
            </div>
            <div class="order-card__footer">
                <span class="order-card__summary">
                    Tổng số lượng: <strong>1 sản phẩm</strong>
                    <span class="order-card__total-price">Tổng cộng: 180.000 đ</span>
                </span>
                <div class="btn-group">
                    <a href="detail.php" class="btn btn--outline btn--sm">Xem chi tiết</a>
                    <button class="btn btn--primary btn--sm">Mua lại</button>
                </div>
            </div>
        </div>

    </div>
</main>

<?php include '../includes/footer.php'; ?>

<!-- Script to toggle navbar on mobile -->
<script>
    document.querySelector('.navbar__toggle').addEventListener('click', function() {
        document.querySelector('.navbar').classList.toggle('is-open');
    });

    // Simulating tabs switching
    const tabs = document.querySelectorAll('.order-tab-item');
    tabs.forEach(tab => {
        tab.addEventListener('click', (e) => {
            e.preventDefault();
            tabs.forEach(t => t.classList.remove('is-active'));
            tab.classList.add('is-active');
            
            // Demo filtering feedback
            const status = tab.getAttribute('href').split('=')[1];
            const cards = document.querySelectorAll('.order-card');
            cards.forEach(card => {
                if (status === 'all') {
                    card.style.display = 'block';
                } else if (status === 'completed' && card.innerText.includes('Đã giao hàng thành công')) {
                    card.style.display = 'block';
                } else if (status === 'shipping' && card.innerText.includes('Đang giao hàng')) {
                    card.style.display = 'block';
                } else if (status === 'cancelled' && card.innerText.includes('Đã hủy đơn')) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
</script>
</body>
</html>
