<?php
require_once '../config/db.php';

$orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;

$order = null;
if ($orderId > 0) {
    // Truy vấn thông tin đơn hàng
    $sql = "
        SELECT o.OrderID, o.OrderDate, o.ShippingAddress, o.OrderStatus, p.PaymentMethod 
        FROM `order` o
        LEFT JOIN `payment` p ON o.OrderID = p.OrderID
        WHERE o.OrderID = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $order = $res->fetch_assoc();
    }
    $stmt->close();
}

$pageTitle = $order ? 'Theo dõi đơn hàng #WBS-' . $orderId : 'Tra cứu hành trình đơn hàng';
$extraCss = ['css/cart.css'];
include '../includes/header.php';

// Các mức độ của hành trình giao hàng
$statusSteps = ['Pending', 'Processing', 'Shipped', 'Delivered'];
$currentStatus = $order['OrderStatus'] ?? '';
$currentIndex = array_search($currentStatus, $statusSteps);
if ($currentIndex === false && $currentStatus === 'Cancelled') {
    $currentIndex = -1; // Đơn hàng đã hủy
}
?>

<main class="order-container">
    <ul class="breadcrumbs">
        <li><a href="<?= url('/') ?>">Trang chủ</a></li>
        <li><a href="history.php">Lịch sử đơn hàng</a></li>
        <?php if ($order): ?>
            <li><a href="detail.php?id=<?= $orderId ?>">Chi tiết đơn #WBS-<?= $orderId ?></a></li>
            <li>Theo dõi đơn hàng</li>
        <?php else: ?>
            <li>Tra cứu hành trình đơn hàng</li>
        <?php endif; ?>
    </ul>

    <div class="order-title-section">
        <h1 class="order-title">Theo dõi hành trình đơn hàng</h1>
    </div>

    <?php if (!$order): ?>
        <!-- Form tra cứu trực tiếp từ trang tracking nếu chưa chọn đơn hàng -->
        <div style="background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--border-radius-lg); padding: var(--spacing-xl); text-align: center; box-shadow: var(--box-shadow-sm); max-width: 600px; margin: 40px auto;">
            <i class="fa-solid fa-location-dot" style="font-size: 3.5rem; display: block; margin-bottom: var(--spacing-sm); color: var(--color-primary);"></i>
            <h2 style="margin-top: 0; margin-bottom: var(--spacing-sm); color: var(--color-text);">Tra cứu nhanh trạng thái đơn hàng</h2>
            <p style="color: var(--color-text-light); margin-bottom: var(--spacing-lg); font-size: 0.95rem;">Nhập mã số đơn hàng của bạn (ví dụ: nhập số 1 cho mã đơn hàng #WBS-1) để kiểm tra hành trình vận chuyển.</p>
            <form method="GET" action="tracking.php" style="display: flex; gap: var(--spacing-xs); max-width: 400px; margin: 0 auto;">
                <input type="number" name="id" class="form-control" placeholder="Nhập mã số đơn hàng (ví dụ: 1)..." required min="1">
                <button type="submit" class="btn btn--primary" style="padding: 10px 24px; font-weight: bold; white-space: nowrap;">Tra cứu</button>
            </form>
        </div>
    <?php else: ?>
        <!-- Hiển thị thông tin hành trình đơn hàng cụ thể -->
        <div class="tracking-info-header">
            <div class="tracking-info-grid">
                <div>
                    <div class="tracking-info-item__label">Mã đơn hàng</div>
                    <div class="tracking-info-item__value" style="color: var(--color-primary); font-family: monospace;">
                        #WBS-<?= $order['OrderID'] ?>
                    </div>
                </div>
                <div>
                    <div class="tracking-info-item__label">Phương thức thanh toán</div>
                    <div class="tracking-info-item__value">
                        <?= $order['PaymentMethod'] === 'VNPAY' ? 'Thanh toán trực tuyến VNPAY' : 'Thanh toán COD khi nhận hàng' ?>
                    </div>
                </div>
                <div>
                    <div class="tracking-info-item__label">Ngày đặt hàng</div>
                    <div class="tracking-info-item__value">
                        <?= date('d/m/Y H:i', strtotime($order['OrderDate'])) ?>
                    </div>
                </div>
            </div>
            <div>
                <?php if ($currentStatus === 'Cancelled'): ?>
                    <span class="badge badge--error" style="font-size: var(--font-size-sm);">Đơn hàng đã hủy</span>
                <?php else: ?>
                    <span class="badge badge--info badge--dot" style="font-size: var(--font-size-sm);">Đang thực hiện</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="timeline-card">
            <h2 style="font-size: var(--font-size-lg); margin-top: 0; margin-bottom: var(--spacing-lg); font-weight: var(--font-weight-bold); border-bottom: 2px solid var(--color-background); padding-bottom: var(--spacing-sm);">
                <i class="fa-solid fa-map-location-dot" style="margin-right: 8px; color: var(--color-primary);"></i>Trạng thái vận chuyển chi tiết
            </h2>

            <div class="timeline">
                <?php if ($currentStatus === 'Cancelled'): ?>
                    <!-- Trạng thái hủy đơn hàng -->
                    <div class="timeline-item is-active">
                        <div class="timeline-badge" style="background-color: var(--color-error); border-color: var(--color-error); color: white;"><i class="fa-solid fa-xmark"></i></div>
                        <div class="timeline-content">
                            <div class="timeline-header">
                                <h3 class="timeline-title" style="color: var(--color-error);">Đã hủy đơn hàng</h3>
                                <span class="timeline-time">Hành trình kết thúc</span>
                            </div>
                            <p class="timeline-desc">Đơn hàng này đã bị hủy bỏ trên hệ thống. Số lượng tồn kho sản phẩm đã được tự động hoàn lại.</p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Bước 4: Giao hàng thành công -->
                <?php 
                $isCompleted = ($currentIndex >= 3);
                $isActive = ($currentStatus === 'Delivered');
                ?>
                <div class="timeline-item <?= $isCompleted ? 'is-completed' : '' ?> <?= $isActive ? 'is-active' : '' ?>">
                    <div class="timeline-badge"><i class="fa-solid fa-check"></i></div>
                    <div class="timeline-content">
                        <div class="timeline-header">
                            <h3 class="timeline-title">Giao hàng thành công</h3>
                            <span class="timeline-time"><?= $isActive ? 'Hoàn tất' : 'Dự kiến' ?></span>
                        </div>
                        <p class="timeline-desc">Đơn hàng được bàn giao thành công và có chữ ký xác nhận của khách hàng.</p>
                    </div>
                </div>

                <!-- Bước 3: Đang vận chuyển -->
                <?php 
                $isCompleted = ($currentIndex >= 2);
                $isActive = ($currentStatus === 'Shipped');
                ?>
                <div class="timeline-item <?= $isCompleted ? 'is-completed' : '' ?> <?= $isActive ? 'is-active' : '' ?>">
                    <div class="timeline-badge"><i class="fa-solid fa-truck-fast"></i></div>
                    <div class="timeline-content">
                        <div class="timeline-header">
                            <h3 class="timeline-title">Đơn hàng đang trung chuyển</h3>
                            <span class="timeline-time"><?= $isActive ? 'Đang giao hàng' : '' ?></span>
                        </div>
                        <p class="timeline-desc">Hàng đã được bàn giao cho đơn vị vận chuyển GHN Express và đang trên đường trung chuyển tới địa chỉ nhận của bạn.</p>
                    </div>
                </div>

                <!-- Bước 2: Đang đóng gói -->
                <?php 
                $isCompleted = ($currentIndex >= 1);
                $isActive = ($currentStatus === 'Processing');
                ?>
                <div class="timeline-item <?= $isCompleted ? 'is-completed' : '' ?> <?= $isActive ? 'is-active' : '' ?>">
                    <div class="timeline-badge"><i class="fa-solid fa-box"></i></div>
                    <div class="timeline-content">
                        <div class="timeline-header">
                            <h3 class="timeline-title">Nhà sách đang đóng gói hàng</h3>
                            <span class="timeline-time"><?= $isActive ? 'Đang chuẩn bị' : '' ?></span>
                        </div>
                        <p class="timeline-desc">Nhân viên kho đang tiến hành lấy sách, đóng gói chống sốc bảo vệ sản phẩm và dán mã vận đơn giao nhận.</p>
                    </div>
                </div>

                <!-- Bước 1: Đặt hàng thành công -->
                <?php 
                $isCompleted = ($currentIndex >= 0);
                $isActive = ($currentStatus === 'Pending');
                ?>
                <div class="timeline-item <?= $isCompleted ? 'is-completed' : '' ?> <?= $isActive ? 'is-active' : '' ?>">
                    <div class="timeline-badge"><i class="fa-solid fa-check"></i></div>
                    <div class="timeline-content">
                        <div class="timeline-header">
                            <h3 class="timeline-title">Đặt hàng thành công</h3>
                            <span class="timeline-time"><?= date('d/m/Y H:i', strtotime($order['OrderDate'])) ?></span>
                        </div>
                        <p class="timeline-desc">Đơn hàng được ghi nhận thành công vào hệ thống. Chờ bộ phận quản trị nhà sách duyệt đơn xác nhận đóng gói.</p>
                    </div>
                </div>
            </div>

            <!-- Điều hướng -->
            <div style="display: flex; gap: var(--spacing-md); justify-content: center; margin-top: var(--spacing-xl); border-top: 1px solid var(--color-border); padding-top: var(--spacing-lg);">
                <a href="<?= url('cart/detail.php?id=' . $orderId) ?>" class="btn btn--outline">
                    <i class="fa-solid fa-file-invoice" style="margin-right: 6px;"></i>Xem chi tiết đơn hàng
                </a>
                <a href="history.php" class="btn btn--ghost">
                    <i class="fa-solid fa-arrow-left" style="margin-right: 6px;"></i>Quay lại danh sách đơn hàng
                </a>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>
</body>
</html>