<?php
require_once '../config/db.php';

// Lấy từ khóa tìm kiếm từ URL (Hỗ trợ cả biến 'q' hoặc 'keyword' tùy thuộc form của bạn)
$keyword = '';
if (isset($_GET['keyword'])) {
    $keyword = trim($_GET['keyword']);
} elseif (isset($_GET['q'])) {
    $keyword = trim($_GET['q']);
}

$pageTitle = 'Kết quả tìm kiếm: ' . htmlspecialchars($keyword);

// Gọi các file CSS giao diện thẻ sản phẩm và lưới để không bị lỗi vỡ khung
$extraCss = ['css/components/card.css', 'css/components/button.css', 'css/components/badge.css', 'css/components/form.css'];
include '../includes/header.php';

// Truy vấn sản phẩm theo từ khóa
$products = null;
if (!empty($keyword)) {
    // Sử dụng Prepared Statement để tìm kiếm theo tên sách hoặc tên hãng, chống lỗi nháy đơn
    $stmt = $conn->prepare("
        SELECT p.ProductID, p.ProductName, p.Price, p.Status, p.Publisher, c.CategoryName, i.ImageURL 
        FROM product p
        LEFT JOIN category c ON p.CategoryID = c.CategoryID
        LEFT JOIN image i ON p.ProductID = i.ProductID AND i.IsThumbnail = 1
        WHERE p.ProductName LIKE CONCAT('%', ?, '%') OR p.Publisher LIKE CONCAT('%', ?, '%')
        ORDER BY p.ProductID DESC
    ");
    $stmt->bind_param("ss", $keyword, $keyword);
    $stmt->execute();
    $products = $stmt->get_result();
}
?>

<style>
    .page-container {
        max-width: 1200px;
        margin: 0 auto var(--spacing-xl);
        padding: 0 var(--spacing-md);
    }
    
    .breadcrumb {
        display: flex; align-items: center; gap: var(--spacing-xs); padding: var(--spacing-md) 0; font-size: 0.95rem; color: var(--color-text);
    }
    .breadcrumb a { color: var(--color-text); text-decoration: none; }
    .breadcrumb a:hover { color: var(--color-primary); }
    .breadcrumb .separator { color: var(--color-text-light); font-size: 1.1rem; }
    .breadcrumb .current { color: var(--color-primary); font-weight: var(--font-weight-medium); }

    /* CSS RIÊNG CHO BANNER TRANG TÌM KIẾM */
    .search-banner {
        width: 100%; min-height: 200px;
        /* Đang dùng ảnh banner_default.jpg. Nếu muốn ảnh khác, copy vào thư mục images và đổi tên ở đây */
        background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('<?= asset('images/banner_default.jpg') ?>') center/cover;
        border-radius: var(--border-radius-lg); margin-bottom: var(--spacing-lg);
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        text-align: center; padding: var(--spacing-md);
        box-shadow: var(--box-shadow-md);
    }
    .search-banner h1, .search-banner p {
        color: #ffffff !important;
        margin: 0;
    }
</style>

<main class="page-container">
    
    <div class="breadcrumb">
        <a href="index.php">Trang chủ</a>
        <span class="separator">›</span>
        <span class="current">Tìm kiếm</span>
    </div>

    <!-- KHỐI BANNER TÌM KIẾM -->
    <div class="search-banner">
        <h1 style="font-size: 2.2rem; font-weight: 800; text-transform: uppercase;">Kết Quả Tìm Kiếm</h1>
        <?php if (!empty($keyword)): ?>
            <p style="margin-top: 8px; font-size: 1.1rem;">Đang hiển thị kết quả cho từ khóa: <strong style="color: var(--color-secondary);">'<?= htmlspecialchars($keyword) ?>'</strong></p>
        <?php else: ?>
            <p style="margin-top: 8px; font-size: 1.1rem;">Vui lòng nhập từ khóa để tìm kiếm sách</p>
        <?php endif; ?>
    </div>

    <div class="card-grid">
        <?php if ($products && $products->num_rows > 0): ?>
            <?php while($product = $products->fetch_assoc()): ?>
                <div class="card card--interactive">
                    <?php 
                        $imgSrc = !empty($product['ImageURL']) ? url('assets' . $product['ImageURL']) : asset('images/default-book.png'); 
                    ?>
                    <div style="text-align: center; background: var(--color-background); padding: var(--spacing-md);">
                        <img src="<?= $imgSrc ?>" class="card__image" alt="<?= htmlspecialchars($product['ProductName']) ?>" style="height: 180px; object-fit: contain; width: 100%;">
                    </div>
                    
                    <div class="card__body">
                        <h3 class="card__title" style="min-height: 44px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?= htmlspecialchars($product['ProductName']) ?></h3>
                        
                        <p class="card__subtitle" style="margin-top: 4px;">Hãng: <span style="color: var(--color-primary); font-weight: bold;"><?= htmlspecialchars($product['Publisher'] ?? 'Chưa rõ') ?></span></p>
                        
                        <div class="card__price">
                            <?= number_format($product['Price'], 0, ',', '.') ?> đ
                        </div> 
                        
                        <?php if($product['Status'] == 'Hết hàng'): ?>
                            <span class="badge badge--error" style="width: fit-content; margin-top: 8px;">Hết hàng</span>
                        <?php else: ?>
                            <span class="badge badge--success" style="width: fit-content; margin-top: 8px;">Còn hàng</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card__footer">
                        <a href="detail.php?id=<?= $product['ProductID'] ?>" class="btn btn--outline btn--sm" style="flex: 1; text-align: center;">Chi tiết</a>
                        <button class="btn btn--primary btn--sm" style="flex: 1;" <?= $product['Status'] == 'Hết hàng' ? 'disabled' : '' ?>>
                            🛒 Thêm
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px var(--spacing-md); background: var(--color-background); border-radius: var(--border-radius-lg);">
                <span style="font-size: 3rem;">🔍</span>
                <?php if (!empty($keyword)): ?>
                    <h3 style="margin: var(--spacing-sm) 0; color: var(--color-text);">Không tìm thấy cuốn sách nào khớp với "<?= htmlspecialchars($keyword) ?>"</h3>
                    <p style="color: var(--color-text-light); margin-bottom: var(--spacing-md);">Vui lòng thử lại với từ khóa khác (ví dụ: tên sách, nhà xuất bản).</p>
                <?php else: ?>
                    <h3 style="margin: var(--spacing-sm) 0; color: var(--color-text);">Bạn chưa nhập từ khóa tìm kiếm</h3>
                <?php endif; ?>
                <a href="index.php" class="btn btn--primary">Quay lại Trang chủ</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
</body>
</html>