<?php
require_once '../config/db.php';

$pageTitle = 'Trang chủ - Khơi Nguồn Tri Thức';
// Khai báo thêm dòng này để gọi CSS dạng lưới và thẻ sản phẩm
$extraCss = ['css/components/card.css', 'css/components/button.css', 'css/components/badge.css'];
include '../includes/header.php';
// Truy vấn lấy 8 sản phẩm mới nhất để hiển thị ở trang chủ
$sql_latest = "
    SELECT p.ProductID, p.ProductName, p.Price, p.Status, p.Publisher, i.ImageURL 
    FROM product p
    LEFT JOIN image i ON p.ProductID = i.ProductID AND i.IsThumbnail = 1
    ORDER BY p.ProductID DESC 
    LIMIT 8
";
$latest_products = $conn->query($sql_latest);
?>

<style>
    .page-container {
        max-width: 1200px;
        margin: 0 auto var(--spacing-xl);
        padding: 0 var(--spacing-md);
    }
    
    /* CSS RIÊNG CHO BANNER TRANG CHỦ */
    .home-banner {
        width: 100%;
        min-height: 240px;
        /* Chèn trực tiếp ảnh và lớp phủ đen mờ 40% ở đây */
        background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('<?= asset('images/banner_default.jpg') ?>') center/cover;
        border-radius: var(--border-radius-lg);
        margin-top: var(--spacing-lg);
        margin-bottom: var(--spacing-lg);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: var(--spacing-md);
        box-shadow: var(--box-shadow-md);
    }
    
    /* Ép chữ trong banner thành màu trắng */
    .home-banner h1, .home-banner p {
        color: #ffffff !important;
    }

    .section-title {
        font-size: 1.8rem; font-weight: 800; text-transform: uppercase;
        margin-top: var(--spacing-xxl); margin-bottom: var(--spacing-lg);
        color: var(--color-text); text-align: center; position: relative;
    }
    .section-title span { color: var(--color-primary); }
    .section-title::after {
        content: ""; display: block; width: 60px; height: 4px;
        background-color: var(--color-primary); margin: 12px auto 0; border-radius: 2px;
    }
</style>

<main class="page-container">
    
    <!-- KHỐI BANNER CHÍNH CỦA TRANG CHỦ -->
    <div class="home-banner">
        <h1 style="font-size: 2.5rem; font-weight: 800; margin: 0 0 8px 0; text-transform: uppercase;">Khơi Nguồn Tri Thức</h1>
        <p style="margin-bottom: 24px; font-size: 1.1rem; max-width: 600px;">Khám phá hàng ngàn đầu sách chọn lọc tinh hoa cùng WebBanSach. Nâng tầm tri thức, mở rộng tư duy.</p>
        <a href="category.php" class="btn btn--white" style="background: #ffffff; color: var(--color-primary) !important; padding: 12px 32px; border-radius: 30px; text-decoration: none; font-weight: 800; box-shadow: var(--box-shadow-md); transition: transform 0.2s;">
            MUA SẮM NGAY
        </a>
    </div>

    <!-- KHỐI SẢN PHẨM MỚI NHẤT -->
    <h2 class="section-title">Sách <span>Mới Nhất</span></h2>
    
    <div class="card-grid">
        <?php if ($latest_products && $latest_products->num_rows > 0): ?>
            <?php while($product = $latest_products->fetch_assoc()): ?>
                <div class="card card--interactive">
                    <?php 
                        $imgSrc = !empty($product['ImageURL']) ? url('assets' . $product['ImageURL']) : asset('images/default-book.png'); 
                    ?>
                    <div style="text-align: center; background: var(--color-background); padding: var(--spacing-md);">
                        <img src="<?= $imgSrc ?>" class="card__image" alt="<?= htmlspecialchars($product['ProductName']) ?>" style="height: 180px; object-fit: contain; width: 100%;">
                    </div>
                    
                    <div class="card__body">
                        <h3 class="card__title" style="min-height: 44px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            <?= htmlspecialchars($product['ProductName']) ?>
                        </h3>
                        
                        <p class="card__subtitle" style="margin-top: 4px;">
                            Hãng: <span style="color: var(--color-primary); font-weight: bold;"><?= htmlspecialchars($product['Publisher'] ?? 'Chưa rõ') ?></span>
                        </p>
                        
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
            <p style="grid-column: 1 / -1; text-align: center; color: var(--color-text-light);">Hiện chưa có sách nào trong hệ thống.</p>
        <?php endif; ?>
    </div>
    
    <div style="text-align: center; margin-top: var(--spacing-xl);">
        <a href="category.php" class="btn btn--outline" style="padding: 10px 32px; font-weight: bold; border-radius: 24px;">Xem tất cả sách →</a>
    </div>

</main>

<?php include '../includes/footer.php'; ?>
</body>
</html>