<?php
require_once '../config/db.php';

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$pageTitle = 'Kết quả tìm kiếm cho: "' . htmlspecialchars($keyword) . '"';
$extraCss = ['css/components/card.css', 'css/components/button.css', 'css/components/badge.css', 'css/components/form.css'];
include '../includes/header.php';

$products = null;
if (!empty($keyword)) {
    $searchPattern = "%" . $keyword . "%";
    
    $sql_search = "
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
        WHERE p.ProductName LIKE ? OR c.CategoryName LIKE ? OR p.Description LIKE ?
        ORDER BY p.ProductID DESC
    ";
    
    $stmt = $conn->prepare($sql_search);
    $stmt->bind_param("sss", $searchPattern, $searchPattern, $searchPattern);
    $stmt->execute();
    $products = $stmt->get_result();
}
?>

<style>
    .search-container {
        max-width: 1200px;
        margin: 0 auto var(--spacing-xl);
        padding: 0 var(--spacing-md);
    }
    .search-header {
        margin-bottom: var(--spacing-xl);
        border-bottom: 2px solid var(--color-border);
        padding: var(--spacing-md) 0;
    }
    .search-title {
        font-size: var(--font-size-xl);
        color: var(--color-text);
        margin: 0 0 var(--spacing-xs);
    }
    .keyword-highlight {
        color: var(--color-primary);
        font-style: italic;
    }
    .breadcrumb {
        display: flex;
        align-items: center;
        gap: var(--spacing-xs);
        padding: var(--spacing-md) 0;
        font-size: 0.95rem;
    }
    .breadcrumb a { color: var(--color-text); text-decoration: none; }
    .breadcrumb .separator { color: var(--color-text-light); }
    .breadcrumb .current { color: var(--color-primary); }
</style>

<main class="search-container">
    <div class="breadcrumb">
        <a href="index.php">Trang chủ</a>
        <span class="separator">›</span>
        <span class="current">Tìm kiếm sản phẩm</span>
    </div>

    <div class="search-header">
        <h1 class="search-title">Kết quả tìm kiếm cho: <span class="keyword-highlight">"<?= htmlspecialchars($keyword) ?>"</span></h1>
        <?php if ($products): ?>
            <p style="color: var(--color-text-light); margin: 0;">Tìm thấy <strong><?= $products->num_rows ?></strong> đầu sách phù hợp với yêu cầu tìm kiếm của bạn.</p>
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
            <div style="grid-column: 1 / -1; text-align: center; padding: 50px var(--spacing-md); background: var(--color-background); border-radius: var(--border-radius-lg);">
                <i class="fa-solid fa-magnifying-glass" style="font-size: 3rem; color: var(--color-text-light); display: block; margin-bottom: 12px;"></i>
                <h3>Rất tiếc, không tìm thấy sản phẩm tương thích!</h3>
                <p style="color: var(--color-text-light); margin-bottom: var(--spacing-lg);">Hãy thử tra cứu lại bằng một từ khóa tổng quan hơn hoặc nhập tên tựa đề sách khác.</p>
                <form action="search.php" method="GET" style="max-width: 450px; margin: 0 auto; display: flex; gap: var(--spacing-xs);">
                    <input type="text" name="keyword" class="form-control" placeholder="Nhập từ khóa tìm kiếm mới..." required value="<?= htmlspecialchars($keyword) ?>">
                    <button type="submit" class="btn btn--primary">Tìm kiếm</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
</body>
</html>