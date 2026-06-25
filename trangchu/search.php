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
        SELECT p.ProductID, p.ProductName, p.Price, p.Status, c.CategoryName, i.ImageURL 
        FROM product p
        LEFT JOIN category c ON p.CategoryID = c.CategoryID
        LEFT JOIN image i ON p.ProductID = i.ProductID AND i.IsThumbnail = 1
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
            <div style="grid-column: 1 / -1; text-align: center; padding: 50px var(--spacing-md); background: var(--color-background); border-radius: var(--border-radius-lg);">
                <div style="font-size: 3rem; margin-bottom: var(--spacing-sm);">🔍</div>
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