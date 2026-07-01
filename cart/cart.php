<?php
require_once '../config/db.php';

$pageTitle = 'Giỏ hàng của tôi';
$extraCss = ['css/cart.css'];
include '../includes/header.php';

// Khởi tạo biến giỏ hàng và danh sách sản phẩm
$cart = $_SESSION['cart'] ?? [];
$cartProducts = [];
$totalAmount = 0;
$shippingFee = 0; // Phí giao hàng mặc định 30.000đ

if (!empty($cart)) {
    $productIds = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    
    $sql = "
        SELECT p.ProductID, p.ProductName, p.Price AS OriginalPrice, p.Quantity AS Stock, p.Status, i.ImageURL,
               ap.DiscountRate
        FROM product p
        LEFT JOIN image i ON p.ProductID = i.ProductID AND i.IsThumbnail = 1
        LEFT JOIN (
            SELECT pd.ProductID, MAX(pd.DiscountRate) AS DiscountRate
            FROM promotion_detail pd
            JOIN promotion pr ON pd.PromotionID = pr.PromotionID
            WHERE NOW() BETWEEN COALESCE(pd.StartDate, pr.StartDate) AND COALESCE(pd.EndDate, pr.EndDate)
            GROUP BY pd.ProductID
        ) ap ON p.ProductID = ap.ProductID
        WHERE p.ProductID IN ($placeholders)
    ";
    
    $stmt = $conn->prepare($sql);
    $types = str_repeat('i', count($productIds));
    $stmt->bind_param($types, ...$productIds);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        // Đồng bộ lại số lượng giỏ hàng nếu vượt quá tồn kho thực tế
        $qty = $cart[$row['ProductID']];
        if ($qty > $row['Stock']) {
            $qty = $row['Stock'];
            $_SESSION['cart'][$row['ProductID']] = $qty;
        }
        
        // Tính toán giá khuyến mãi
        $discountRate = isset($row['DiscountRate']) ? floatval($row['DiscountRate']) : 0;
        $row['Price'] = $row['OriginalPrice'] - ($row['OriginalPrice'] * $discountRate / 100);
        
        $row['CartQuantity'] = $qty;
        $row['Subtotal'] = $row['Price'] * $qty;
        $totalAmount += $row['Subtotal'];
        $cartProducts[] = $row;
    }
    $stmt->close();
}
?>

<style>
    /* CSS custom bổ sung cho giao diện giỏ hàng */
    .cart-grid-layout {
        display: grid;
        grid-template-columns: 2.2fr 1fr;
        gap: var(--spacing-lg);
        margin-top: var(--spacing-md);
    }
    @media (max-width: 992px) {
        .cart-grid-layout {
            grid-template-columns: 1fr;
        }
    }
    .cart-item-card {
        display: flex;
        align-items: center;
        gap: var(--spacing-md);
        padding: var(--spacing-md);
        border: var(--border-width) solid var(--color-border);
        border-radius: var(--border-radius);
        background-color: var(--color-surface);
        margin-bottom: var(--spacing-md);
        box-shadow: var(--box-shadow-sm);
    }
    .cart-item-img {
        width: 70px;
        height: 95px;
        object-fit: contain;
        background: var(--color-background);
        border-radius: var(--border-radius-sm);
        padding: 4px;
        border: 1px solid var(--color-border);
        flex-shrink: 0;
    }
    .cart-item-info {
        flex: 1;
        min-width: 0;
    }
    .cart-item-title {
        font-size: var(--font-size-md);
        font-weight: var(--font-weight-bold);
        color: var(--color-text);
        margin: 0 0 6px 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .cart-item-price {
        color: var(--color-primary);
        font-weight: var(--font-weight-bold);
        font-size: 1.05rem;
    }
    .cart-qty-wrapper {
        display: flex;
        align-items: center;
        border: var(--border-width) solid var(--color-border);
        border-radius: var(--border-radius-sm);
        overflow: hidden;
        width: 110px;
        height: 34px;
    }
    .cart-qty-btn {
        background: var(--color-background);
        border: none;
        width: 32px;
        height: 100%;
        cursor: pointer;
        font-weight: bold;
        font-size: 1rem;
    }
    .cart-qty-btn:hover {
        background: var(--color-border);
    }
    .cart-qty-input {
        flex: 1;
        border: none;
        text-align: center;
        width: 100%;
        font-weight: bold;
        font-size: 0.95rem;
    }
    .cart-subtotal-info {
        text-align: right;
        min-width: 120px;
    }
    .cart-subtotal-val {
        font-weight: var(--font-weight-bold);
        font-size: 1.1rem;
        color: var(--color-text);
    }
    .delete-cart-btn {
        background: transparent;
        border: none;
        color: var(--color-error);
        cursor: pointer;
        font-size: 1.2rem;
        padding: var(--spacing-xs);
        transition: opacity var(--transition-fast);
    }
    .delete-cart-btn:hover {
        opacity: 0.8;
    }
    .empty-cart-box {
        text-align: center;
        padding: 80px var(--spacing-md);
        background: var(--color-surface);
        border: 1px solid var(--color-border);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--box-shadow-sm);
    }
</style>

<main class="order-container">
    <ul class="breadcrumbs">
        <li><a href="<?= url('/') ?>">Trang chủ</a></li>
        <li>Giỏ hàng</li>
    </ul>

    <div class="order-title-section">
        <h1 class="order-title"><i class="fa-solid fa-cart-shopping" style="color: var(--color-primary); margin-right: 10px;"></i> Giỏ hàng của tôi</h1>
        <span style="color: var(--color-text-light);"><?= count($cartProducts) ?> sản phẩm</span>
    </div>

    <!-- Thông báo Alerts -->
    <?php
    $successMsg = $_SESSION['success'] ?? '';
    $warningMsg = $_SESSION['warning'] ?? '';
    $errorMsg = $_SESSION['error'] ?? '';
    unset($_SESSION['success'], $_SESSION['warning'], $_SESSION['error']);
    ?>

    <?php if ($successMsg): ?>
        <div style="background-color: #d4edda; color: #155724; padding: 12px; border-radius: var(--border-radius); border: 1px solid #c3e6cb; margin-bottom: var(--spacing-md); font-weight: bold;">
            <?= htmlspecialchars($successMsg) ?>
        </div>
    <?php endif; ?>
    <?php if ($warningMsg): ?>
        <div style="background-color: #fff3cd; color: #856404; padding: 12px; border-radius: var(--border-radius); border: 1px solid #ffeeba; margin-bottom: var(--spacing-md); font-weight: bold;">
            <?= htmlspecialchars($warningMsg) ?>
        </div>
    <?php endif; ?>
    <?php if ($errorMsg): ?>
        <div style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: var(--border-radius); border: 1px solid #f5c6cb; margin-bottom: var(--spacing-md); font-weight: bold;">
            <?= htmlspecialchars($errorMsg) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($cartProducts)): ?>
        <div class="empty-cart-box">
            <i class="fa-solid fa-cart-flatbed-suitcase" style="font-size: 4rem; display: block; margin-bottom: var(--spacing-sm); color: var(--color-text-light);"></i>
            <h3 style="margin-bottom: var(--spacing-xs);">Giỏ hàng của bạn đang trống!</h3>
            <p style="color: var(--color-text-light); margin-bottom: var(--spacing-lg);">Hãy quay lại cửa hàng để chọn cho mình những cuốn sách ưng ý nhất.</p>
            <a href="<?= url('trangchu/index.php') ?>" class="btn btn--primary" style="padding: 12px 32px; font-weight: bold;">Tiếp tục mua sắm</a>
        </div>
    <?php else: ?>
        <div class="cart-grid-layout">
            <!-- Cột trái: Danh sách sản phẩm -->
            <div>
                <?php foreach ($cartProducts as $item): ?>
                    <div class="cart-item-card">
                        <?php 
                        // ĐOẠN XỬ LÝ ẢNH ĐÃ ĐƯỢC CẬP NHẬT
                        if (!empty($item['ImageURL'])) {
                            if (strpos($item['ImageURL'], 'http') === 0) {
                                $imgSrc = $item['ImageURL'];
                            } else {
                                $fileName = basename($item['ImageURL']);
                                $imgSrc = asset('images/uploads/' . $fileName);
                            }
                        } else {
                            $imgSrc = asset('images/default-book.png'); 
                        }
                        ?>
                        <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($item['ProductName']) ?>" class="cart-item-img">
                        
                        <div class="cart-item-info">
                            <h3 class="cart-item-title" title="<?= htmlspecialchars($item['ProductName']) ?>">
                                <a href="<?= url('trangchu/detail.php?id=' . $item['ProductID']) ?>" style="text-decoration: none; color: inherit;">
                                    <?= htmlspecialchars($item['ProductName']) ?>
                                </a>
                            </h3>
                            <?php 
                            $itemDiscountRate = isset($item['DiscountRate']) ? floatval($item['DiscountRate']) : 0;
                            ?>
                            <?php if ($itemDiscountRate > 0): ?>
                                <div class="cart-item-price" style="display: flex; align-items: baseline; gap: 6px;">
                                    <span><?= number_format($item['Price'], 0, ',', '.') ?> đ</span>
                                    <span style="text-decoration: line-through; color: var(--color-text-light); font-size: 0.8rem; font-weight: normal;"><?= number_format($item['OriginalPrice'], 0, ',', '.') ?> đ</span>
                                </div>
                            <?php else: ?>
                                <div class="cart-item-price"><?= number_format($item['Price'], 0, ',', '.') ?> đ</div>
                            <?php endif; ?>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 0.8rem; color: var(--color-text-light);">Kho: <?= $item['Stock'] ?></span>
                                <?php if ($itemDiscountRate > 0): ?>
                                    <span class="badge" style="background-color: var(--color-primary); color: white; font-size: 0.7rem; padding: 2px 6px; border-radius: var(--border-radius-sm); font-weight: bold;">-<?= number_format($itemDiscountRate, 0) ?>%</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Cập nhật số lượng -->
                        <form action="update.php" method="POST" class="cart-qty-wrapper" id="qty-form-<?= $item['ProductID'] ?>">
                            <input type="hidden" name="product_id" value="<?= $item['ProductID'] ?>">
                            <input type="hidden" name="action" value="update">
                            
                            <button type="button" class="cart-qty-btn" onclick="updateQty(<?= $item['ProductID'] ?>, -1)">-</button>
                            <input type="number" name="quantity" id="qty-input-<?= $item['ProductID'] ?>" class="cart-qty-input" 
                                   value="<?= $item['CartQuantity'] ?>" min="1" max="<?= $item['Stock'] ?>" 
                                   onchange="this.form.submit()">
                            <button type="button" class="cart-qty-btn" onclick="updateQty(<?= $item['ProductID'] ?>, 1, <?= $item['Stock'] ?>)">+</button>
                        </form>

                        <div class="cart-subtotal-info">
                            <div class="cart-subtotal-val"><?= number_format($item['Subtotal'], 0, ',', '.') ?> đ</div>
                        </div>

                        <!-- Nút xóa -->
                        <form action="update.php" method="POST" style="margin: 0;">
                            <input type="hidden" name="product_id" value="<?= $item['ProductID'] ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="delete-cart-btn" title="Xóa khỏi giỏ" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                <i class="fa-regular fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Cột phải: Summary -->
            <div>
                <div class="detail-summary-card">
                    <h2 class="detail-summary-title">Tóm tắt đơn hàng</h2>
                    
                    <div class="detail-summary-row">
                        <span>Tạm tính</span>
                        <span><?= number_format($totalAmount, 0, ',', '.') ?> đ</span>
                    </div>
                    
                    <div class="detail-summary-row">
                        <span>Phí vận chuyển</span>
                        <span><?= number_format($shippingFee, 0, ',', '.') ?> đ</span>
                    </div>

                    <?php 
                    $voucherDiscount = 0;
                    $appliedVoucher = $_SESSION['applied_voucher'] ?? null;
                    if ($appliedVoucher && isset($_SESSION['user'])) {
                        $voucherDiscount = floatval($appliedVoucher['value']);
                    } else {
                        // Nếu chưa đăng nhập hoặc đăng xuất, gỡ voucher khỏi session
                        unset($_SESSION['applied_voucher']);
                        $appliedVoucher = null;
                    }
                    
                    $finalTotal = max(0, $totalAmount + $shippingFee - $voucherDiscount);
                    ?>

                    <?php if ($appliedVoucher): ?>
                        <div class="detail-summary-row" style="color: #2e7d32; font-weight: bold;">
                            <span style="display: flex; align-items: center; gap: 4px;">
                                <i class="fa-solid fa-ticket"></i> Voucher (<?= htmlspecialchars($appliedVoucher['code']) ?>)
                            </span>
                            <span style="display: flex; align-items: center; gap: 8px;">
                                -<?= number_format($voucherDiscount, 0, ',', '.') ?> đ
                                <form action="<?= url('cart/apply_voucher.php') ?>" method="POST" style="margin: 0; display: inline;">
                                    <input type="hidden" name="action" value="remove">
                                    <button type="submit" style="background: none; border: none; color: var(--color-error); cursor: pointer; padding: 0; font-size: 0.95rem;" title="Gỡ mã">
                                        <i class="fa-solid fa-circle-xmark"></i>
                                    </button>
                                </form>
                            </span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="detail-summary-row detail-summary-row--total">
                        <span>Tổng tiền</span>
                        <span class="detail-summary-value"><?= number_format($finalTotal, 0, ',', '.') ?> đ</span>
                    </div>

                    <!-- Khu vực nhập Voucher -->
                    <div style="border-top: 1px dashed var(--color-border); margin-top: var(--spacing-md); padding-top: var(--spacing-md);">
                        <?php if (isset($_SESSION['user'])): ?>
                            <?php if (!$appliedVoucher): ?>
                                <form action="<?= url('cart/apply_voucher.php') ?>" method="POST" style="display: flex; gap: var(--spacing-xs); margin-bottom: 0;">
                                    <input type="hidden" name="action" value="apply">
                                    <input type="text" name="voucher_code" class="form-control" placeholder="Nhập mã giảm giá..." style="padding: 8px 12px; font-size: 0.9rem; margin-bottom: 0;" required>
                                    <button type="submit" class="btn btn--primary" style="padding: 0 16px; font-size: 0.9rem; font-weight: bold; white-space: nowrap; height: 38px;">Áp dụng</button>
                                </form>
                            <?php else: ?>
                                <div style="font-size: 0.85rem; color: #2e7d32; display: flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-circle-check"></i> Đã áp dụng mã giảm giá thành công!
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div style="font-size: 0.85rem; color: var(--color-text-light); text-align: center; background: var(--color-background); padding: var(--spacing-sm); border-radius: var(--border-radius-sm); border: 1px dashed var(--color-border);">
                                <i class="fa-solid fa-lock" style="margin-right: 4px;"></i> <a href="<?= url('auth/pages/login.php') ?>" style="color: var(--color-primary); font-weight: bold; text-decoration: none;">Đăng nhập</a> để sử dụng mã giảm giá.
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="detail-summary-actions" style="margin-top: var(--spacing-md);">
                        <a href="checkout.php" class="btn btn--primary btn--block" style="text-align: center; text-decoration: none; padding: 12px 0; font-weight: bold;">
                            Tiến hành thanh toán
                        </a>
                        <a href="<?= url('trangchu/index.php') ?>" class="btn btn--outline btn--block" style="text-align: center; text-decoration: none; padding: 10px 0;">
                            Tiếp tục mua hàng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>

<script>
    function updateQty(productId, delta, maxStock = 99) {
        const input = document.getElementById('qty-input-' + productId);
        let val = parseInt(input.value) || 1;
        val += delta;
        if (val < 1) val = 1;
        if (val > maxStock) val = maxStock;
        input.value = val;
        
        // Tự động submit form
        document.getElementById('qty-form-' + productId).submit();
    }
</script>
</body>
</html>