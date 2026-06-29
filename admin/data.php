<?php
session_start();

if (($_SESSION['admin_seed_version'] ?? 0) !== 2) {
  unset(
    $_SESSION['admin_products'],
    $_SESSION['admin_categories'],
    $_SESSION['admin_orders'],
    $_SESSION['admin_users'],
    $_SESSION['admin_coupons']
  );
  $_SESSION['admin_seed_version'] = 2;
}

if (!isset($_SESSION['admin_products'])) {
  $_SESSION['admin_products'] = [
    ['id' => 1, 'name' => 'Đắc Nhân Tâm', 'category' => 'Kỹ năng sống', 'price' => 89000, 'stock' => 35, 'status' => 'Đang bán'],
    ['id' => 2, 'name' => 'Nhà Giả Kim', 'category' => 'Tiểu thuyết', 'price' => 76000, 'stock' => 18, 'status' => 'Đang bán'],
    ['id' => 3, 'name' => 'Tư Duy Nhanh Và Chậm', 'category' => 'Kinh tế', 'price' => 159000, 'stock' => 12, 'status' => 'Đang bán'],
  ];
}

if (!isset($_SESSION['admin_categories'])) {
  $_SESSION['admin_categories'] = [
    ['id' => 1, 'name' => 'Tiểu thuyết', 'count' => 32, 'status' => 'Hiển thị'],
    ['id' => 2, 'name' => 'Kỹ năng sống', 'count' => 18, 'status' => 'Hiển thị'],
    ['id' => 3, 'name' => 'Kinh tế', 'count' => 11, 'status' => 'Hiển thị'],
  ];
}

if (!isset($_SESSION['admin_orders'])) {
  $_SESSION['admin_orders'] = [
    ['id' => 1, 'code' => 'DH001', 'customer' => 'Nguyễn Văn A', 'date' => '2026-01-12', 'total' => 245000, 'status' => 'Chờ xử lý'],
    ['id' => 2, 'code' => 'DH002', 'customer' => 'Trần Thị B', 'date' => '2026-02-09', 'total' => 389000, 'status' => 'Đã xác nhận'],
    ['id' => 3, 'code' => 'DH003', 'customer' => 'Lê Minh C', 'date' => '2026-05-18', 'total' => 520000, 'status' => 'Hoàn thành'],
    ['id' => 4, 'code' => 'DH004', 'customer' => 'Phạm Hà D', 'date' => '2026-06-22', 'total' => 176000, 'status' => 'Đang giao'],
  ];
}

if (!isset($_SESSION['admin_users'])) {
  $_SESSION['admin_users'] = [
    ['id' => 1, 'name' => 'Admin', 'email' => 'admin@gmail.com', 'role' => 'Quản trị viên', 'status' => 'Hoạt động'],
    ['id' => 2, 'name' => 'Nguyễn Văn A', 'email' => 'vana@gmail.com', 'role' => 'Khách hàng', 'status' => 'Hoạt động'],
  ];
}

if (!isset($_SESSION['admin_coupons'])) {
  $_SESSION['admin_coupons'] = [
    ['id' => 1, 'code' => 'SALE10', 'discount' => '10%', 'start' => '2026-06-20', 'end' => '2026-06-30', 'status' => 'Đang áp dụng'],
    ['id' => 2, 'code' => 'FREESHIP', 'discount' => 'Miễn phí vận chuyển', 'start' => '2026-06-22', 'end' => '2026-06-28', 'status' => 'Sắp hết hạn'],
  ];
}

function nextId($items) {
  return empty($items) ? 1 : max(array_column($items, 'id')) + 1;
}

function findItem($key, $id) {
  foreach ($_SESSION[$key] as $item) {
    if ((int) $item['id'] === (int) $id) {
      return $item;
    }
  }

  return null;
}

function saveItem($key, $item) {
  foreach ($_SESSION[$key] as $index => $current) {
    if ((int) $current['id'] === (int) $item['id']) {
      $_SESSION[$key][$index] = $item;
      return;
    }
  }

  $_SESSION[$key][] = $item;
}

function deleteItem($key, $id) {
  $_SESSION[$key] = array_values(array_filter($_SESSION[$key], function ($item) use ($id) {
    return (int) $item['id'] !== (int) $id;
  }));
}

function monthlyOrderStats() {
  $stats = array_fill(1, 12, ['orders' => 0, 'revenue' => 0]);

  foreach ($_SESSION['admin_orders'] as $order) {
    $month = (int) date('n', strtotime($order['date']));
    $stats[$month]['orders']++;
    $stats[$month]['revenue'] += (int) $order['total'];
  }

  return $stats;
}

function countByField($items, $field) {
  $result = [];

  foreach ($items as $item) {
    $value = $item[$field] ?? 'Khác';
    $result[$value] = ($result[$value] ?? 0) + 1;
  }

  return $result;
}
?>
