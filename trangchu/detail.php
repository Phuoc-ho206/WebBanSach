<?php
require_once '../config/db.php';

$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($productId <= 0) {
    header('Location: index.php');
    exit;
}

$sql_product = "
    SELECT p.ProductID, p.ProductName, p.Price, p.Description, p.Status, c.CategoryName, i.ImageURL 
    FROM product p
    LEFT JOIN category c ON p.CategoryID = c.CategoryID
    LEFT JOIN image i ON p.ProductID = i.ProductID AND i.IsThumbnail = 1
    WHERE p.ProductID = ?
";

$stmt = $conn->prepare($sql_product);
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("<div style='text-align:center; padding:100px 20px;'><h3>Sản phẩm không hiện diện trên hệ thống!</h3><a href='index.php' class='btn btn--primary'>Quay lại trang chủ</a></div>");
}

$product = $result->fetch_assoc();
$stmt->close();

$pageTitle = $product['ProductName'] . ' - Chi tiết sách';
$extraCss = ['css/components/button.css', 'css/components/badge.css', 'css/components/form.css', 'css/components/card.css'];
include '../includes/header.php';
?>

<style>
    .detail-container { max-width: 1200px; margin: 0 auto var(--spacing-xl); padding: 0 var(--spacing-md); }
    .breadcrumb { display: flex; align-items: center; gap: var(--spacing-xs); padding: var(--spacing-md) 0; font-size: 0.95rem; }
    .breadcrumb a { color: var(--color-text); text-decoration: none; }
    .breadcrumb .separator { color: var(--color-text-light); }
    .breadcrumb .current { color: var(--color-primary); font-weight: var(--font-weight-medium); }
    
    .product-layout { display: grid; grid-template-columns: 1fr 1.4fr; gap: var(--spacing-xl); background-color: var(--color-surface); border: var(--border-width) solid var(--color-border); border-radius: var(--border-radius-lg); padding: var(--spacing-xl); box-shadow: var(--box-shadow-sm); }
    @media (max-width: 768px) { .product-layout { grid-template-columns: 1fr; gap: var(--spacing-lg); padding: var(--spacing-md); } }
    
    .product-image-wrapper { text-align: center; border: var(--border-width) solid var(--color-border); border-radius: var(--border-radius); padding: var(--spacing-md); background-color: var(--color-background); display: flex; align-items: center; justify-content: center; min-height: 350px; }
    .product-main-image { max-width: 100%; max-height: 400px; object-fit: contain; border-radius: var(--border-radius-sm); }
    
    .product-info-wrapper { display: flex; flex-direction: column; gap: var(--spacing-md); }
    .product-detail-title { font-size: 1.75rem; font-weight: var(--font-weight-bold); color: var(--color-text); margin: 0; line-height: 1.3; }
    .product-meta-row { display: flex; gap: var(--spacing-md); align-items: center; font-size: var(--font-size-sm); color: var(--color-text-light); border-bottom: 1px solid var(--color-border); padding-bottom: var(--spacing-sm); }
    .product-detail-price { font-size: 2.2rem; font-weight: var(--font-weight-bold); color: var(--color-primary); margin: var(--spacing-xs) 0; }
    
    .product-description-box { line-height: var(--line-height-base); color: var(--color-text); font-size: var(--font-size-md); border-top: 1px dashed var(--color-border); border-bottom: 1px dashed var(--color-border); padding: var(--spacing-md) 0; }
    .purchase-action-box { display: flex; align-items: flex-end; gap: var(--spacing-md); margin-top: var(--spacing-sm); }
    .quantity-select-group { width: 130px; }
    
    .quantity-input-wrapper { display: flex; align-items: center; border: var(--border-width) solid var(--color-border); border-radius: var(--border-radius); overflow: hidden; background: var(--color-surface); }
    .quantity-btn { background: var(--color-background); border: none; width: 38px; height: 40px; cursor: pointer; font-weight: bold; font-size: 1.1rem; }
    .quantity-btn:hover { background: var(--color-border); }
    .quantity-input { flex: 1; border: none; text-align: center; height: 40px; width: 100%; font-size: var(--font-size-md); font-weight: bold; }
    .quantity-input::-webkit-outer-spin-button, .quantity-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
</style>

<main class="detail-container">
    <div class="breadcrumb">
        <a href="index.php">Trang chủ</a>
        <span class="separator">›</span>
        <a href="category.php">Cửa hàng</a>
        <span class="separator">›</span>
        <span class="current" style="display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; max-width: 400px;"><?= htmlspecialchars($product['ProductName']) ?></span>
    </div>

    <div class="product-layout">
        <div class="product-image-wrapper">
            <?php $imgSrc = !empty($product['ImageURL']) ? url('assets' . $product['ImageURL']) : asset('images/default-book.png'); ?>
            <img src="<?= $imgSrc ?>" class="product-main-image" alt="<?= htmlspecialchars($product['ProductName']) ?>">
        </div>

        <div class="product-info-wrapper">
            <h1 class="product-detail-title"><?= htmlspecialchars($product['ProductName']) ?></h1>
            
            <div class="product-meta-row">
                <div>Thể loại: <strong style="color: var(--color-text);"><?= htmlspecialchars($product['CategoryName'] ?? 'Chưa phân loại') ?></strong></div>
                <div>|</div>
                <div>Trạng thái kho: 
                    <?php if($product['Status'] == 'Hết hàng'): ?>
                        <span class="badge badge--error">Hết hàng</span>
                    <?php else: ?>
                        <span class="badge badge--success">Còn hàng</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="product-detail-price"><?= number_format($product['Price'], 0, ',', '.') ?> đ</div>

            <div class="product-description-box">
                <h4 style="margin: 0 0 var(--spacing-xs) 0; color: var(--color-text);">Tóm tắt nội dung tác phẩm:</h4>
                <div style="white-space: pre-line; color: var(--color-text-light);">
                    <?= !empty($product['Description']) ? htmlspecialchars($product['Description']) : 'Mô tả nội dung chi tiết cho đầu sách này đang được cập nhật...' ?>
                </div>
            </div>

            <form action="../cart/add.php" method="POST" class="purchase-action-box">
                <input type="hidden" name="product_id" value="<?= $product['ProductID'] ?>">
                
                <div class="form-group quantity-select-group">
                    <label class="form-label" style="font-weight: bold; margin-bottom: 6px;">Chọn số lượng:</label>
                    <div class="quantity-input-wrapper">
                        <button type="button" class="quantity-btn" onclick="decreaseQty()">-</button>
                        <input type="number" id="quantity" name="quantity" class="quantity-input" value="1" min="1" max="99">
                        <button type="button" class="quantity-btn" onclick="increaseQty()">+</button>
                    </div>
                </div>

                <div style="flex: 1;">
                    <button type="submit" class="btn btn--primary" style="width: 100%; height: 40px; font-weight: bold; font-size: 1rem;" <?= $product['Status'] == 'Hết hàng' ? 'disabled' : '' ?>>
                        🛒 Thêm vào giỏ hàng
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
<script>
    const qtyInput = document.getElementById('quantity');
    function increaseQty() { let c = parseInt(qtyInput.value) || 1; if(c < 99) qtyInput.value = c + 1; }
    function decreaseQty() { let c = parseInt(qtyInput.value) || 1; if (c > 1) qtyInput.value = c - 1; }
</script>
</body>
</html>