<?php
require_once 'data.php';
require_once 'partials.php';

$editOrder = isset($_GET['edit']) ? findItem('admin_orders', $_GET['edit']) : null;
$orderStatuses = ['Chờ xử lý', 'Đã xác nhận', 'Đang giao', 'Hoàn thành', 'Đã hủy'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'save') {
    $id = $_POST['id'] !== '' ? (int) $_POST['id'] : nextId($_SESSION['admin_orders']);
    saveItem('admin_orders', [
      'id' => $id,
      'code' => strtoupper(trim($_POST['code'])),
      'customer' => trim($_POST['customer']),
      'date' => $_POST['date'],
      'total' => (int) $_POST['total'],
      'status' => trim($_POST['status']),
    ]);
  }

  if ($action === 'delete') {
    deleteItem('admin_orders', $_POST['id']);
    if (isset($_POST['ajax'])) {
      header('Content-Type: application/json');
      echo json_encode(['success' => true]);
      exit;
    }
  }

  redirectTo('orders.php');
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản lý đơn hàng</title><?= adminCssLinks() ?>
</head>
<body>
  <div class="app-layout">
    <?php adminSidebar(); ?>

    <main class="page-content">
      <header class="page-header">
        <div>
          <h1>Quản lý đơn hàng</h1>
          <p>Thêm, sửa, xóa đơn hàng và cập nhật trạng thái</p>
        </div>
      </header>

      <form method="post" class="card">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= h($editOrder['id'] ?? '') ?>">
        <div class="card__body form">
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Mã đơn</label>
              <input class="form-control" name="code" value="<?= h($editOrder['code'] ?? 'DH' . str_pad((string) nextId($_SESSION['admin_orders']), 3, '0', STR_PAD_LEFT)) ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Khách hàng</label>
              <input class="form-control" name="customer" value="<?= h($editOrder['customer'] ?? '') ?>" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Ngày đặt</label>
              <input class="form-control" type="date" name="date" value="<?= h($editOrder['date'] ?? date('Y-m-d')) ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Tổng tiền</label>
              <div class="input-unit">
                <input class="form-control" type="number" min="0" name="total" value="<?= h($editOrder['total'] ?? 0) ?>" required>
                <span class="input-unit__text">đ</span>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Trạng thái</label>
              <select class="form-control" name="status">
                <?php foreach ($orderStatuses as $status): ?>
                  <option value="<?= h($status) ?>" <?= ($editOrder['status'] ?? 'Chờ xử lý') === $status ? 'selected' : '' ?>><?= h($status) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="btn-group">
            <button class="btn btn--primary" type="submit"><?= $editOrder ? 'Cập nhật đơn hàng' : 'Thêm đơn hàng' ?></button>
            <?php if ($editOrder): ?><a class="btn btn--ghost" href="orders.php">Hủy sửa</a><?php endif; ?>
          </div>
        </div>
      </form>

      <div class="table-wrapper">
        <table class="table">
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
            <?php foreach ($_SESSION['admin_orders'] as $order): ?>
              <tr>
                <td><?= h($order['code']) ?></td>
                <td><?= h($order['customer']) ?></td>
                <td><?= date('d/m/Y', strtotime($order['date'])) ?></td>
                <td><?= number_format($order['total'], 0, ',', '.') ?> đ</td>
                <td><span class="badge <?= badgeClass($order['status']) ?>"><?= h($order['status']) ?></span></td>
                <td class="table__actions">
                  <a class="btn btn--sm btn--outline" href="orders.php?edit=<?= h($order['id']) ?>">Sửa</a>
                  <form method="post" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này?')">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= h($order['id']) ?>">
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
