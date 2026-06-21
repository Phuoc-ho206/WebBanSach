<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý mã giảm giá</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
  <div class="admin-layout">
    <aside class="sidebar">
      <h2>Book Admin</h2>
      <a href="index.php">Dashboard</a>
      <a href="products.php">Sản phẩm</a>
      <a href="categories.php">Danh mục</a>
      <a href="orders.php">Đơn hàng</a>
      <a href="users.php">Người dùng</a>
      <a href="coupons.php" class="active">Mã giảm giá</a>
    </aside>

    <main class="content">
      <div class="topbar">
        <h1>Quản lý mã giảm giá</h1>
        <button class="btn">Thêm mã giảm giá</button>
      </div>

      <div class="table-box">
        <table>
          <thead>
            <tr>
              <th>Mã</th>
              <th>Giảm giá</th>
              <th>Ngày bắt đầu</th>
              <th>Ngày kết thúc</th>
              <th>Trạng thái</th>
              <th>Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>SALE10</td>
              <td>10%</td>
              <td>20/06/2026</td>
              <td>30/06/2026</td>
              <td><span class="status active">Đang áp dụng</span></td>
              <td class="actions">
                <button class="btn">Sửa</button>
                <button class="btn danger">Xóa</button>
              </td>
            </tr>
            <tr>
              <td>FREESHIP</td>
              <td>Miễn phí vận chuyển</td>
              <td>22/06/2026</td>
              <td>28/06/2026</td>
              <td><span class="status pending">Sắp hết hạn</span></td>
              <td class="actions">
                <button class="btn">Sửa</button>
                <button class="btn danger">Xóa</button>
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