<?php
function h($value) {
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function activeClass($page) {
  return basename($_SERVER['PHP_SELF']) === $page ? 'is-active' : '';
}

function adminCssLinks() {
  return '
  <link rel="stylesheet" href="../assets/css/variables.css">
  <link rel="stylesheet" href="../assets/css/components/button.css">
  <link rel="stylesheet" href="../assets/css/components/badge.css">
  <link rel="stylesheet" href="../assets/css/components/card.css">
  <link rel="stylesheet" href="../assets/css/components/form.css">
  <link rel="stylesheet" href="../assets/css/components/navbar.css">
  <link rel="stylesheet" href="../assets/css/components/table.css">';
}

function badgeClass($status) {
  if (in_array($status, ['Đang bán', 'Hiển thị', 'Hoạt động', 'Đang áp dụng', 'Hoàn thành'], true)) {
    return 'badge--success';
  }

  if (in_array($status, ['Chờ xử lý', 'Sắp hết hạn', 'Đang giao'], true)) {
    return 'badge--warning';
  }

  if (in_array($status, ['Ngừng bán', 'Ẩn', 'Đã khóa', 'Đã hết hạn', 'Đã hủy'], true)) {
    return 'badge--danger';
  }

  return 'badge--info';
}

function adminSidebar() {
  ?>
  <header class="navbar admin-navbar">
    <a href="index.php" class="navbar__brand">Book Admin</a>
    <nav>
      <ul class="navbar__menu">
        <li class="navbar__item"><a href="index.php" class="navbar__link <?= activeClass('index.php') ?>">Dashboard</a></li>
        <li class="navbar__item"><a href="products.php" class="navbar__link <?= activeClass('products.php') ?>">Sản phẩm</a></li>
        <li class="navbar__item"><a href="categories.php" class="navbar__link <?= activeClass('categories.php') ?>">Danh mục</a></li>
        <li class="navbar__item"><a href="orders.php" class="navbar__link <?= activeClass('orders.php') ?>">Đơn hàng</a></li>
        <li class="navbar__item"><a href="users.php" class="navbar__link <?= activeClass('users.php') ?>">Người dùng</a></li>
        <li class="navbar__item"><a href="coupons.php" class="navbar__link <?= activeClass('coupons.php') ?>">Mã giảm giá</a></li>
      </ul>
    </nav>
  </header>
  <?php
}

function redirectTo($page) {
  header('Location: ' . $page);
  exit;
}
?>
