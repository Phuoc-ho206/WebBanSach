<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Admin Panel - Dashboard</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
  <div class="admin-layout">
    <aside class="sidebar">
      <h2>Book Admin</h2>
      <a href="index.php" class="active">Dashboard</a>
      <a href="products.php">Sản phẩm</a>
      <a href="categories.php">Danh mục</a>
      <a href="orders.php">Đơn hàng</a>
      <a href="users.php">Người dùng</a>
      <a href="coupons.php">Mã giảm giá</a>
    </aside>

    <main class="content">
      <div class="topbar">
        <h1>Dashboard & thống kê</h1>
        <span>Xin chào, Admin</span>
      </div>

      <section class="cards">
        <div class="card">
          <h3>Tổng sản phẩm</h3>
          <p>128</p>
        </div>
        <div class="card">
          <h3>Đơn hàng mới</h3>
          <p>24</p>
        </div>
        <div class="card">
          <h3>Người dùng</h3>
          <p>560</p>
        </div>
        <div class="card">
          <h3>Doanh thu</h3>
          <p>12.500.000đ</p>
        </div>
      </section>
    </main>
  </div>
  <script src="../assets/js/admin.js"></script>
</body>
</html>