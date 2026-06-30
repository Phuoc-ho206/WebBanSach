<?php
require_once 'data.php';
require_once 'partials.php';

$statusMapping = [
  'Pending' => 'Chờ xác nhận',
  'Processing' => 'Đã xác nhận',
  'Shipped' => 'Đang giao',
  'Delivered' => 'Hoàn thành',
  'Cancelled' => 'Đã hủy'
];

// Hàm tách tên từ địa chỉ giao nhận của khách vãng lai
function getCustomerNameFromAddress($address, $default = 'Khách vãng lai') {
  if (preg_match('/Người nhận:\s*([^|]+)/i', $address, $matches)) {
    return trim($matches[1]);
  }
  return $default;
}

$editOrder = null;
$orderItems = [];
$editOrderCustomerName = '';

if (isset($_GET['edit'])) {
  $stmt = $conn->prepare("SELECT * FROM `order` WHERE OrderID = ?");
  $stmt->bind_param("i", $_GET['edit']);
  $stmt->execute();
  $editOrder = $stmt->get_result()->fetch_assoc();
  $stmt->close();

  if ($editOrder) {
    // Lấy tên khách hàng
    if ($editOrder['CustomerID']) {
      $stmtUser = $conn->prepare("SELECT LastName, FirstName FROM user WHERE CustomerID = ?");
      $stmtUser->bind_param("i", $editOrder['CustomerID']);
      $stmtUser->execute();
      $resUser = $stmtUser->get_result()->fetch_assoc();
      $editOrderCustomerName = $resUser ? trim($resUser['LastName'] . ' ' . $resUser['FirstName']) : 'N/A';
      $stmtUser->close();
    } else {
      $editOrderCustomerName = getCustomerNameFromAddress($editOrder['ShippingAddress']);
    }

    // Lấy danh sách sản phẩm trong đơn hàng
    $resItems = $conn->query("
      SELECT od.Quantity, od.Price, od.UnitPrice, p.ProductName 
      FROM order_detail od 
      LEFT JOIN product p ON od.ProductID = p.ProductID 
      WHERE od.OrderID = " . (int)$editOrder['OrderID']
    );
    if ($resItems) {
      while ($row = $resItems->fetch_assoc()) {
        $orderItems[] = $row;
      }
    }
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'save') {
    $id = (int)$_POST['id'];
    $status = trim($_POST['status']);

    // Lấy trạng thái cũ để kiểm tra xem có thay đổi hay không
    $stmtOld = $conn->prepare("SELECT OrderStatus FROM `order` WHERE OrderID = ?");
    $stmtOld->bind_param("i", $id);
    $stmtOld->execute();
    $oldOrder = $stmtOld->get_result()->fetch_assoc();
    $stmtOld->close();
    $oldStatus = $oldOrder['OrderStatus'] ?? '';

    if ($oldStatus !== $status) {
      // Bắt đầu Transaction để đảm bảo tính toàn vẹn
      $conn->begin_transaction();
      try {
        // Cập nhật trạng thái đơn hàng
        $stmt = $conn->prepare("UPDATE `order` SET OrderStatus = ? WHERE OrderID = ?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        $stmt->close();

        // Đồng bộ hóa với bảng payment và delivery
        if ($status === 'Delivered') {
          $conn->query("UPDATE payment SET PaymentStatus = 'Completed', PaymentDate = NOW() WHERE OrderID = $id");
          $conn->query("UPDATE delivery SET DeliveryStatus = 'Delivered', DeliveryDate = NOW() WHERE OrderID = $id");
        } elseif ($status === 'Cancelled') {
          $resPay = $conn->query("SELECT PaymentStatus FROM payment WHERE OrderID = $id LIMIT 1");
          $newPayStatus = 'Failed';
          if ($resPay && $rowPay = $resPay->fetch_assoc()) {
            if ($rowPay['PaymentStatus'] === 'Completed') {
              $newPayStatus = 'Refunded';
            }
          }
          $conn->query("UPDATE payment SET PaymentStatus = '$newPayStatus' WHERE OrderID = $id");
          $conn->query("UPDATE delivery SET DeliveryStatus = 'Failed' WHERE OrderID = $id");

          // Hoàn trả lại số lượng tồn kho sản phẩm nếu hủy đơn (chỉ thực hiện nếu đơn chưa hoàn thành trước đó)
          if ($oldStatus !== 'Delivered' && $oldStatus !== 'Cancelled') {
            $resDetails = $conn->query("SELECT ProductID, Quantity FROM order_detail WHERE OrderID = $id");
            if ($resDetails) {
              while ($detail = $resDetails->fetch_assoc()) {
                $pId = (int)$detail['ProductID'];
                $qty = (int)$detail['Quantity'];
                $conn->query("UPDATE product SET Quantity = Quantity + $qty, Status = 'Còn hàng' WHERE ProductID = $pId");
              }
            }
          }
        } elseif ($status === 'Shipped') {
          $conn->query("UPDATE delivery SET DeliveryStatus = 'Shipping' WHERE OrderID = $id");
        } elseif ($status === 'Processing') {
          $conn->query("UPDATE delivery SET DeliveryStatus = 'Preparing' WHERE OrderID = $id");
        }

        $conn->commit();
      } catch (Exception $e) {
        $conn->rollback();
      }
    }
  }

  if ($action === 'delete') {
    $id = (int)$_POST['id'];
    $conn->query("DELETE FROM `order` WHERE OrderID = $id");
  }

  redirectTo('orders.php');
}

// Lấy danh sách đơn hàng thực tế
$orders = [];
$res = $conn->query("
  SELECT o.OrderID, o.CustomerID, o.OrderDate, o.ShippingAddress, o.OrderStatus, o.TotalAmount,
         u.LastName, u.FirstName, p.PaymentMethod, p.PaymentStatus, d.DeliveryStatus
  FROM `order` o
  LEFT JOIN user u ON o.CustomerID = u.CustomerID
  LEFT JOIN payment p ON o.OrderID = p.OrderID
  LEFT JOIN delivery d ON o.OrderID = d.OrderID
  ORDER BY o.OrderID DESC
");
if ($res) {
  while ($row = $res->fetch_assoc()) {
    $orders[] = $row;
  }
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
          <p>Xem chi tiết đơn hàng và cập nhật trạng thái duyệt đơn</p>
        </div>
      </header>

      <?php if ($editOrder): ?>
        <form method="post" class="card">
          <input type="hidden" name="action" value="save">
          <input type="hidden" name="id" value="<?= h($editOrder['OrderID']) ?>">
          <div class="card__body form">
            <h2 style="font-size: 1.2rem; margin-bottom: var(--spacing-sm); color: var(--color-primary);">Chi tiết đơn hàng #WBS-<?= h($editOrder['OrderID']) ?></h2>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Khách hàng</label>
                <input class="form-control" value="<?= h($editOrderCustomerName) ?>" readonly style="background-color: var(--color-bg-light); cursor: not-allowed;">
              </div>
              <div class="form-group">
                <label class="form-label">Ngày đặt</label>
                <input class="form-control" value="<?= date('d/m/Y H:i', strtotime($editOrder['OrderDate'])) ?>" readonly style="background-color: var(--color-bg-light); cursor: not-allowed;">
              </div>
              <div class="form-group">
                <label class="form-label">Tổng tiền</label>
                <input class="form-control" value="<?= number_format($editOrder['TotalAmount'], 0, ',', '.') ?> đ" readonly style="background-color: var(--color-bg-light); cursor: not-allowed;">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group" style="flex: 2;">
                <label class="form-label">Địa chỉ giao hàng</label>
                <textarea class="form-control" readonly style="background-color: var(--color-bg-light); cursor: not-allowed;" rows="2"><?= h($editOrder['ShippingAddress']) ?></textarea>
              </div>
              <div class="form-group" style="flex: 1;">
                <label class="form-label">Duyệt trạng thái</label>
                <select class="form-control" name="status">
                  <?php foreach ($statusMapping as $key => $label): ?>
                    <option value="<?= h($key) ?>" <?= $editOrder['OrderStatus'] === $key ? 'selected' : '' ?>><?= h($label) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div style="margin-top: var(--spacing-md); border-top: 1px solid var(--color-border); padding-top: var(--spacing-md);">
              <h3 style="font-size: 1rem; margin-bottom: var(--spacing-sm);">Sản phẩm đã mua</h3>
              <div class="table-wrapper">
                <table class="table" style="font-size: 0.9rem;">
                  <thead>
                    <tr>
                      <th>Tên sách</th>
                      <th>Đơn giá</th>
                      <th>Số lượng</th>
                      <th>Thành tiền</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($orderItems as $item): ?>
                      <tr>
                        <td><strong><?= h($item['ProductName']) ?></strong></td>
                        <td><?= number_format($item['UnitPrice'], 0, ',', '.') ?> đ</td>
                        <td><?= h($item['Quantity']) ?></td>
                        <td><?= number_format($item['Price'], 0, ',', '.') ?> đ</td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="btn-group" style="margin-top: var(--spacing-md);">
              <button class="btn btn--primary" type="submit">Cập nhật trạng thái</button>
              <a class="btn btn--ghost" href="orders.php">Đóng chi tiết</a>
            </div>
          </div>
        </form>
      <?php else: ?>
        <div class="card" style="margin-bottom: var(--spacing-md); padding: var(--spacing-md); text-align: center; color: var(--color-text-light);">
          <p>Vui lòng chọn nút "Xem chi tiết" của đơn hàng dưới đây để kiểm duyệt sản phẩm và cập nhật trạng thái.</p>
        </div>
      <?php endif; ?>

      <div class="table-wrapper">
        <table class="table">
          <thead>
            <tr>
              <th>Mã đơn</th>
              <th>Khách hàng</th>
              <th>Ngày đặt</th>
              <th>Tổng tiền</th>
              <th>Thanh toán</th>
              <th>Trạng thái</th>
              <th>Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $order): 
              $customerName = $order['CustomerID'] 
                ? trim($order['LastName'] . ' ' . $order['FirstName']) 
                : getCustomerNameFromAddress($order['ShippingAddress']);
              $vnStatus = $statusMapping[$order['OrderStatus']] ?? $order['OrderStatus'];
            ?>
              <tr>
                <td>#WBS-<?= h($order['OrderID']) ?></td>
                <td><strong><?= h($customerName) ?></strong></td>
                <td><?= date('d/m/Y H:i', strtotime($order['OrderDate'])) ?></td>
                <td><?= number_format($order['TotalAmount'], 0, ',', '.') ?> đ</td>
                <td>
                  <small>
                    Phương thức: <?= h($order['PaymentMethod'] ?? 'N/A') ?><br>
                    Trạng thái: <em><?= h($order['PaymentStatus'] ?? 'Pending') ?></em>
                  </small>
                </td>
                <td><span class="badge <?= badgeClass($vnStatus) ?>"><?= h($vnStatus) ?></span></td>
                <td class="table__actions">
                  <a class="btn btn--sm btn--outline" href="orders.php?edit=<?= h($order['OrderID']) ?>">Xem chi tiết</a>
                  <form method="post" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này?')" style="display:inline">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= h($order['OrderID']) ?>">
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
