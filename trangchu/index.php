<?php
require_once '../config/db.php';

// ĐÁNH DẤU CỜ ĐỂ BUNG SẴN DANH MỤC TRÊN TRANG CHỦ
$isHomepage = true;

$pageTitle = 'Trang chủ - Hệ Thống Bán Sách Trực Tuyến';
$extraCss = ['css/components/card.css', 'css/components/button.css', 'css/components/badge.css', 'css/components/form.css'];
include '../includes/header.php';

// Thực hiện câu lệnh truy vấn sản phẩm mới (Đã bổ sung cột Price, kết nối bảng ảnh và lấy thông tin khuyến mãi đang hoạt động)
$sql_products = "
    SELECT p.ProductID, p.ProductName, p.Price, p.Status, c.CategoryName, i.ImageURL,
           ap.DiscountRate, ap.PromotionName
    FROM product p
    LEFT JOIN category c ON p.CategoryID = c.CategoryID
    LEFT JOIN image i ON p.ProductID = i.ProductID AND i.IsThumbnail = 1
    LEFT JOIN (
        SELECT pd.ProductID, MAX(pd.DiscountRate) AS DiscountRate, MIN(pr.PromotionName) AS PromotionName
        FROM promotion_detail pd
        JOIN promotion pr ON pd.PromotionID = pr.PromotionID
        WHERE NOW() BETWEEN COALESCE(pd.StartDate, pr.StartDate) AND COALESCE(pd.EndDate, pr.EndDate)
        GROUP BY pd.ProductID
    ) ap ON p.ProductID = ap.ProductID
    ORDER BY p.ProductID DESC
    LIMIT 8
";
$products = $conn->query($sql_products);
?>

<style>
    /* BỐ CỤC KHỐI HERO BANNER HAI CỘT */
    .homepage-layout {
        max-width: 1200px;
        margin: var(--spacing-lg) auto;
        padding: 0 var(--spacing-md);
        display: grid;
        grid-template-columns: 260px 1fr;
        gap: var(--spacing-md);
    }

    .sidebar-placeholder {
        width: 260px;
        height: 100%;
        min-height: 380px;
    }

    .homepage-banner {
        border-radius: var(--border-radius);
        overflow: hidden;
        background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: green;
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

    @media (max-width: 768px) {
        .homepage-layout {
            grid-template-columns: 1fr;
        }
        .sidebar-placeholder {
            display: none; /* Ẩn khoảng trống trên mobile vì danh mục đã chuyển thành dropdown ẩn */
        }
    }
</style>

<main>
    <div class="homepage-layout">
        <div class="sidebar-placeholder"></div>

        <!-- CỘT PHẢI: BANNER CHÍNH CỦA TRANG CHỦ -->
        <div class="homepage-banner" style="background-image: url('<?= asset('images/uploads/banner_homepage.jpg') ?>'); background-size: cover; background-position: center;">
            <div>
                <h1 style="font-size: 2.6rem; margin: 0 0 var(--spacing-sm); font-weight: 800; text-shadow: 0 2px 4px rgba(0,0,0,0.15);">Khơi Nguồn Tri Thức</h1>
                <p style="font-size: 1.15rem; margin-bottom: var(--spacing-xl); opacity: 0.95;">Khám phá hàng ngàn đầu sách chọn lọc tinh hoa cùng WebBanSach</p>
                
                <!-- Đã thay thế ô tìm kiếm thừa bằng Nút bấm điều hướng -->
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
                            $imgSrc = getProductImage($product['ImageURL'] ?? ''); 
                        ?>
                        <div style="height: 220px; display: flex; align-items: center; justify-content: center; background: var(--color-background); padding: 12px; box-sizing: border-box; overflow: hidden;">
                            <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($product['ProductName']) ?>" style="max-height: 100%; max-width: 100%; object-fit: contain; border-radius: 4px; box-shadow: 0 4px 8px rgba(0,0,0,0.08); transition: transform 0.3s;">
                        </div>
                        
                        <div class="card__body">
                            <h3 class="card__title" style="min-height: 44px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?= htmlspecialchars($product['ProductName']) ?></h3>
                            <p class="card__subtitle"><?= htmlspecialchars($product['CategoryName'] ?? 'Chưa phân loại') ?></p>
                            
                            <?php 
                            $originalPrice = $product['Price'];
                            $discountRate = isset($product['DiscountRate']) ? floatval($product['DiscountRate']) : 0;
                            $discountedPrice = $originalPrice - ($originalPrice * $discountRate / 100);
                            ?>

                            <?php if ($discountRate > 0): ?>
                                <div class="card__price" style="display: flex; align-items: baseline; flex-wrap: wrap; gap: 6px;">
                                    <span style="color: var(--color-primary); font-weight: bold;"><?= number_format($discountedPrice, 0, ',', '.') ?> đ</span>
                                    <span style="text-decoration: line-through; color: var(--color-text-light); font-size: 0.85rem; font-weight: normal;"><?= number_format($originalPrice, 0, ',', '.') ?> đ</span>
                                </div>
                            <?php else: ?>
                                <div class="card__price">
                                    <?= number_format($originalPrice, 0, ',', '.') ?> đ
                                </div>
                            <?php endif; ?>
                            
                            <div style="display: flex; gap: var(--spacing-xs); align-items: center; margin-top: 8px; flex-wrap: wrap;">
                                <?php if($product['Status'] == 'Hết hàng'): ?>
                                    <span class="badge badge--error">Hết hàng</span>
                                <?php else: ?>
                                    <span class="badge badge--success">Còn hàng</span>
                                <?php endif; ?>
                                
                                <?php if ($discountRate > 0): ?>
                                    <span class="badge badge--warning" style="background-color: var(--color-primary); color: white; font-weight: bold;">-<?= number_format($discountRate, 0) ?>%</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="card__footer">
                            <a href="detail.php?id=<?= $product['ProductID'] ?>" class="btn btn--outline btn--sm" style="flex: 1; text-align: center; line-height: 28px;">Chi tiết</a>
                            <form action="../cart/add.php" method="POST" style="flex: 1; margin: 0; display: flex;">
                                <input type="hidden" name="product_id" value="<?= $product['ProductID'] ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn--primary btn--sm" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 6px;" <?= $product['Status'] == 'Hết hàng' ? 'disabled' : '' ?>>
                                    <i class="fa-solid fa-cart-plus"></i> Thêm
                                </button>
                            </form>
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