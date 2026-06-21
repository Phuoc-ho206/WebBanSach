<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý người dùng</title>
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
      <a href="users.php" class="active">Người dùng</a>
      <a href="coupons.php">Mã giảm giá</a>
    </aside>

    <main class="content">
      <div class="topbar">
        <h1>Quản lý người dùng</h1>
        <button class="btn">Thêm người dùng</button>
      </div>

      <div class="table-box">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Họ tên</th>
              <th>Email</th>
              <th>Vai trò</th>
              <th>Trạng thái</th>
              <th>Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>Admin</td>
              <td>admin@gmail.com</td>
              <td>Quản trị viên</td>
              <td><span class="status active">Hoạt động</span></td>
              <td class="actions">
                <button class="btn">Sửa</button>
                <button class="btn danger">Khóa</button>
              </td>
            </tr>
            <tr>
              <td>2</td>
              <td>Nguyễn Văn A</td>
              <td>vana@gmail.com</td>
              <td>Khách hàng</td>
              <td><span class="status active">Hoạt động</span></td>
              <td class="actions">
                <button class="btn">Sửa</button>
                <button class="btn danger">Khóa</button>
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