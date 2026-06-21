<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý danh mục</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
  <div class="admin-layout">
    <aside class="sidebar">
      <h2>Book Admin</h2>
      <a href="index.php">Dashboard</a>
      <a href="products.php">Sản phẩm</a>
      <a href="categories.php" class="active">Danh mục</a>
      <a href="orders.php">Đơn hàng</a>
      <a href="users.php">Người dùng</a>
      <a href="coupons.php">Mã giảm giá</a>
    </aside>

    <main class="content">
      <div class="topbar">
        <h1>Quản lý danh mục</h1>
        <button class="btn">Thêm danh mục</button>
      </div>

      <div class="table-box">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Tên danh mục</th>
              <th>Số sản phẩm</th>
              <th>Trạng thái</th>
              <th>Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>Tiểu thuyết</td>
              <td>32</td>
              <td><span class="status active">Hiển thị</span></td>
              <td class="actions">
                <button class="btn">Sửa</button>
                <button class="btn danger">Xóa</button>
              </td>
            </tr>
            <tr>
              <td>2</td>
              <td>Kỹ năng sống</td>
              <td>18</td>
              <td><span class="status active">Hiển thị</span></td>
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