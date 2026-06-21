<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý sản phẩm</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
  <div class="admin-layout">
    <aside class="sidebar">
      <h2>Book Admin</h2>
      <a href="index.php">Dashboard</a>
      <a href="products.php" class="active">Sản phẩm</a>
      <a href="categories.php">Danh mục</a>
      <a href="orders.php">Đơn hàng</a>
      <a href="users.php">Người dùng</a>
      <a href="coupons.php">Mã giảm giá</a>
    </aside>

    <main class="content">
      <div class="topbar">
        <h1>Quản lý sản phẩm</h1>
        <button class="btn">Thêm sản phẩm</button>
      </div>

      <div class="table-box">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Tên sách</th>
              <th>Danh mục</th>
              <th>Giá</th>
              <th>Tồn kho</th>
              <th>Trạng thái</th>
              <th>Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>Đắc Nhân Tâm</td>
              <td>Kỹ năng sống</td>
              <td>89.000đ</td>
              <td>35</td>
              <td><span class="status active">Đang bán</span></td>
              <td class="actions">
                <button class="btn">Sửa</button>
                <button class="btn danger">Xóa</button>
              </td>
            </tr>
            <tr>
              <td>2</td>
              <td>Nhà Giả Kim</td>
              <td>Tiểu thuyết</td>
              <td>76.000đ</td>
              <td>18</td>
              <td><span class="status active">Đang bán</span></td>
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