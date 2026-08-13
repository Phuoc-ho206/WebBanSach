<?php
require_once '../includes/config.php';
$pageTitle = 'Đặt hàng thành công';
$extraCss = ['css/cart.css', 'css/cart-checkout.css'];
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
        <li class="navbar__item"><a href="<?= url('/') ?>#books" class="navbar__link">Cửa hàng</a></li>
        <li class="navbar__item"><a href="<?= url('/') ?>#categories" class="navbar__link">Danh mục</a></li>
        <li class="navbar__item"><a href="<?= url('/') ?>#promotions" class="navbar__link">Khuyến mãi</a></li>
        <li class="navbar__item"><a href="<?= url('/') ?>#contact" class="navbar__link">Liên hệ</a></li>
    </ul>
    <div class="navbar__actions">
        <a href="<?= url('cart/index.php') ?>" class="btn btn--ghost btn--sm" style="position: relative;">
            🛒 Giỏ hàng
            <span class="badge badge--primary" id="navbar-cart-count" style="position: absolute; top: -5px; right: -5px;">3</span>
        </a>
        <a href="<?= url('auth/pages/profile.php') ?>" class="btn btn--primary btn--sm">Tài khoản</a>
    </div>
</header>


<main class="page-container">
<!-- Breadcrumbs -->
<ol aria-label="Đường dẫn" class="breadcrumbs">
<li><a href="<?= url('/') ?>">Trang chủ</a></li>
<li><a href="<?= url('cart/index.php') ?>">Giỏ hàng</a></li>
<li><a href="<?= url('cart/checkout.php') ?>">Thanh toán</a></li>
<li>Xác nhận đơn hàng</li>
</ol>
<!-- Hero thành công -->
<div aria-live="polite" class="success-hero" role="status">
<div aria-hidden="true" class="success-icon">✅</div>
<h1 class="success-title">Đặt hàng thành công!</h1>
<p class="success-order-id">
        Mã đơn hàng: <strong>ORD-2026-001</strong>
</p>
<p class="success-desc">
        Cảm ơn bạn đã mua sách tại WebBanSach! Chúng tôi sẽ xử lý và giao hàng sớm nhất có thể.
      </p>
<div class="success-header__meta">
<span class="status-badge status-badge--received">📋 Đã tiếp nhận</span>
</div>
</div>
<!-- Layout chi tiết -->
<div class="success-layout">
<!-- Thông tin & Sản phẩm -->
<div>
<!-- Thông tin người nhận -->
<div class="section-card">
<h2 class="section-title">📦 Thông tin giao hàng</h2>
<ul class="info-list">
<li class="info-item">
<span class="info-item__label">Người nhận</span>
<span class="info-item__value" id="success-name">—</span>
</li>
<li class="info-item">
<span class="info-item__label">Số điện thoại</span>
<span class="info-item__value" id="success-phone">—</span>
</li>
<li class="info-item">
<span class="info-item__label">Email</span>
<span class="info-item__value" id="success-email">—</span>
</li>
<li class="info-item">
<span class="info-item__label">Địa chỉ</span>
<span class="info-item__value" id="success-address">—</span>
</li>
<li class="info-item">
<span class="info-item__label">Vận chuyển</span>
<span class="info-item__value">Giao hàng tiêu chuẩn (3–5 ngày)</span>
</li>
<li class="info-item">
<span class="info-item__label">Thanh toán</span>
<span class="info-item__value">Thanh toán khi nhận hàng (COD)</span>
</li>
</ul>
</div>
<!-- Danh sách sản phẩm đã đặt -->
<div class="section-card">
<h2 class="section-title">📚 Sản phẩm đã đặt</h2>
<div class="success-product-list">
<div class="success-product">
<div class="success-product__img">📘</div>
<div class="success-product__info">
<p class="success-product__name">Đắc Nhân Tâm</p>
<p class="success-product__meta">Dale Carnegie × 1</p>
</div>
<span class="success-product__price">86.000₫</span>
</div>
<div class="success-product success-product--bordered">
<div class="success-product__img">📙</div>
<div class="success-product__info">
<p class="success-product__name">Nhà Giả Kim</p>
<p class="success-product__meta">Paulo Coelho × 2</p>
</div>
<span class="success-product__price">138.000₫</span>
</div>
<div class="success-product success-product--bordered">
<div class="success-product__img">📗</div>
<div class="success-product__info">
<p class="success-product__name">Tôi Tài Giỏi, Bạn Cũng Thế</p>
<p class="success-product__meta">Adam Khoo × 1</p>
</div>
<span class="success-product__price">94.000₫</span>
</div>
</div>
</div>
</div>
<!-- Tổng tiền -->
<aside>
<div class="section-card section-card--sticky">
<h2 class="section-title">💰 Tổng kết đơn hàng</h2>
<div class="order-summary__row">
<span>Tạm tính (4 sách)</span>
<span>318.000₫</span>
</div>
<div class="order-summary__row">
<span>Phí vận chuyển</span>
<span>30.000₫</span>
</div>
<div class="order-summary__row order-summary__row--total">
<span>Tổng thanh toán</span>
<span class="order-summary__value">348.000₫</span>
</div>
<!-- Nút hành động -->
<div class="success-actions">
<a class="btn btn--outline btn--lg btn--block" href="<?= url('cart/order-success.php') ?>">
              🔍 Theo dõi đơn hàng
            </a>
<a class="btn btn--primary btn--lg btn--block" href="<?= url('/') ?>">
              🏠 Quay về trang chủ
            </a>
</div>
<!-- Thông tin thêm -->
<div class="success-note">
<p class="success-note__text">
              📧 Email xác nhận đã được gửi đến địa chỉ email của bạn.<br/>
              📞 Liên hệ <strong>0909 123 456</strong> nếu cần hỗ trợ.
            </p>
</div>
</div>
</aside>
</div>
</main>

<div aria-atomic="true" aria-live="polite" class="toast-container"></div>

<?php include '../includes/footer.php'; ?>

<script src="<?= asset('js/main.js') ?>"></script>
<script src="<?= asset('js/cart-checkout.js') ?>"></script>
</body>
</html>
