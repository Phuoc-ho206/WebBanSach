<?php
require_once '../config/db.php';

$orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($orderId <= 0) {
    header('Location: ' . url('cart/history.php'));
    exit;
}

// 1. Truy vấn thông tin đơn hàng
$sqlOrder = "
    SELECT o.OrderID, o.CustomerID, o.OrderDate, o.ShippingAddress, o.OrderStatus, o.TotalAmount,
           p.PaymentMethod, p.PaymentStatus, d.DeliveryStatus, d.ShippingFee
    FROM `order` o
    LEFT JOIN `payment` p ON o.OrderID = p.OrderID
    LEFT JOIN `delivery` d ON o.OrderID = d.OrderID
    WHERE o.OrderID = ?
";
$stmt = $conn->prepare($sqlOrder);
$stmt->bind_param("i", $orderId);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    die("<div style='text-align:center; padding:100px 20px;'><h3>Đơn hàng không tồn tại trên hệ thống!</h3><a href='index.php' class='btn btn--primary'>Quay lại trang chủ</a></div>");
}

$order = $res->fetch_assoc();
$stmt->close();

// Kiểm tra quyền truy cập đơn hàng
$currentCustomerId = isset($_SESSION['user']) ? intval($_SESSION['user']['id']) : 0;
if ($order['CustomerID'] !== null && $order['CustomerID'] !== $currentCustomerId) {
    die("<div style='text-align:center; padding:100px 20px;'><h3>Bạn không có quyền truy cập thông tin đơn hàng này!</h3><a href='history.php' class='btn btn--primary'>Lịch sử đơn hàng</a></div>");
}

// 2. Truy vấn chi tiết sản phẩm đã mua
$sqlDetails = "
    SELECT od.ProductID, od.Quantity, od.Price, od.UnitPrice, p.ProductName, i.ImageURL 
    FROM `order_detail` od
    JOIN `product` p ON od.ProductID = p.ProductID
    LEFT JOIN `image` i ON p.ProductID = i.ProductID AND i.IsThumbnail = 1
    WHERE od.OrderID = ?
";
$stmtD = $conn->prepare($sqlDetails);
$stmtD->bind_param("i", $orderId);
$stmtD->execute();
$resD = $stmtD->get_result();

$items = [];
while ($row = $resD->fetch_assoc()) {
    $items[] = $row;
}
$stmtD->close();

$pageTitle = 'Chi tiết đơn hàng #WBS-' . $orderId;
$extraCss = ['css/cart.css'];
include '../includes/header.php';

// Các hàm tiện ích đổi text trạng thái
function getOrderStatusText($status) {
    switch ($status) {
        case 'Pending': return 'Chờ xác nhận';
        case 'Processing': return 'Đang đóng gói';
        case 'Shipped': return 'Đang vận chuyển';
        case 'Delivered': return 'Giao thành công';
        case 'Cancelled': return 'Đã hủy đơn';
        default: return 'Chưa rõ';
    }
}

function getPaymentStatusText($status) {
    switch ($status) {
        case 'Pending': return 'Chờ thanh toán';
        case 'Completed': return 'Đã thanh toán trực tuyến';
        case 'Failed': return 'Thanh toán thất bại';
        case 'Refunded': return 'Đã hoàn tiền';
        default: return 'Chờ thanh toán';
    }
}

function getDeliveryStatusText($status) {
    switch ($status) {
        case 'Preparing': return 'Đang chuẩn bị';
        case 'Shipping': return 'Đang vận chuyển';
        case 'Delivered': return 'Đã giao hàng';
        case 'Failed': return 'Giao hàng thất bại';
        default: return 'Chờ xử lý';
    }
}
?>

<main class="order-container">
    <ul class="breadcrumbs">
        <li><a href="<?= url('/') ?>">Trang chủ</a></li>
        <li><a href="history.php">Lịch sử đơn hàng</a></li>
        <li>Chi tiết đơn hàng #WBS-<?= $orderId ?></li>
    </ul>

    <div class="order-title-section">
        <div>
            <h1 class="order-title">Chi tiết đơn hàng #WBS-<?= $orderId ?></h1>
            <p style="color: var(--color-text-light); margin: var(--spacing-xs) 0 0 0; font-size: var(--font-size-sm);">
                Đặt ngày: <?= date('d/m/Y H:i', strtotime($order['OrderDate'])) ?> | Trạng thái đơn: 
                <span class="badge <?= $order['OrderStatus'] === 'Delivered' ? 'badge--success' : ($order['OrderStatus'] === 'Cancelled' ? 'badge--error' : 'badge--info') ?>" style="font-size: 0.7rem; vertical-align: middle;">
                    <?= getOrderStatusText($order['OrderStatus']) ?>
                </span>
            </p>
        </div>
        <div>
            <a href="<?= url('cart/tracking.php?id=' . $orderId) ?>" class="btn btn--primary" style="text-decoration: none;">
                <i class="fa-solid fa-location-dot" style="margin-right: 6px;"></i>Theo dõi vận chuyển
            </a>
        </div>
    </div>

    <div class="order-detail-layout">
        <!-- Cột trái: Thông tin nhận hàng, Thanh toán, Sản phẩm -->
        <div class="order-detail-main">
            <!-- Thông tin nhận hàng -->
            <div class="detail-section-card">
                <h2 class="detail-section-title"><i class="fa-solid fa-user" style="margin-right: 10px; color: var(--color-primary);"></i>Thông tin nhận hàng</h2>
                <div style="line-height: 1.6; color: var(--color-text); font-size: 0.95rem;">
                    <?php if ($order['CustomerID'] === null): ?>
                        <!-- Định dạng lưu trữ khách vãng lai: Tên | SĐT | Địa chỉ -->
                        <div style="white-space: pre-line;"><?= htmlspecialchars($order['ShippingAddress']) ?></div>
                    <?php else: ?>
                        <!-- Định dạng thành viên đăng nhập -->
                        <div class="info-details-item">
                            <span class="info-details-label" style="display:inline-block; width: 140px;">Người nhận:</span>
                            <span class="info-details-value"><?= htmlspecialchars($_SESSION['user']['full_name'] ?? 'Thành viên') ?></span>
                        </div>
                        <div class="info-details-item" style="margin-top: 6px;">
                            <span class="info-details-label" style="display:inline-block; width: 140px;">Số điện thoại:</span>
                            <span class="info-details-value"><?= htmlspecialchars($order['Phone'] ?? $_SESSION['user']['phone'] ?? 'Chưa rõ') ?></span>
                        </div>
                        <div class="info-details-item" style="margin-top: 6px;">
                            <span class="info-details-label" style="display:inline-block; width: 140px;">Địa chỉ giao hàng:</span>
                            <span class="info-details-value"><?= htmlspecialchars($order['ShippingAddress']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Thanh toán và vận chuyển -->
            <div class="detail-section-card">
                <h2 class="detail-section-title"><i class="fa-solid fa-credit-card" style="margin-right: 10px; color: var(--color-primary);"></i>Thanh toán & Vận chuyển</h2>
                <ul class="info-details-list">
                    <li class="info-details-item">
                        <span class="info-details-label">Phương thức:</span>
                        <span class="info-details-value">
                            <?= $order['PaymentMethod'] === 'VNPAY' ? 'Thanh toán trực tuyến cổng VNPAY' : 'Thanh toán khi nhận hàng (COD)' ?>
                        </span>
                    </li>
                    <li class="info-details-item">
                        <span class="info-details-label">Trạng thái thanh toán:</span>
                        <span class="info-details-value">
                            <span class="badge <?= $order['PaymentStatus'] === 'Completed' ? 'badge--success' : 'badge--warning' ?>">
                                <?= getPaymentStatusText($order['PaymentStatus']) ?>
                            </span>
                        </span>
                    </li>
                    <li class="info-details-item">
                        <span class="info-details-label">Trạng thái giao hàng:</span>
                        <span class="info-details-value">
                            <span class="badge <?= $order['DeliveryStatus'] === 'Delivered' ? 'badge--success' : 'badge--info' ?>">
                                <?= getDeliveryStatusText($order['DeliveryStatus']) ?>
                            </span>
                        </span>
                    </li>
                </ul>
            </div>

            <!-- Sản phẩm đã mua -->
            <div class="detail-section-card">
                <h2 class="detail-section-title"><i class="fa-solid fa-box-open" style="margin-right: 10px; color: var(--color-primary);"></i>Sản phẩm đã mua</h2>
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Đơn giá</th>
                                <th>Số lượng</th>
                                <th style="text-align: right;">Thành tiền</th>
                                <?php if ($order['OrderStatus'] === 'Delivered'): ?>
                                    <th style="text-align: center; width: 120px;">Thao tác</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): 
                                $imgSrc = !empty($item['ImageURL']) ? url('assets' . $item['ImageURL']) : asset('images/default-book.png');
                            ?>
                                <tr>
                                    <td>
                                        <div class="detail-product-link">
                                            <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($item['ProductName']) ?>" style="width: 45px; height: 60px; object-fit: contain; border-radius: var(--border-radius-sm); border: 1px solid var(--color-border); background: var(--color-background); padding: 2px;">
                                            <div>
                                                <div class="detail-product-name"><?= htmlspecialchars($item['ProductName']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= number_format($item['UnitPrice'], 0, ',', '.') ?> đ</td>
                                    <td><?= $item['Quantity'] ?></td>
                                    <td style="text-align: right; font-weight: var(--font-weight-bold); color: var(--color-text);">
                                        <?= number_format($item['Price'], 0, ',', '.') ?> đ
                                    </td>
                                    <?php if ($order['OrderStatus'] === 'Delivered'): ?>
                                        <td style="text-align: center; vertical-align: middle;">
                                            <a href="<?= url('trangchu/detail.php?id=' . $item['ProductID']) ?>#review-form-section" class="btn btn--outline btn--sm" style="display: inline-flex; align-items: center; gap: 4px; padding: 6px 12px; font-size: 0.8rem; font-weight: bold; border-color: var(--color-secondary); color: var(--color-text); text-decoration: none; border-radius: var(--border-radius-sm);">
                                                <i class="fa-solid fa-star" style="color: var(--color-secondary);"></i> Đánh giá
                                            </a>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Cột phải: Tổng tiền thanh toán -->
        <div class="order-detail-sidebar">
            <div class="detail-summary-card">
                <h2 class="detail-summary-title">Tóm tắt thanh toán</h2>

                <?php 
                $subtotal = $order['TotalAmount'] - $order['ShippingFee'];
                ?>
                <div class="detail-summary-row">
                    <span>Tổng tiền hàng</span>
                    <span><?= number_format($subtotal, 0, ',', '.') ?> đ</span>
                </div>

                <div class="detail-summary-row">
                    <span>Phí vận chuyển</span>
                    <span><?= number_format($order['ShippingFee'], 0, ',', '.') ?> đ</span>
                </div>

                <div class="detail-summary-row detail-summary-row--total">
                    <span>Tổng tiền thanh toán</span>
                    <span class="detail-summary-value"><?= number_format($order['TotalAmount'], 0, ',', '.') ?> đ</span>
                </div>

                <div class="detail-summary-actions">
                    <a href="<?= url('cart/tracking.php?id=' . $orderId) ?>" class="btn btn--primary btn--block" style="text-align: center; text-decoration: none; padding: 12px 0; font-weight: bold;">
                        <i class="fa-solid fa-location-dot" style="margin-right: 6px;"></i>Theo dõi đơn hàng
                    </a>
                    <a href="history.php" class="btn btn--ghost btn--block" style="text-align: center; text-decoration: none; padding: 10px 0;">
                        <i class="fa-solid fa-arrow-left" style="margin-right: 6px;"></i>Quay lại lịch sử đơn
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
</body>
</html>