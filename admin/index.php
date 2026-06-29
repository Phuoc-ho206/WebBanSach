<?php
require_once 'data.php';
require_once 'partials.php';

$productCount = count($_SESSION['admin_products']);
$categoryCount = count($_SESSION['admin_categories']);
$orderCount = count($_SESSION['admin_orders']);
$userCount = count($_SESSION['admin_users']);
$couponCount = count($_SESSION['admin_coupons']);
$revenue = array_sum(array_column($_SESSION['admin_orders'], 'total'));
$monthlyStats = monthlyOrderStats();
$orderStatusStats = countByField($_SESSION['admin_orders'], 'status');
$productCategoryStats = countByField($_SESSION['admin_products'], 'category');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Dashboard</title><?= adminCssLinks() ?>
</head>
<body>
  <div class="app-layout">
    <?php adminSidebar(); ?>

    <main class="page-content">
      <header class="page-header">
        <div>
          <h1>Dashboard & Thống kê</h1>
          <p>Tổng quan hoạt động của cửa hàng sách</p>
        </div>
        <span>Xin chào, Admin</span>
      </header>

      <section class="card-grid">
        <article class="card stat-card"><div class="card__body"><p class="card__subtitle">Tổng sản phẩm</p><h3 class="card__title"><?= $productCount ?></h3></div></article>
        <article class="card stat-card"><div class="card__body"><p class="card__subtitle">Đơn hàng</p><h3 class="card__title"><?= $orderCount ?></h3></div></article>
        <article class="card stat-card"><div class="card__body"><p class="card__subtitle">Người dùng</p><h3 class="card__title"><?= $userCount ?></h3></div></article>
        <article class="card stat-card"><div class="card__body"><p class="card__subtitle">Mã giảm giá</p><h3 class="card__title"><?= $couponCount ?></h3></div></article>
        <article class="card stat-card"><div class="card__body"><p class="card__subtitle">Doanh thu</p><h3 class="card__title"><?= number_format($revenue, 0, ',', '.') ?> đ</h3></div></article>
      </section>

      <section class="card-grid card-grid--charts">
        <article class="card chart-card chart-card--wide">
          <div class="card__body">
            <h2 class="card__title">Doanh thu theo tháng</h2>
            <canvas
              data-chart="bar"
              data-labels='<?= h(json_encode(array_map(function ($m) { return 'T' . $m; }, range(1, 12)), JSON_UNESCAPED_UNICODE)) ?>'
              data-values='<?= h(json_encode(array_column($monthlyStats, 'revenue'), JSON_UNESCAPED_UNICODE)) ?>'
              data-color="#ff7a3d"
              data-unit="đ">
            </canvas>
          </div>
        </article>

        <article class="card chart-card">
          <div class="card__body">
            <h2 class="card__title">Đơn hàng theo tháng</h2>
            <canvas
              data-chart="line"
              data-labels='<?= h(json_encode(array_map(function ($m) { return 'T' . $m; }, range(1, 12)), JSON_UNESCAPED_UNICODE)) ?>'
              data-values='<?= h(json_encode(array_column($monthlyStats, 'orders'), JSON_UNESCAPED_UNICODE)) ?>'
              data-color="#2196f3"
              data-unit="đơn">
            </canvas>
          </div>
        </article>

        <article class="card chart-card">
          <div class="card__body">
            <h2 class="card__title">Trạng thái đơn hàng</h2>
            <canvas
              data-chart="doughnut"
              data-labels='<?= h(json_encode(array_keys($orderStatusStats), JSON_UNESCAPED_UNICODE)) ?>'
              data-values='<?= h(json_encode(array_values($orderStatusStats), JSON_UNESCAPED_UNICODE)) ?>'
              data-unit="đơn">
            </canvas>
          </div>
        </article>

        <article class="card chart-card">
          <div class="card__body">
            <h2 class="card__title">Sản phẩm theo danh mục</h2>
            <canvas
              data-chart="doughnut"
              data-labels='<?= h(json_encode(array_keys($productCategoryStats), JSON_UNESCAPED_UNICODE)) ?>'
              data-values='<?= h(json_encode(array_values($productCategoryStats), JSON_UNESCAPED_UNICODE)) ?>'
              data-unit="Sản Phẩm">
            </canvas>
          </div>
        </article>
      </section>
    </main>
  </div>

  <script src="../assets/js/admin.js"></script>
</body>
</html>
