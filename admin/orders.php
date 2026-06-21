<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý đơn hàng</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
  <div class="admin-layout">
    <aside class="sidebar">
      <h2>Book Admin</h2>
      <a href="index.php">Dashboard</a>
      <a href="products.php">Sản phẩm</a>
      <a href="categories.php">Danh mục</a>
      <a href="orders.php" class="active">Đơn hàng</a>
      <a href="users.php">Người dùng</a>
      <a href="coupons.php">Mã giảm giá</a>
    </aside>

    <main class="content">
      <div class="topbar">
        <h1>Quản lý đơn hàng</h1>
      </div>

      <div class="table-box">
        <table>
          <thead>
            <tr>
              <th>Mã đơn</th>
              <th>Khách hàng</th>
              <th>Ngày đặt</th>
              <th>Tổng tiền</th>
              <th>Trạng thái</th>
              <th>Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>#DH001</td>
              <td>Nguyễn Văn A</td>
              <td>22/06/2026</td>
              <td>245.000đ</td>
              <td><span class="status pending">Chờ xử lý</span></td>
              <td class="actions">
                <button class="btn">Chi tiết</button>
                <button class="btn danger">Hủy</button>
              </td>
            </tr>
            <tr>
              <td>#DH002</td>
              <td>Trần Thị B</td>
              <td>22/06/2026</td>
              <td>389.000đ</td>
              <td><span class="status active">Đã xác nhận</span></td>
              <td class="actions">
                <button class="btn">Chi tiết</button>
                <button class="btn danger">Hủy</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>
  </div>
  <script src="../assets/js/admin.js"></script>
</body>
</html>