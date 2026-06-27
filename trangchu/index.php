<?php
require_once '../config/db.php';

// Vẫn giữ lại cờ trang chủ nhưng menu giờ đây sẽ được CSS điều khiển đóng hoàn toàn
$isHomepage = true;

$pageTitle = 'Trang chủ - Hệ Thống Bán Sách Trực Tuyến';
$extraCss = ['css/components/card.css', 'css/components/button.css', 'css/components/badge.css', 'css/components/form.css'];
include '../includes/header.php';

// Thực hiện câu lệnh truy vấn sản phẩm mới cập nhật
$sql_products = "
    SELECT p.ProductID, p.ProductName, p.Price, p.Status, c.CategoryName, i.ImageURL 
    FROM product p
    LEFT JOIN category c ON p.CategoryID = c.CategoryID
    LEFT JOIN image i ON p.ProductID = i.ProductID AND i.IsThumbnail = 1
    ORDER BY p.ProductID DESC
    LIMIT 8
";
$products = $conn->query($sql_products);
?>

<style>
    /* Đã sửa đổi: Loại bỏ hoàn toàn chia cột bất đối xứng, cho phép banner mở rộng tối đa */
    .homepage-layout {
        max-width: 1200px;
        margin: var(--spacing-lg) auto;
        padding: 0 var(--spacing-md);
    }

    .homepage-banner {
        border-radius: var(--border-radius-lg);
        overflow: hidden;
        background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: white;
        padding: var(--spacing-xl);
        min-height: 380px;
        box-shadow: var(--box-shadow-sm);
    }

    /* SECTION LAYOUT */
    .section-container {
        max-width: 1200px;
        margin: var(--spacing-xl) auto;
        padding: 0 var(--spacing-md);
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: var(--spacing-lg);
        border-bottom: 2px solid var(--color-border);
        padding-bottom: var(--spacing-sm);
    }

    .section-title {
        font-size: var(--font-size-xl);
        font-weight: var(--font-weight-bold);
        color: var(--color-text);
        margin: 0;
    }
</style>

<main>
    <div class="homepage-layout">
        <div class="homepage-banner">
            <div>
                <h1 style="font-size: 2.6rem; margin: 0 0 var(--spacing-sm); font-weight: 800; text-shadow: 0 2px 4px rgba(0,0,0,0.15);">Khơi Nguồn Tri Thức</h1>
                <p style="font-size: 1.15rem; margin-bottom: var(--spacing-xl); opacity: 0.95;">Khám phá hàng ngàn đầu sách chọn lọc tinh hoa cùng WebBanSach</p>
                
                <a href="category.php" class="btn btn--primary" style="background-color: white; color: var(--color-primary); border-radius: 999px; padding: 12px 36px; font-size: 1.1rem; font-weight: 900; box-shadow: var(--box-shadow-md); text-decoration: none; display: inline-block;">
                    MUA SẮM NGAY
                </a>
            </div>
        </div>
    </div>

    <section class="section-container">
        <div class="section-header">
            <h2 class="section-title">Sách Mới Cập Nhật</h2>
            <a href="category.php" class="btn btn--ghost btn--sm" style="font-weight: bold;">Xem tất cả →</a>
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
                            <p class="card__subtitle"><?= htmlspecialchars($product['CategoryName'] ?? 'Chưa phân loại') ?></p>
                            
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
                <div style="grid-column: 1 / -1; text-align: center; padding: var(--spacing-xl); color: var(--color-text-light);">
                    <p>Hiện chưa có sản phẩm nào được cập nhật trong hệ thống CSDL.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>
</body>
</html>