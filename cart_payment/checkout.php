<?php
require_once '../includes/config.php';
$pageTitle = 'Thanh toán';
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
<li>Thanh toán</li>
</ol>
<h1 class="page-title">💳 Thanh toán</h1>
<!-- Bước thanh toán -->
<div class="checkout-layout">
<!-- Form -->
<div>
<form class="form" id="checkout-form" novalidate="">
<!-- 1. Thông tin giao hàng -->
<div class="section-card">
<h2 class="section-title">📦 Thông tin giao hàng</h2>
<div class="form-row">
<div class="form-group">
<label class="form-label form-label--required" for="field-name">Họ và tên người nhận</label>
<input autocomplete="name" class="form-control" id="field-name" placeholder="Nguyễn Văn A" required="" type="text"/>
<span class="form-error" id="field-name-error" role="alert"></span>
</div>
<div class="form-group">
<label class="form-label form-label--required" for="field-phone">Số điện thoại</label>
<input autocomplete="tel" class="form-control" id="field-phone" placeholder="0912 345 678" required="" type="tel"/>
<span class="form-error" id="field-phone-error" role="alert"></span>
</div>
</div>
<div class="form-group">
<label class="form-label form-label--required" for="field-email">Email</label>
<input autocomplete="email" class="form-control" id="field-email" placeholder="email@example.com" required="" type="email"/>
<span class="form-error" id="field-email-error" role="alert"></span>
</div>
<div class="form-group">
<label class="form-label form-label--required" for="field-address">Địa chỉ nhận hàng</label>
<input autocomplete="street-address" class="form-control" id="field-address" placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố" required="" type="text"/>
<span class="form-error" id="field-address-error" role="alert"></span>
</div>
<div class="form-group">
<label class="form-label" for="field-note">Ghi chú đơn hàng</label>
<textarea class="form-control" id="field-note" placeholder="Ghi chú thêm (không bắt buộc): ví dụ giờ giao hàng, yêu cầu đặc biệt..."></textarea>
</div>
</div>
<!-- 2. Phương thức vận chuyển -->
<div class="section-card">
<h2 class="section-title">🚚 Phương thức vận chuyển</h2>
<div aria-label="Chọn phương thức vận chuyển" class="method-options" role="radiogroup">
<label class="method-option">
<input checked="" name="shipping" type="radio" value="standard"/>
<span class="method-option__icon">📬</span>
<div class="method-option__info">
<p class="method-option__name">Giao hàng tiêu chuẩn</p>
<p class="method-option__desc">Nhận hàng trong 3–5 ngày làm việc • 30.000₫</p>
</div>
</label>
<label class="method-option">
<input name="shipping" type="radio" value="express"/>
<span class="method-option__icon">⚡</span>
<div class="method-option__info">
<p class="method-option__name">Giao hàng nhanh</p>
<p class="method-option__desc">Nhận hàng trong 1–2 ngày làm việc • 50.000₫</p>
</div>
</label>
</div>
</div>
<!-- 3. Phương thức thanh toán -->
<div class="section-card">
<h2 class="section-title">💳 Phương thức thanh toán</h2>
<div aria-label="Chọn phương thức thanh toán" class="method-options" role="radiogroup">
<label class="method-option">
<input checked="" name="payment" type="radio" value="cod"/>
<span class="method-option__icon">💵</span>
<div class="method-option__info">
<p class="method-option__name">Thanh toán khi nhận hàng (COD)</p>
<p class="method-option__desc">Trả tiền mặt khi nhận được sách</p>
</div>
</label>
<label class="method-option">
<input name="payment" type="radio" value="bank"/>
<span class="method-option__icon">🏦</span>
<div class="method-option__info">
<p class="method-option__name">Chuyển khoản ngân hàng</p>
<p class="method-option__desc">Vietcombank • MB Bank • Techcombank</p>
</div>
</label>
<label class="method-option">
<input name="payment" type="radio" value="ewallet"/>
<span class="method-option__icon">📱</span>
<div class="method-option__info">
<p class="method-option__name">Ví điện tử</p>
<p class="method-option__desc">MoMo • ZaloPay • VNPay</p>
</div>
</label>
</div>
</div>
<!-- Nút đặt hàng (mobile) -->
<div class="section-card checkout-mobile-submit" id="submit-btn-mobile-wrapper">
<button class="btn btn--primary btn--lg btn--block" type="submit">
              ✅ Xác nhận đặt hàng
            </button>
</div>
</form>
</div>
<!-- ====== CỘT PHẢI: Tóm tắt đơn hàng ====== -->
<aside aria-label="Tóm tắt đơn hàng" class="order-summary">
<h2 class="order-summary__title">📋 Đơn hàng của bạn</h2>
<!-- Danh sách sản phẩm mẫu -->
<div class="order-summary__products">
<div class="order-summary__product">
<div class="order-summary__product-img">📘</div>
<div class="order-summary__product-info">
<p class="order-summary__product-name">Đắc Nhân Tâm</p>
<p class="order-summary__product-qty">Dale Carnegie × 1</p>
</div>
<span class="order-summary__product-price">86.000₫</span>
</div>
<div class="order-summary__product">
<div class="order-summary__product-img">📙</div>
<div class="order-summary__product-info">
<p class="order-summary__product-name">Nhà Giả Kim</p>
<p class="order-summary__product-qty">Paulo Coelho × 2</p>
</div>
<span class="order-summary__product-price">138.000₫</span>
</div>
<div class="order-summary__product">
<div class="order-summary__product-img">📗</div>
<div class="order-summary__product-info">
<p class="order-summary__product-name">Tôi Tài Giỏi, Bạn Cũng Thế</p>
<p class="order-summary__product-qty">Adam Khoo × 1</p>
</div>
<span class="order-summary__product-price">94.000₫</span>
</div>
</div>
<!-- Mã đã áp dụng (ẩn mặc định, hiện nếu có) -->
<div class="order-summary__row order-summary__row--coupon" id="checkout-coupon-row" style="display:none;">
<span>🏷️ Mã giảm giá</span>
<span id="checkout-coupon-val">—</span>
</div>
<!-- Tóm tắt tiền -->
<div class="order-summary__row">
<span>Tạm tính</span>
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
<!-- Nút xác nhận (desktop) -->
<button class="btn btn--primary btn--lg btn--block order-summary__submit" form="checkout-form" type="submit">
          ✅ Xác nhận đặt hàng
        </button>
<p class="form-text form-text--center">
          🔒 Thông tin của bạn được bảo mật an toàn
        </p>
</aside>
</div><!-- /.checkout-layout -->
</main>

<div aria-atomic="true" aria-live="polite" class="toast-container"></div>

<?php include '../includes/footer.php'; ?>

<script src="<?= asset('js/main.js') ?>"></script>
<script src="<?= asset('js/cart-checkout.js') ?>"></script>
</body>
</html>
