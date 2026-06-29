<?php
require_once 'data.php';
require_once 'partials.php';

$editCoupon = isset($_GET['edit']) ? findItem('admin_coupons', $_GET['edit']) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'save') {
    $id = $_POST['id'] !== '' ? (int) $_POST['id'] : nextId($_SESSION['admin_coupons']);
    saveItem('admin_coupons', ['id' => $id, 'code' => strtoupper(trim($_POST['code'])), 'discount' => trim($_POST['discount']), 'start' => $_POST['start'], 'end' => $_POST['end'], 'status' => trim($_POST['status'])]);
  }

  if ($action === 'delete') {
    deleteItem('admin_coupons', $_POST['id']);
  }

  redirectTo('coupons.php');
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản lý mã giảm giá</title><?= adminCssLinks() ?>
</head>
<body>
  <div class="app-layout">
    <?php adminSidebar(); ?>
    <main class="page-content">
      <header class="page-header"><div><h1>Quản lý mã giảm giá</h1><p>Thêm, sửa, xóa mã giảm giá</p></div></header>
      <form method="post" class="card"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= h($editCoupon['id'] ?? '') ?>"><div class="card__body form"><div class="form-row"><div class="form-group"><label class="form-label">Mã giảm giá</label><input class="form-control" name="code" value="<?= h($editCoupon['code'] ?? '') ?>" required></div><div class="form-group"><label class="form-label">Mức giảm</label><input class="form-control" name="discount" value="<?= h($editCoupon['discount'] ?? '') ?>" required></div></div><div class="form-row"><div class="form-group"><label class="form-label">Ngày bắt đầu</label><input class="form-control" type="date" name="start" value="<?= h($editCoupon['start'] ?? date('Y-m-d')) ?>" required></div><div class="form-group"><label class="form-label">Ngày kết thúc</label><input class="form-control" type="date" name="end" value="<?= h($editCoupon['end'] ?? date('Y-m-d')) ?>" required></div><div class="form-group"><label class="form-label">Trạng thái</label><select class="form-control" name="status"><?php foreach (['Đang áp dụng', 'Sắp hết hạn', 'Đã hết hạn'] as $status): ?><option value="<?= h($status) ?>" <?= ($editCoupon['status'] ?? 'Đang áp dụng') === $status ? 'selected' : '' ?>><?= h($status) ?></option><?php endforeach; ?></select></div></div><div class="btn-group"><button class="btn btn--primary" type="submit"><?= $editCoupon ? 'Cập nhật mã giảm giá' : 'Thêm mã giảm giá' ?></button><?php if ($editCoupon): ?><a class="btn btn--ghost" href="coupons.php">Hủy sửa</a><?php endif; ?></div></div></form>
      <div class="table-wrapper">
        <table class="table">
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
            <?php foreach ($_SESSION['admin_coupons'] as $coupon): ?>
              <tr>
                <td><?= h($coupon['code']) ?></td>
                <td><?= h($coupon['discount']) ?></td>
                <td><?= date('d/m/Y', strtotime($coupon['start'])) ?></td>
                <td><?= date('d/m/Y', strtotime($coupon['end'])) ?></td>
                <td><span class="badge <?= badgeClass($coupon['status']) ?>"><?= h($coupon['status']) ?></span></td>
                <td class="table__actions">
                  <a class="btn btn--sm btn--outline" href="coupons.php?edit=<?= h($coupon['id']) ?>">Sửa</a>
                  <form method="post" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')" style="display:inline">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= h($coupon['id']) ?>">
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
