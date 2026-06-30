<?php
require_once '../config/db.php';

$orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$inputPhone = isset($_GET['phone']) ? trim($_GET['phone']) : '';

$order = null;
$isAuthorized = false;
$verifyError = '';

if ($orderId > 0) {
    // Truy vấn thông tin đơn hàng
    $sql = "
        SELECT o.OrderID, o.CustomerID, o.OrderDate, o.ShippingAddress, o.OrderStatus, p.PaymentMethod 
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
    
    if ($order) {
        $currentCustomerId = isset($_SESSION['user']) ? intval($_SESSION['user']['id']) : 0;
        
        // 1. Kiểm tra đối với đơn hàng thành viên
        if ($order['CustomerID'] !== null) {
            if ($order['CustomerID'] === $currentCustomerId) {
                $isAuthorized = true;
            } else {
                $verifyError = 'Bạn không có quyền truy cập thông tin đơn hàng này!';
            }
        } 
        // 2. Kiểm tra đối với đơn hàng khách vãng lai
        else {
            $guestInfo = getGuestInfoFromAddress($order['ShippingAddress']);
            $orderPhone = $guestInfo['phone'];
            
            // Xử lý nếu người dùng cung cấp SĐT qua GET
            if (!empty($inputPhone)) {
                if ($inputPhone === $orderPhone) {
                    $_SESSION['verified_orders'][$orderId] = true;
                    $_SESSION['guest_search_phone'] = $inputPhone;
                    $isAuthorized = true;
                } else {
                    $verifyError = 'Số điện thoại xác minh không chính xác!';
                }
            } else {
                // Kiểm tra các session đã lưu trước đó
                if (isset($_SESSION['verified_orders'][$orderId]) && $_SESSION['verified_orders'][$orderId] === true) {
                    $isAuthorized = true;
                } elseif (isset($_SESSION['guest_search_phone']) && $_SESSION['guest_search_phone'] === $orderPhone) {
                    $isAuthorized = true;
                } elseif (isset($_SESSION['guest_checkout']['phone']) && $_SESSION['guest_checkout']['phone'] === $orderPhone) {
                    $isAuthorized = true;
                }
            }
        }
    } else {
        $verifyError = 'Đơn hàng không tồn tại trên hệ thống!';
    }
}

// Xử lý POST xác minh số điện thoại cho khách vãng lai
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_guest_phone'])) {
    $postPhone = trim($_POST['verify_guest_phone']);
    if ($order && $order['CustomerID'] === null) {
        $guestInfo = getGuestInfoFromAddress($order['ShippingAddress']);
        $orderPhone = $guestInfo['phone'];
        if ($postPhone === $orderPhone) {
            $_SESSION['verified_orders'][$orderId] = true;
            $_SESSION['guest_search_phone'] = $postPhone;
            $isAuthorized = true;
            $verifyError = '';
        } else {
            $verifyError = 'Số điện thoại xác minh không chính xác. Vui lòng thử lại!';
        }
    }
}

$pageTitle = ($order && $isAuthorized) ? 'Theo dõi đơn hàng #WBS-' . $orderId : 'Tra cứu hành trình đơn hàng';
$extraCss = ['css/cart.css'];
include '../includes/header.php';

// Các mức độ của hành trình giao hàng
$statusSteps = ['Pending', 'Processing', 'Shipped', 'Delivered'];
$currentStatus = ($order && $isAuthorized) ? $order['OrderStatus'] : '';
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

    <?php if (!$order || ($order['CustomerID'] !== null && !$isAuthorized)): ?>
        <?php if ($orderId <= 0): ?>
            <!-- Form tra cứu nhanh trạng thái đơn hàng (yêu cầu ID + SĐT) -->
            <div style="background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--border-radius-lg); padding: var(--spacing-xl); text-align: center; box-shadow: var(--box-shadow-sm); max-width: 500px; margin: 40px auto;">
                <i class="fa-solid fa-map-location-dot" style="font-size: 3.5rem; display: block; margin-bottom: var(--spacing-sm); color: var(--color-primary);"></i>
                <h2 style="margin-top: 0; margin-bottom: var(--spacing-sm); color: var(--color-text);">Tra cứu hành trình đơn hàng</h2>
                <p style="color: var(--color-text-light); margin-bottom: var(--spacing-lg); font-size: 0.95rem;">Nhập Mã đơn hàng và Số điện thoại đặt hàng để tra cứu hành trình vận chuyển.</p>
                
                <?php if (!empty($verifyError)): ?>
                    <div class="alert alert--error" style="margin-bottom: var(--spacing-md); padding: 10px; border-radius: var(--border-radius-sm); font-size: 0.9rem; text-align: left; background-color: rgba(239, 68, 68, 0.1); border: 1px solid var(--color-error); color: var(--color-error);">
                        <i class="fa-solid fa-circle-exclamation" style="margin-right: 6px;"></i><?= htmlspecialchars($verifyError) ?>
                    </div>
                <?php endif; ?>

                <form method="GET" action="tracking.php" style="display: flex; flex-direction: column; gap: var(--spacing-md); max-width: 400px; margin: 0 auto; text-align: left;">
                    <div>
                        <label style="display: block; font-weight: bold; margin-bottom: 6px; color: var(--color-text);">Mã đơn hàng <span style="color: var(--color-error);">*</span></label>
                        <input type="number" name="id" class="form-control" placeholder="Ví dụ: 1, 2..." required min="1">
                    </div>
                    <div>
                        <label style="display: block; font-weight: bold; margin-bottom: 6px; color: var(--color-text);">Số điện thoại nhận hàng <?= isset($_SESSION['user']) ? '<span style="font-weight: normal; color: var(--color-text-light);">(Không bắt buộc với thành viên)</span>' : '<span style="color: var(--color-error);">*</span>' ?></label>
                        <input type="text" name="phone" class="form-control" placeholder="Nhập số điện thoại đặt hàng..." <?= isset($_SESSION['user']) ? '' : 'required' ?> value="<?= htmlspecialchars($inputPhone) ?>">
                    </div>
                    <button type="submit" class="btn btn--primary" style="padding: 12px; font-weight: bold; margin-top: var(--spacing-sm);">Tra cứu ngay</button>
                </form>
            </div>
        <?php else: ?>
            <!-- Báo lỗi không tìm thấy hoặc không có quyền truy cập -->
            <div style="background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--border-radius-lg); padding: var(--spacing-xl); text-align: center; box-shadow: var(--box-shadow-sm); max-width: 500px; margin: 40px auto;">
                <i class="fa-solid fa-triangle-exclamation" style="font-size: 3.5rem; display: block; margin-bottom: var(--spacing-sm); color: var(--color-error);"></i>
                <h2 style="margin-top: 0; margin-bottom: var(--spacing-sm); color: var(--color-text);">Không thể truy cập</h2>
                <p style="color: var(--color-text-light); margin-bottom: var(--spacing-lg); font-size: 0.95rem;"><?= !empty($verifyError) ? htmlspecialchars($verifyError) : 'Đơn hàng không tồn tại hoặc bạn không có quyền truy cập.' ?></p>
                <a href="tracking.php" class="btn btn--primary" style="padding: 10px 24px; font-weight: bold; text-decoration: none;">Quay lại tra cứu</a>
            </div>
        <?php endif; ?>
    <?php elseif (!$isAuthorized): ?>
        <!-- Form xác minh số điện thoại cho khách vãng lai -->
        <div style="background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--border-radius-lg); padding: var(--spacing-xl); text-align: center; box-shadow: var(--box-shadow-sm); max-width: 500px; margin: 40px auto;">
            <i class="fa-solid fa-user-shield" style="font-size: 3.5rem; display: block; margin-bottom: var(--spacing-sm); color: var(--color-primary);"></i>
            <h2 style="margin-top: 0; margin-bottom: var(--spacing-sm); color: var(--color-text);">Xác minh đơn hàng</h2>
            <p style="color: var(--color-text-light); margin-bottom: var(--spacing-lg); font-size: 0.95rem;">Đơn hàng này thuộc về <strong>Khách vãng lai</strong>. Vui lòng cung cấp số điện thoại mua hàng để tiếp tục theo dõi vận chuyển.</p>
            
            <?php if (!empty($verifyError)): ?>
                <div class="alert alert--error" style="margin-bottom: var(--spacing-md); padding: 10px; border-radius: var(--border-radius-sm); font-size: 0.9rem; text-align: left; background-color: rgba(239, 68, 68, 0.1); border: 1px solid var(--color-error); color: var(--color-error);">
                    <i class="fa-solid fa-circle-exclamation" style="margin-right: 6px;"></i><?= htmlspecialchars($verifyError) ?>
                </div>
            <?php endif; ?>

            <form method="POST" style="display: flex; flex-direction: column; gap: var(--spacing-md); max-width: 350px; margin: 0 auto;">
                <input type="text" name="verify_guest_phone" class="form-control" placeholder="Nhập số điện thoại mua hàng..." required style="text-align: center;">
                <button type="submit" class="btn btn--primary" style="padding: 12px; font-weight: bold;">Xác minh & Theo dõi</button>
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