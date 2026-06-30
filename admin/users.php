<?php
require_once 'data.php';
require_once 'partials.php';

$editUser = null;
if (isset($_GET['edit'])) {
  $stmt = $conn->prepare("SELECT * FROM user WHERE CustomerID = ?");
  $stmt->bind_param("i", $_GET['edit']);
  $stmt->execute();
  $editUser = $stmt->get_result()->fetch_assoc();
  $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'save') {
    $id = $_POST['id'] !== '' ? (int) $_POST['id'] : null;
    $email = trim($_POST['email']);
    $roleId = (int) $_POST['role_id'];
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    // Tách Họ tên thành LastName và FirstName
    $fullName = trim($_POST['name']);
    $parts = explode(' ', $fullName);
    $firstName = array_pop($parts);
    $lastName = implode(' ', $parts);
    if (empty($lastName)) {
      $lastName = $firstName;
    }

    if ($id) {
      $stmt = $conn->prepare("UPDATE user SET RoleID = ?, LastName = ?, FirstName = ?, Email = ?, Phone = ?, Address = ? WHERE CustomerID = ?");
      $stmt->bind_param("isssssi", $roleId, $lastName, $firstName, $email, $phone, $address, $id);
      $stmt->execute();
      $stmt->close();
    } else {
      // Thiết lập mật khẩu mặc định cho người dùng mới
      $defaultPassword = password_hash('123456', PASSWORD_DEFAULT);
      $stmt = $conn->prepare("INSERT INTO user (RoleID, LastName, FirstName, Email, Password, Phone, Address) VALUES (?, ?, ?, ?, ?, ?, ?)");
      $stmt->bind_param("issssss", $roleId, $lastName, $firstName, $email, $defaultPassword, $phone, $address);
      $stmt->execute();
      $stmt->close();
    }
  }

  if ($action === 'delete') {
    $id = (int)$_POST['id'];
    $stmt = $conn->prepare("DELETE FROM user WHERE CustomerID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
  }

  redirectTo('users.php');
}

// Lấy danh sách Vai trò
$roles = [];
$resRoles = $conn->query("SELECT RoleID, RoleName FROM role ORDER BY RoleID ASC");
if ($resRoles) {
  while ($row = $resRoles->fetch_assoc()) {
    $roles[] = $row;
  }
}

// Lấy danh sách Người dùng kèm tên Vai trò từ DB
$users = [];
$resUsers = $conn->query("
  SELECT u.CustomerID, u.LastName, u.FirstName, u.Email, u.Phone, u.Address, r.RoleName 
  FROM user u 
  LEFT JOIN role r ON u.RoleID = r.RoleID 
  ORDER BY u.CustomerID DESC
");
if ($resUsers) {
  while ($row = $resUsers->fetch_assoc()) {
    $users[] = $row;
  }
}

$editFullName = $editUser ? trim(($editUser['LastName'] ?? '') . ' ' . ($editUser['FirstName'] ?? '')) : '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản lý người dùng</title><?= adminCssLinks() ?>
</head>
<body>
  <div class="app-layout">
    <?php adminSidebar(); ?>
    <main class="page-content">
      <header class="page-header"><div><h1>Quản lý người dùng</h1><p>Thêm, sửa, xóa người dùng</p></div></header>
      
      <form method="post" class="card">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= h($editUser['CustomerID'] ?? '') ?>">
        <div class="card__body form">
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Họ tên</label>
              <input class="form-control" name="name" value="<?= h($editFullName) ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Email</label>
              <input class="form-control" type="email" name="email" value="<?= h($editUser['Email'] ?? '') ?>" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Số điện thoại</label>
              <input class="form-control" type="text" name="phone" value="<?= h($editUser['Phone'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label class="form-label">Vai trò</label>
              <select class="form-control" name="role_id" required>
                <option value="">-- Chọn vai trò --</option>
                <?php foreach ($roles as $role): ?>
                  <option value="<?= h($role['RoleID']) ?>" <?= ($editUser['RoleID'] ?? '') == $role['RoleID'] ? 'selected' : '' ?>><?= h($role['RoleName']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group" style="width: 100%;">
              <label class="form-label">Địa chỉ</label>
              <input class="form-control" type="text" name="address" value="<?= h($editUser['Address'] ?? '') ?>">
            </div>
          </div>
          <div class="btn-group">
            <button class="btn btn--primary" type="submit"><?= $editUser ? 'Cập nhật người dùng' : 'Thêm người dùng' ?></button>
            <?php if ($editUser): ?><a class="btn btn--ghost" href="users.php">Hủy sửa</a><?php endif; ?>
          </div>
        </div>
      </form>
      
      <div class="table-wrapper">
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Họ tên</th>
              <th>Email</th>
              <th>Số điện thoại</th>
              <th>Vai trò</th>
              <th>Địa chỉ</th>
              <th>Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $user): ?>
              <tr>
                <td><?= h($user['CustomerID']) ?></td>
                <td><strong><?= h(($user['LastName'] ?? '') . ' ' . ($user['FirstName'] ?? '')) ?></strong></td>
                <td><?= h($user['Email']) ?></td>
                <td><?= h($user['Phone'] ?? 'Chưa cập nhật') ?></td>
                <td><span class="badge badge--info"><?= h($user['RoleName'] ?? 'Chưa xác định') ?></span></td>
                <td><?= h($user['Address'] ?? 'Chưa cập nhật') ?></td>
                <td class="table__actions">
                  <a class="btn btn--sm btn--outline" href="users.php?edit=<?= h($user['CustomerID']) ?>">Sửa</a>
                  <form method="post" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này?')" style="display:inline">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= h($user['CustomerID']) ?>">
                    <button class="btn btn--sm btn--danger" type="submit">Xóa</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</body>
</html>
