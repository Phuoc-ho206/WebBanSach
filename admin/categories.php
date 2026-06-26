<?php
require_once 'data.php';
require_once 'partials.php';

$editCategory = isset($_GET['edit']) ? findItem('admin_categories', $_GET['edit']) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'save') {
    $id = $_POST['id'] !== '' ? (int) $_POST['id'] : nextId($_SESSION['admin_categories']);
    saveItem('admin_categories', [
      'id' => $id,
      'name' => trim($_POST['name']),
      'count' => (int) $_POST['count'],
      'status' => trim($_POST['status']),
    ]);
  }

  if ($action === 'delete') {
    deleteItem('admin_categories', $_POST['id']);
  }

  redirectTo('categories.php');
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản lý danh mục</title><?= adminCssLinks() ?>
</head>
<body>
  <div class="app-layout">
    <?php adminSidebar(); ?>
    <main class="page-content">
      <header class="page-header"><div><h1>Quản lý danh mục</h1><p>Thêm, sửa, xóa danh mục sách</p></div></header>
      <form method="post" class="card"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= h($editCategory['id'] ?? '') ?>"><div class="card__body form"><div class="form-row"><div class="form-group"><label class="form-label">Tên danh mục</label><input class="form-control" type="text" name="name" value="<?= h($editCategory['name'] ?? '') ?>" required></div><div class="form-group"><label class="form-label">Số sản phẩm</label><input class="form-control" type="number" name="count" value="<?= h($editCategory['count'] ?? 0) ?>" required></div><div class="form-group"><label class="form-label">Trạng thái</label><select class="form-control" name="status"><?php foreach (['Hiển thị', 'Ẩn'] as $status): ?><option value="<?= h($status) ?>" <?= ($editCategory['status'] ?? 'Hiển thị') === $status ? 'selected' : '' ?>><?= h($status) ?></option><?php endforeach; ?></select></div></div><div class="btn-group"><button class="btn btn--primary" type="submit"><?= $editCategory ? 'Cập nhật danh mục' : 'Thêm danh mục' ?></button><?php if ($editCategory): ?><a class="btn btn--ghost" href="categories.php">Hủy sửa</a><?php endif; ?></div></div></form>
      <div class="table-wrapper"><table class="table"><thead><tr><th>ID</th><th>Tên danh mục</th><th>Sản phẩm</th><th>Trạng thái</th><th>Thao tác</th></tr></thead><tbody><?php foreach ($_SESSION['admin_categories'] as $category): ?><tr><td><?= h($category['id']) ?></td><td><?= h($category['name']) ?></td><td><?= h($category['count']) ?></td><td><span class="badge <?= badgeClass($category['status']) ?>"><?= h($category['status']) ?></span></td><td class="table__actions"><a class="btn btn--sm btn--outline" href="categories.php?edit=<?= h($category['id']) ?>">Sửa</a><form method="post" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= h($category['id']) ?>"><button class="btn btn--sm btn--danger" type="submit">Xóa</button></form></td></tr><?php endforeach; ?></tbody></table></div>
    </main>
  </div>
</body>
</html>
