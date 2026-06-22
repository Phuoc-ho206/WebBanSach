<?php
require_once 'includes/config.php';
$pageTitle = '404 - Không tìm thấy trang';
$extraCss = ['css/cart.css', 'css/cart-checkout.css'];
include 'includes/header.php';
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


<main>
<div class="error-page" role="main">
<div class="error-container">
<div aria-hidden="true" class="error-illustration">📚</div>
<h1 aria-label="Lỗi 404" class="error-code">404</h1>
<h2 class="error-title">Không tìm thấy trang</h2>
<p class="error-desc">
          Trang bạn đang tìm kiếm có thể đã bị xóa, đổi tên<br/>
          hoặc đường dẫn không chính xác. hãy thử lại sau!
        </p>
<div class="error-search">
<div class="input-group error-search__group">
<input aria-label="Tìm kiếm sách" class="form-control" placeholder="Tìm kiếm sách..." type="search"/>
<button class="btn btn--primary">🔍</button>
</div>
</div>
<!--nút hành động -->
<div class="error-actions">
<a class="btn btn--primary btn--lg" href="<?= url('/') ?>">
            🏠 Quay về trang chủ
          </a>
<a class="btn btn--outline btn--lg" href="<?= url('cart/index.php') ?>">
            🛒 Quay lại giỏ hàng
          </a>
</div>
<div class="error-quick-links">
<p class="error-quick-links__text">
            Hoặc khám phá các danh mục phổ biến:
          </p>
<div class="error-quick-links__list">
<a class="btn btn--ghost btn--sm" href="<?= url('/') ?>#categories">📖 Văn học</a>
<a class="btn btn--ghost btn--sm" href="<?= url('/') ?>#categories">💼 Kinh tế</a>
<a class="btn btn--ghost btn--sm" href="<?= url('/') ?>#categories">🌟 Kỹ năng sống</a>
<a class="btn btn--ghost btn--sm" href="<?= url('/') ?>#categories">🧪 Khoa học</a>
<a class="btn btn--ghost btn--sm" href="<?= url('/') ?>#categories">🎨 Thiếu nhi</a>
</div>
</div>
</div>
</div>
</main>

<?php include 'includes/footer.php'; ?>

<script src="<?= asset('js/main.js') ?>"></script>
<script src="<?= asset('js/cart-checkout.js') ?>"></script>
</body>
</html>
