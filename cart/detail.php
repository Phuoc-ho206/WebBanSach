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
           p.PaymentMethod, p.PaymentStatus, d.DeliveryStatus, d.ShippingFee,
           u.FirstName, u.LastName, u.Phone
    FROM `order` o
    LEFT JOIN `payment` p ON o.OrderID = p.OrderID
    LEFT JOIN `delivery` d ON o.OrderID = d.OrderID
    LEFT JOIN `user` u ON o.CustomerID = u.CustomerID
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
$isAuthorized = false;
$orderPhone = '';

if ($order['CustomerID'] !== null) {
    // Đơn hàng của thành viên đăng nhập
    if ($order['CustomerID'] === $currentCustomerId) {
        $isAuthorized = true;
    }
} else {
    // Đơn hàng của khách vãng lai
    $guestInfo = getGuestInfoFromAddress($order['ShippingAddress']);
    $orderPhone = $guestInfo['phone'];
    
    if (isset($_SESSION['verified_orders'][$orderId]) && $_SESSION['verified_orders'][$orderId] === true) {
        $isAuthorized = true;
    } elseif (isset($_SESSION['guest_search_phone']) && $_SESSION['guest_search_phone'] === $orderPhone) {
        $isAuthorized = true;
    } elseif (isset($_SESSION['guest_checkout']['phone']) && $_SESSION['guest_checkout']['phone'] === $orderPhone) {
        $isAuthorized = true;
    }
}

// Xử lý POST xác minh số điện thoại cho khách vãng lai
$verifyPhoneError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_guest_phone'])) {
    $inputPhone = trim($_POST['verify_guest_phone']);
    if ($order['CustomerID'] === null && !empty($orderPhone) && $inputPhone === $orderPhone) {
        $_SESSION['verified_orders'][$orderId] = true;
        $_SESSION['guest_search_phone'] = $inputPhone;
        $isAuthorized = true;
    } else {
        $verifyPhoneError = 'Số điện thoại xác minh không chính xác. Vui lòng thử lại!';
    }
}

if ($order['CustomerID'] !== null && !$isAuthorized) {
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

    <?php if (!$isAuthorized): ?>
        <!-- Form xác minh số điện thoại cho khách vãng lai -->
        <div style="background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--border-radius-lg); padding: var(--spacing-xl); text-align: center; box-shadow: var(--box-shadow-sm); max-width: 500px; margin: 40px auto;">
            <i class="fa-solid fa-user-shield" style="font-size: 3.5rem; display: block; margin-bottom: var(--spacing-sm); color: var(--color-primary);"></i>
            <h2 style="margin-top: 0; margin-bottom: var(--spacing-sm); color: var(--color-text);">Xác minh đơn hàng</h2>
            <p style="color: var(--color-text-light); margin-bottom: var(--spacing-lg); font-size: 0.95rem;">Đơn hàng này thuộc về <strong>Khách vãng lai</strong>. Vui lòng cung cấp số điện thoại mua hàng để tiếp tục xem chi tiết.</p>
            
            <?php if (!empty($verifyPhoneError)): ?>
                <div class="alert alert--error" style="margin-bottom: var(--spacing-md); padding: 10px; border-radius: var(--border-radius-sm); font-size: 0.9rem; text-align: left; background-color: rgba(239, 68, 68, 0.1); border: 1px solid var(--color-error); color: var(--color-error);">
                    <i class="fa-solid fa-circle-exclamation" style="margin-right: 6px;"></i><?= htmlspecialchars($verifyPhoneError) ?>
                </div>
            <?php endif; ?>

            <form method="POST" style="display: flex; flex-direction: column; gap: var(--spacing-md); max-width: 350px; margin: 0 auto;">
                <input type="text" name="verify_guest_phone" class="form-control" placeholder="Nhập số điện thoại mua hàng..." required style="text-align: center;">
                <button type="submit" class="btn btn--primary" style="padding: 12px; font-weight: bold;">Xác minh & Xem chi tiết</button>
            </form>
        </div>
    <?php else: ?>
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
                    <?php if ($order['CustomerID'] === null): 
                        $guestInfo = getGuestInfoFromAddress($order['ShippingAddress']);
                    ?>
                        <div class="info-details-item">
                            <span class="info-details-label" style="display:inline-block; width: 140px;">Người nhận:</span>
                            <span class="info-details-value"><?= htmlspecialchars($guestInfo['fullname']) ?></span>
                        </div>
                        <div class="info-details-item" style="margin-top: 6px;">
                            <span class="info-details-label" style="display:inline-block; width: 140px;">Số điện thoại:</span>
                            <span class="info-details-value"><?= htmlspecialchars($guestInfo['phone']) ?></span>
                        </div>
                        <div class="info-details-item" style="margin-top: 6px;">
                            <span class="info-details-label" style="display:inline-block; width: 140px;">Địa chỉ giao hàng:</span>
                            <span class="info-details-value"><?= htmlspecialchars($guestInfo['address']) ?></span>
                        </div>
                    <?php else: 
                        $memberFullName = trim(($order['FirstName'] ?? '') . ' ' . ($order['LastName'] ?? ''));
                        if (empty($memberFullName)) {
                            $memberFullName = $_SESSION['user']['full_name'] ?? 'Thành viên';
                        }
                        $memberPhone = !empty($order['Phone']) ? $order['Phone'] : ($_SESSION['user']['phone'] ?? 'Chưa rõ');
                    ?>
                        <!-- Định dạng thành viên đăng nhập -->
                        <div class="info-details-item">
                            <span class="info-details-label" style="display:inline-block; width: 140px;">Người nhận:</span>
                            <span class="info-details-value"><?= htmlspecialchars($memberFullName) ?></span>
                        </div>
                        <div class="info-details-item" style="margin-top: 6px;">
                            <span class="info-details-label" style="display:inline-block; width: 140px;">Số điện thoại:</span>
                            <span class="info-details-value"><?= htmlspecialchars($memberPhone) ?></span>
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
                                $imgSrc = getProductImage($item['ImageURL'] ?? '');
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
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>
</body>
</html>