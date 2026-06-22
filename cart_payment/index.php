<?php
require_once '../includes/config.php';
$pageTitle = 'Giỏ hàng';
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
<li>Giỏ hàng</li>
</ol>
<h1 class="page-title">🛒 Giỏ hàng của bạn</h1>
<div class="cart-layout">
<!-- Danh sách sản phẩm  -->
<div>
<!-- Bảng sản phẩm -->
<div class="section-card section-card--table">
<!-- Giỏ trống -->
<div class="cart-empty" id="cart-empty" style="display:none;">
<div class="cart-empty__icon">🛒</div>
<h2 class="cart-empty__title">Giỏ hàng đang trống</h2>
<p class="cart-empty__desc">Hãy thêm sách vào giỏ để tiến hành mua hàng nhé!</p>
<a class="btn btn--primary btn--lg" href="<?= url('/') ?>#books">🔍 Khám phá sách hay</a>
</div>
<!-- Bảng sản phẩm -->
<div id="cart-table-wrapper">
<table aria-label="Danh sách sản phẩm trong giỏ" class="cart-table">
<thead>
<tr>
<th>Sản phẩm</th>
<th>Đơn giá</th>
<th>Số lượng</th>
<th>Thành tiền</th>
<th></th>
</tr>
</thead>
<tbody id="cart-items-body">
<!-- Render bởi JavaScript -->
</tbody>
</table>
</div>
</div>
<!-- Nút hành động -->
<div class="cart-actions" id="cart-actions" style="display:none;">
<a class="btn btn--outline" href="<?= url('/') ?>#books">← Tiếp tục mua sách</a>
<a class="btn btn--primary btn--lg" href="<?= url('cart/checkout.php') ?>">Tiến hành thanh toán →</a>
</div>
</div>
<!--Tóm tắt đơn hàng -->
<aside aria-label="Tóm tắt đơn hàng" class="cart-summary">
<h2 class="cart-summary__title">📋 Tóm tắt đơn hàng</h2>
<!-- Mã giảm giá -->
<div class="coupon-section">
<label class="coupon-label" for="coupon-input">🏷️ Mã giảm giá</label>
<div class="input-group">
<input aria-label="Nhập mã giảm giá" autocomplete="off" class="form-control" id="coupon-input" placeholder="Nhập mã (VD: SACH10)" type="text"/>
<button class="btn btn--primary" id="coupon-apply-btn">Áp dụng</button>
</div>
<div aria-live="polite" class="coupon-result" id="coupon-result" role="alert"></div>
<p class="form-text form-text--coupon">
            Mã thử: <code>SACH10</code> (giảm 10%) hoặc <code>FREESHIP</code> (miễn ship)
          </p>
</div>
<!-- Tóm tắt tiền -->
<div class="cart-summary__row">
<span>Tạm tính</span>
<span id="summary-subtotal">0₫</span>
</div>
<div class="cart-summary__row" id="summary-discount-row" style="display:none;">
<span class="cart-summary__discount">Giảm giá</span>
<span class="cart-summary__discount" id="summary-discount">0₫</span>
</div>
<div class="cart-summary__row">
<span>Phí vận chuyển</span>
<span id="summary-shipping">30.000₫</span>
</div>
<div class="cart-summary__row cart-summary__row--total">
<span>Tổng cộng</span>
<span class="cart-summary__value" id="summary-total">0₫</span>
</div>
<!-- Nút thanh toán -->
<div class="cart-summary__actions">
<a class="btn btn--primary btn--lg btn--block" href="<?= url('cart/checkout.php') ?>">
            💳 Tiến hành thanh toán
          </a>
<a class="btn btn--outline btn--block" href="<?= url('/') ?>#books">
            ← Tiếp tục mua sách
          </a>
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
