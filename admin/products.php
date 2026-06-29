<?php
require_once 'data.php';
require_once 'partials.php';

$editProduct = isset($_GET['edit']) ? findItem('admin_products', $_GET['edit']) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'save') {
    $id = $_POST['id'] !== '' ? (int) $_POST['id'] : nextId($_SESSION['admin_products']);
    saveItem('admin_products', [
      'id' => $id,
      'name' => trim($_POST['name']),
      'category' => trim($_POST['category']),
      'price' => (int) $_POST['price'],
      'stock' => (int) $_POST['stock'],
      'status' => trim($_POST['status']),
    ]);
  }

  if ($action === 'delete') {
    deleteItem('admin_products', $_POST['id']);
  }

  redirectTo('products.php');
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản lý sản phẩm</title><?= adminCssLinks() ?>
</head>
<body>
  <div class="app-layout">
    <?php adminSidebar(); ?>

    <main class="page-content">
      <header class="page-header"><div><h1>Quản lý sản phẩm</h1><p>Thêm, sửa, xóa sản phẩm sách</p></div></header>

      <form method="post" class="card">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= h($editProduct['id'] ?? '') ?>">
        <div class="card__body form">
          <div class="form-row">
            <div class="form-group"><label class="form-label">Tên sách</label><input class="form-control" type="text" name="name" value="<?= h($editProduct['name'] ?? '') ?>" required></div>
            <div class="form-group"><label class="form-label">Danh mục</label><input class="form-control" type="text" name="category" value="<?= h($editProduct['category'] ?? '') ?>" required></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label class="form-label">Giá</label><div class="input-unit"><input class="form-control" type="number" name="price" value="<?= h($editProduct['price'] ?? '') ?>" required><span class="input-unit__text">đ</span></div></div>
            <div class="form-group"><label class="form-label">Tồn kho</label><div class="input-unit"><input class="form-control" type="number" name="stock" value="<?= h($editProduct['stock'] ?? '') ?>" required><span class="input-unit__text">Cuốn</span></div></div>
            <div class="form-group"><label class="form-label">Trạng thái</label><select class="form-control" name="status"><?php foreach (['Đang bán', 'Ngừng bán'] as $status): ?><option value="<?= h($status) ?>" <?= ($editProduct['status'] ?? 'Đang bán') === $status ? 'selected' : '' ?>><?= h($status) ?></option><?php endforeach; ?></select></div>
          </div>
          <div class="btn-group"><button class="btn btn--primary" type="submit"><?= $editProduct ? 'Cập nhật sản phẩm' : 'Thêm sản phẩm' ?></button><?php if ($editProduct): ?><a class="btn btn--ghost" href="products.php">Hủy sửa</a><?php endif; ?></div>
        </div>
      </form>

      <div class="table-wrapper"><table class="table"><thead><tr><th>ID</th><th>Tên sách</th><th>Danh mục</th><th>Giá</th><th>Tồn kho</th><th>Trạng thái</th><th>Thao tác</th></tr></thead><tbody>
        <?php foreach ($_SESSION['admin_products'] as $product): ?>
          <tr><td><?= h($product['id']) ?></td><td><?= h($product['name']) ?></td><td><?= h($product['category']) ?></td><td><?= number_format($product['price'], 0, ',', '.') ?> đ</td><td><?= h($product['stock']) ?></td><td><span class="badge <?= badgeClass($product['status']) ?>"><?= h($product['status']) ?></span></td><td class="table__actions"><a class="btn btn--sm btn--outline" href="products.php?edit=<?= h($product['id']) ?>">Sửa</a><form method="post" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= h($product['id']) ?>"><button class="btn btn--sm btn--danger" type="submit">Xóa</button></form></td></tr>
        <?php endforeach; ?>
      </tbody></table></div>
    </main>
  </div>
</body>
</html>
