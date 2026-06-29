<?php
require_once 'data.php';
require_once 'partials.php';

$editUser = isset($_GET['edit']) ? findItem('admin_users', $_GET['edit']) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'save') {
    $id = $_POST['id'] !== '' ? (int) $_POST['id'] : nextId($_SESSION['admin_users']);
    saveItem('admin_users', ['id' => $id, 'name' => trim($_POST['name']), 'email' => trim($_POST['email']), 'role' => trim($_POST['role']), 'status' => trim($_POST['status'])]);
  }

  if ($action === 'delete') {
    deleteItem('admin_users', $_POST['id']);
  }

  redirectTo('users.php');
}
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
      <form method="post" class="card"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= h($editUser['id'] ?? '') ?>"><div class="card__body form"><div class="form-row"><div class="form-group"><label class="form-label">Họ tên</label><input class="form-control" name="name" value="<?= h($editUser['name'] ?? '') ?>" required></div><div class="form-group"><label class="form-label">Email</label><input class="form-control" type="email" name="email" value="<?= h($editUser['email'] ?? '') ?>" required></div></div><div class="form-row"><div class="form-group"><label class="form-label">Vai trò</label><select class="form-control" name="role"><?php foreach (['Quản trị viên', 'Nhân viên', 'Khách hàng'] as $role): ?><option value="<?= h($role) ?>" <?= ($editUser['role'] ?? 'Khách hàng') === $role ? 'selected' : '' ?>><?= h($role) ?></option><?php endforeach; ?></select></div><div class="form-group"><label class="form-label">Trạng thái</label><select class="form-control" name="status"><?php foreach (['Hoạt động', 'Đã khóa'] as $status): ?><option value="<?= h($status) ?>" <?= ($editUser['status'] ?? 'Hoạt động') === $status ? 'selected' : '' ?>><?= h($status) ?></option><?php endforeach; ?></select></div></div><div class="btn-group"><button class="btn btn--primary" type="submit"><?= $editUser ? 'Cập nhật người dùng' : 'Thêm người dùng' ?></button><?php if ($editUser): ?><a class="btn btn--ghost" href="users.php">Hủy sửa</a><?php endif; ?></div></div></form>
      <div class="table-wrapper"><table class="table"><thead><tr><th>ID</th><th>Họ tên</th><th>Email</th><th>Vai trò</th><th>Trạng thái</th><th>Thao tác</th></tr></thead><tbody><?php foreach ($_SESSION['admin_users'] as $user): ?><tr><td><?= h($user['id']) ?></td><td><?= h($user['name']) ?></td><td><?= h($user['email']) ?></td><td><?= h($user['role']) ?></td><td><span class="badge <?= badgeClass($user['status']) ?>"><?= h($user['status']) ?></span></td><td class="table__actions"><a class="btn btn--sm btn--outline" href="users.php?edit=<?= h($user['id']) ?>">Sửa</a><form method="post" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= h($user['id']) ?>"><button class="btn btn--sm btn--danger" type="submit">Xóa</button></form></td></tr><?php endforeach; ?></tbody></table></div>
    </main>
  </div>
</body>
</html>
