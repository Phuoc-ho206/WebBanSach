<?php
function h($value) {
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function activeClass($page) {
  return basename($_SERVER['PHP_SELF']) === $page ? 'is-active' : '';
}

function adminCssLinks() {
  return '
  <link rel="stylesheet" href="../assets/css/variables.css?v=3">
  <link rel="stylesheet" href="../assets/css/components/button.css?v=3">
  <link rel="stylesheet" href="../assets/css/components/badge.css?v=3">
  <link rel="stylesheet" href="../assets/css/components/card.css?v=3">
  <link rel="stylesheet" href="../assets/css/components/form.css?v=3">
  <link rel="stylesheet" href="../assets/css/components/navbar.css?v=3">
  <link rel="stylesheet" href="../assets/css/components/table.css?v=3">
  <link rel="stylesheet" href="../assets/css/components/alert_toast.css?v=3">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">';
}

function badgeClass($status) {
  if (in_array($status, ['Đang bán', 'Hiển thị', 'Hoạt động', 'Đang áp dụng', 'Hoàn thành'], true)) {
    return 'badge--success';
  }

  if (in_array($status, ['Chờ xử lý', 'Sắp hết hạn', 'Đang giao', 'Chờ xác nhận'], true)) {
    return 'badge--warning';
  }

  if (in_array($status, ['Ngừng bán', 'Ẩn', 'Đã khóa', 'Đã hết hạn', 'Đã hủy'], true)) {
    return 'badge--danger';
  }

  return 'badge--info'; // Ví dụ: 'Đã xác nhận' sẽ có màu badge--info
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
        <li class="navbar__item">
          <a href="#" class="navbar__link" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();" style="color: var(--color-error, #e53e3e);">
            Đăng xuất
          </a>
          <form id="admin-logout-form" action="/WebBanSach/auth/pages/login.php" method="POST" style="display: none;">
            <input type="hidden" name="action" value="logout">
          </form>
        </li>
      </ul>
    </nav>
  </header>

  <?php if (isset($_SESSION['log_toast'])): 
    $toastMsg = $_SESSION['log_toast'];
    $toastClass = 'toast--success';
    $toastColor = 'var(--color-success)';
    $toastIcon = 'fa-circle-check';
    
    // Kiểm tra các từ khóa mang tính chất tiêu cực/cảnh báo/lỗi
    if (preg_match('/(thất bại|hủy|lỗi|xóa|vượt quá|giới hạn|hết hàng|cảnh báo)/i', $toastMsg)) {
        $toastClass = 'toast--error';
        $toastColor = 'var(--color-error)';
        $toastIcon = 'fa-circle-xmark';
    }
  ?>
    <div class="toast-container" id="admin-toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 10000; display: block; max-width: 380px; width: 100%;">
      <div class="toast <?= $toastClass ?>" id="admin-toast" style="animation: toast-in 0.3s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-left: 4px solid <?= $toastColor ?>; background: var(--color-surface); padding: 16px; border-radius: var(--border-radius); display: flex; gap: 12px; align-items: flex-start;">
        <i class="fa-solid <?= $toastIcon ?>" style="color: <?= $toastColor ?>; font-size: 1.25rem; margin-top: 2px;"></i>
        <div style="flex: 1;">
          <div style="font-weight: bold; margin-bottom: 4px; color: var(--color-text); text-align: left;">
            <?= ($toastClass === 'toast--error') ? 'Thông báo hệ thống' : 'Nhật ký hệ thống' ?>
          </div>
          <div style="font-size: 0.9rem; color: var(--color-text-light); line-height: 1.4; text-align: left;"><?= htmlspecialchars($toastMsg) ?></div>
        </div>
        <button onclick="document.getElementById('admin-toast-container').remove()" style="background: none; border: none; font-size: 1.1rem; cursor: pointer; color: var(--color-text-light); padding: 0; line-height: 1; opacity: 0.7;">&times;</button>
      </div>
    </div>
    <script>
      setTimeout(function() {
        var el = document.getElementById('admin-toast-container');
        if (el) {
          el.style.opacity = '0';
          el.style.transition = 'opacity 0.5s ease';
          setTimeout(function() { el.remove(); }, 500);
        }
      }, 4000);
    </script>
    <?php unset($_SESSION['log_toast']); ?>
  <?php endif; ?>
  <?php
}

function redirectTo($page) {
  header('Location: ' . $page);
  exit;
}
?>
