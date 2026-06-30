<?php
require_once '../config/db.php';

$pageTitle = 'Lịch sử đơn hàng';
$extraCss = ['css/cart.css'];
include '../includes/header.php';

// Trạng thái lọc từ URL
$statusFilter = isset($_GET['status']) ? trim($_GET['status']) : 'all';
$searchPhone = isset($_GET['search_phone']) ? trim($_GET['search_phone']) : '';

$orders = [];
$customerId = isset($_SESSION['user']) ? intval($_SESSION['user']['id']) : 0;

// Xử lý truy vấn đơn hàng
if ($customerId > 0) {
    // Người dùng đăng nhập
    $sql = "SELECT o.OrderID, o.OrderDate, o.ShippingAddress, o.OrderStatus, o.TotalAmount, p.PaymentStatus, p.PaymentMethod 
            FROM `order` o
            LEFT JOIN `payment` p ON o.OrderID = p.OrderID
            WHERE o.CustomerID = ?";
    
    if ($statusFilter !== 'all') {
        $sql .= " AND o.OrderStatus = ?";
    }
    $sql .= " ORDER BY o.OrderDate DESC";
    
    $stmt = $conn->prepare($sql);
    if ($statusFilter !== 'all') {
        $stmt->bind_param("is", $customerId, $statusFilter);
    } else {
        $stmt->bind_param("i", $customerId);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    $stmt->close();
} elseif (!empty($searchPhone)) {
    // Khách vãng lai tra cứu bằng số điện thoại
    $phonePattern = "%SĐT: " . $searchPhone . "%";
    $sql = "SELECT o.OrderID, o.OrderDate, o.ShippingAddress, o.OrderStatus, o.TotalAmount, p.PaymentStatus, p.PaymentMethod 
            FROM `order` o
            LEFT JOIN `payment` p ON o.OrderID = p.OrderID
            WHERE o.CustomerID IS NULL AND o.ShippingAddress LIKE ?";
            
    if ($statusFilter !== 'all') {
        $sql .= " AND o.OrderStatus = ?";
    }
    $sql .= " ORDER BY o.OrderDate DESC";
    
    $stmt = $conn->prepare($sql);
    if ($statusFilter !== 'all') {
        $stmt->bind_param("ss", $phonePattern, $statusFilter);
    } else {
        $stmt->bind_param("s", $phonePattern);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    $stmt->close();
}

// Lấy danh sách chi tiết sản phẩm cho các đơn hàng tìm thấy
$orderDetails = [];
if (!empty($orders)) {
    $orderIds = array_column($orders, 'OrderID');
    $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
    
    $sqlDetails = "
        SELECT od.OrderID, od.ProductID, od.Quantity, od.Price, od.UnitPrice, p.ProductName, i.ImageURL 
        FROM `order_detail` od
        JOIN `product` p ON od.ProductID = p.ProductID
        LEFT JOIN `image` i ON p.ProductID = i.ProductID AND i.IsThumbnail = 1
        WHERE od.OrderID IN ($placeholders)
    ";
    
    $stmtD = $conn->prepare($sqlDetails);
    $types = str_repeat('i', count($orderIds));
    $stmtD->bind_param($types, ...$orderIds);
    $stmtD->execute();
    $resD = $stmtD->get_result();
    
    while ($detail = $resD->fetch_assoc()) {
        $orderDetails[$detail['OrderID']][] = $detail;
    }
    $stmtD->close();
}

// Trực quan hóa tên trạng thái
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'Pending': return 'badge--info';
        case 'Processing': return 'badge--warning';
        case 'Shipped': return 'badge--primary';
        case 'Delivered': return 'badge--success';
        case 'Cancelled': return 'badge--error';
        default: return 'badge--secondary';
    }
}

function getStatusText($status) {
    switch ($status) {
        case 'Pending': return 'Chờ xác nhận';
        case 'Processing': return 'Đang đóng gói';
        case 'Shipped': return 'Đang vận chuyển';
        case 'Delivered': return 'Giao thành công';
        case 'Cancelled': return 'Đã hủy đơn';
        default: return 'Chưa rõ';
    }
}
?>

<main class="order-container">
    <ul class="breadcrumbs">
        <li><a href="<?= url('/') ?>">Trang chủ</a></li>
        <?php if ($customerId > 0): ?>
            <li><a href="<?= url('auth/pages/profile.php') ?>">Tài khoản</a></li>
        <?php endif; ?>
        <li>Lịch sử đơn hàng</li>
    </ul>

    <div class="order-title-section">
        <h1 class="order-title"><i class="fa-solid fa-clipboard-list" style="margin-right: 10px; color: var(--color-primary);"></i>Lịch sử đơn hàng</h1>
    </div>

    <!-- Giao diện tra cứu cho Khách vãng lai -->
    <?php if ($customerId <= 0): ?>
        <div style="background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--border-radius-lg); padding: var(--spacing-lg); margin-bottom: var(--spacing-lg); box-shadow: var(--box-shadow-sm);">
            <h3 style="margin-top: 0; margin-bottom: var(--spacing-sm); color: var(--color-primary);"><i class="fa-solid fa-magnifying-glass" style="margin-right: 8px;"></i>Tra cứu đơn hàng của Khách vãng lai</h3>
            <p style="font-size: 0.9rem; color: var(--color-text-light); margin-bottom: var(--spacing-md);">Vui lòng nhập số điện thoại bạn dùng khi đặt hàng để kiểm tra lịch sử và hành trình đơn hàng.</p>
            <form method="GET" style="display: flex; gap: var(--spacing-xs); max-width: 450px;">
                <input type="text" name="search_phone" class="form-control" placeholder="Nhập số điện thoại đặt hàng..." required value="<?= htmlspecialchars($searchPhone) ?>">
                <button type="submit" class="btn btn--primary" style="padding: 10px 24px; font-weight: bold; white-space: nowrap;">Tra cứu</button>
            </form>
        </div>
    <?php endif; ?>

    <!-- Bộ lọc trạng thái đơn hàng -->
    <?php if ($customerId > 0 || !empty($searchPhone)): ?>
        <div class="order-tabs">
            <?php 
            $statuses = [
                'all' => 'Tất cả',
                'Pending' => 'Chờ xác nhận',
                'Processing' => 'Đang đóng gói',
                'Shipped' => 'Đang giao',
                'Delivered' => 'Đã giao',
                'Cancelled' => 'Đã hủy'
            ];
            foreach ($statuses as $val => $label): 
                $activeClass = ($statusFilter === $val) ? 'is-active' : '';
                $queryStr = "?status=" . $val;
                if ($customerId <= 0 && !empty($searchPhone)) {
                    $queryStr .= "&search_phone=" . urlencode($searchPhone);
                }
            ?>
                <a href="<?= $queryStr ?>" class="order-tab-item <?= $activeClass ?>"><?= $label ?></a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Danh sách đơn hàng -->
    <div class="order-card-list">
        <?php if (!empty($orders)): ?>
            <?php foreach ($orders as $order): 
                $oId = $order['OrderID'];
                $items = $orderDetails[$oId] ?? [];
                $totalQty = array_sum(array_column($items, 'Quantity'));
            ?>
                <div class="order-card">
                    <div class="order-card__header">
                        <div class="order-card__header-left">
                            <span class="order-card__id">Đơn hàng: #WBS-<?= $oId ?></span>
                            <span class="order-card__date">Đặt ngày: <?= date('d/m/Y H:i', strtotime($order['OrderDate'])) ?></span>
                        </div>
                        <div style="display: flex; gap: 8px; align-items: center;">
                            <span class="badge <?= getStatusBadgeClass($order['OrderStatus']) ?> badge--dot">
                                <?= getStatusText($order['OrderStatus']) ?>
                            </span>
                            <span class="badge badge--secondary">
                                <?= $order['PaymentStatus'] === 'Completed' ? 'Đã thanh toán trực tuyến' : 'Thanh toán COD' ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="order-card__products">
                        <?php foreach ($items as $item): 
                            $imgSrc = !empty($item['ImageURL']) ? url('assets' . $item['ImageURL']) : asset('images/default-book.png');
                        ?>
                            <div class="order-card__product-row" style="display: flex; align-items: center; justify-content: space-between; gap: var(--spacing-md); border-bottom: 1px dashed var(--color-border); padding-bottom: var(--spacing-sm); margin-bottom: 2px; width: 100%;">
                                <a href="<?= url('cart/detail.php?id=' . $oId) ?>" class="order-card__product" style="flex: 1;">
                                    <img class="order-card__product-img" src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($item['ProductName']) ?>" style="width: 50px; height: 68px; object-fit: contain; background: var(--color-background); border-radius: var(--border-radius-sm); border: 1px solid var(--color-border); padding: 2px;">
                                    <div class="order-card__product-info">
                                        <h3 class="order-card__product-title"><?= htmlspecialchars($item['ProductName']) ?></h3>
                                        <span class="order-card__product-meta">Đơn giá: <?= number_format($item['UnitPrice'], 0, ',', '.') ?> đ</span>
                                    </div>
                                    <div class="order-card__product-price">
                                        <span class="order-card__product-price-current"><?= number_format($item['Price'], 0, ',', '.') ?> đ</span>
                                        <div class="order-card__product-price-qty">x <?= $item['Quantity'] ?></div>
                                    </div>
                                </a>
                                <?php if ($order['OrderStatus'] === 'Delivered'): ?>
                                    <div style="flex-shrink: 0; padding-left: var(--spacing-md);">
                                        <a href="<?= url('trangchu/detail.php?id=' . $item['ProductID']) ?>#review-form-section" class="btn btn--outline btn--sm" style="display: inline-flex; align-items: center; gap: 4px; padding: 6px 12px; font-size: 0.8rem; font-weight: bold; border-color: var(--color-secondary); color: var(--color-text); text-decoration: none; border-radius: var(--border-radius-sm);">
                                            <i class="fa-solid fa-star" style="color: var(--color-secondary);"></i> Đánh giá
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="order-card__footer">
                        <span class="order-card__summary">
                            Tổng cộng: <strong><?= $totalQty ?> sản phẩm</strong>
                            <span class="order-card__total-price">Tổng thanh toán: <?= number_format($order['TotalAmount'], 0, ',', '.') ?> đ</span>
                        </span>
                        <div class="btn-group">
                            <a href="<?= url('cart/detail.php?id=' . $oId) ?>" class="btn btn--outline btn--sm">Chi tiết đơn</a>
                            <a href="<?= url('cart/tracking.php?id=' . $oId) ?>" class="btn btn--primary btn--sm">Theo dõi vận chuyển</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 60px var(--spacing-md); background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--border-radius-lg); box-shadow: var(--box-shadow-sm);">
                <i class="fa-solid fa-folder-open" style="font-size: 3.5rem; display: block; margin-bottom: var(--spacing-sm); color: var(--color-text-light);"></i>
                <?php if ($customerId <= 0 && empty($searchPhone)): ?>
                    <h3>Vui lòng nhập số điện thoại để tra cứu</h3>
                    <p style="color: var(--color-text-light);">Nhập thông tin tại ô tra cứu phía trên.</p>
                <?php else: ?>
                    <h3>Không tìm thấy đơn hàng nào!</h3>
                    <p style="color: var(--color-text-light);">Bạn chưa thực hiện giao dịch nào hoặc đơn hàng thuộc trạng thái khác.</p>
                    <a href="<?= url('trangchu/index.php') ?>" class="btn btn--primary" style="margin-top: var(--spacing-md); font-weight: bold; padding: 10px 24px;">Mua sách ngay</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
</body>
</html>