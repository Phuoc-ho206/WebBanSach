<?php
require_once '../config/db.php';

// Lấy các tham số lọc từ URL (GET)
$categoryId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$minPrice   = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? intval($_GET['min_price']) : 0;
$maxPrice   = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? intval($_GET['max_price']) : 0;
$publisher  = isset($_GET['publisher']) ? trim($_GET['publisher']) : '';

// Lấy thông tin tiêu đề danh mục
$categoryName = "Tất cả danh mục";
if ($categoryId > 0) {
    $stmt_cat = $conn->prepare("SELECT CategoryName FROM category WHERE CategoryID = ?");
    $stmt_cat->bind_param("i", $categoryId);
    $stmt_cat->execute();
    $result_cat = $stmt_cat->get_result();
    if ($row = $result_cat->fetch_assoc()) {
        $categoryName = $row['CategoryName'];
    }
    $stmt_cat->close();
}

$pageTitle = $categoryName . ' - Danh mục sách';
$extraCss = ['css/components/card.css', 'css/components/button.css', 'css/components/badge.css', 'css/components/form.css'];
include '../includes/header.php';

// XÂY DỰNG CÂU LỆNH TRUY VẤN LỌC SẢN PHẨM (FILTER LOGIC)
$sql_products = "
    SELECT p.ProductID, p.ProductName, p.Price, p.Status, p.Publisher, c.CategoryName, i.ImageURL 
    FROM product p
    LEFT JOIN category c ON p.CategoryID = c.CategoryID
    LEFT JOIN image i ON p.ProductID = i.ProductID AND i.IsThumbnail = 1
    WHERE 1=1
";

if ($categoryId > 0) $sql_products .= " AND p.CategoryID = $categoryId";
if ($minPrice > 0) $sql_products .= " AND p.Price >= $minPrice";
if ($maxPrice > 0) $sql_products .= " AND p.Price <= $maxPrice";
if (!empty($publisher)) {
    $safe_publisher = $conn->real_escape_string($publisher);
    $sql_products .= " AND p.Publisher = '$safe_publisher'";
}

$sql_products .= " ORDER BY p.ProductID DESC";
$products = $conn->query($sql_products);
$publishers_query = $conn->query("SELECT DISTINCT Publisher FROM product WHERE Publisher IS NOT NULL AND Publisher != ''");
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

    /* CSS RIÊNG CHO BANNER TRANG DANH MỤC */
    .category-banner {
        width: 100%; min-height: 200px;
        border-radius: var(--border-radius-lg); margin-bottom: var(--spacing-lg);
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        text-align: center; padding: var(--spacing-md);
        box-shadow: var(--box-shadow-md);
    }
    .category-banner h1 {
        color: #ffffff !important;
    }

    .advanced-filter-bar {
        background-color: var(--color-surface); border: 1px solid var(--color-border);
        border-radius: var(--border-radius-lg); padding: var(--spacing-md) var(--spacing-lg);
        margin-bottom: var(--spacing-xl); box-shadow: var(--box-shadow-sm);
    }
    .filter-form { display: flex; flex-wrap: wrap; align-items: center; gap: var(--spacing-xl); }
    .filter-group { display: flex; align-items: center; gap: var(--spacing-sm); }
    .filter-label { font-weight: var(--font-weight-bold); color: var(--color-text); font-size: 0.95rem; min-width: 90px; }
    .filter-price-inputs { display: flex; align-items: center; gap: 8px; }
    .filter-input { padding: 8px 12px; border: 1px solid var(--color-border); border-radius: var(--border-radius); outline: none; width: 130px; }
    .filter-input:focus { border-color: var(--color-primary); }
    .filter-select { padding: 8px 12px; border: 1px solid var(--color-border); border-radius: var(--border-radius); outline: none; min-width: 180px; background-color: white; }

    @media (max-width: 768px) {
        .filter-form { flex-direction: column; align-items: flex-start; gap: var(--spacing-md); }
        .filter-price-inputs, .filter-select { width: 100%; }
        .filter-input { width: 45%; }
    }
</style>

<main class="page-container">
    
    <div class="breadcrumb">
        <a href="index.php">Trang chủ</a>
        <span class="separator">›</span>
        <span class="current"><?= htmlspecialchars($categoryName) ?></span>
    </div>

    <?php 
        // Dùng chung 1 tấm ảnh duy nhất cho TẤT CẢ các danh mục
        $bannerImage = 'banner_default.jpg';
    ?>
    
    <!-- KHỐI BANNER CHÍNH CỦA TRANG DANH MỤC -->
    <!-- Chèn trực tiếp lớp màu đen mờ 40% và đường dẫn ảnh động vào thuộc tính style -->
    <div class="category-banner" style="background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('<?= asset("images/" . $bannerImage) ?>') center/cover;">
        <h1 style="font-size: 2.2rem; font-weight: 800; margin: 0; text-transform: uppercase;">Tủ Sách <?= htmlspecialchars($categoryName) ?></h1>
    </div>

    <div class="advanced-filter-bar">
        <form action="category.php" method="GET" class="filter-form">
            <?php if ($categoryId > 0): ?>
                <input type="hidden" name="id" value="<?= $categoryId ?>">
            <?php endif; ?>

            <div class="filter-group">
                <span class="filter-label">Khoảng giá:</span>
                <div class="filter-price-inputs">
                    <input type="number" name="min_price" class="filter-input" placeholder="0 đ" value="<?= $minPrice > 0 ? $minPrice : '' ?>" min="0">
                    <span style="color: var(--color-text-light);">-</span>
                    <input type="number" name="max_price" class="filter-input" placeholder="1.000.000 đ" value="<?= $maxPrice > 0 ? $maxPrice : '' ?>" min="0">
                </div>
            </div>

            <div class="filter-group">
                <span class="filter-label">Thương hiệu:</span>
                <select name="publisher" class="filter-select">
                    <option value="">-- Tất cả thương hiệu --</option>
                    <?php if ($publishers_query && $publishers_query->num_rows > 0): ?>
                        <?php while($pub = $publishers_query->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($pub['Publisher']) ?>" <?= ($publisher === $pub['Publisher']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pub['Publisher']) ?>
                            </option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <option value="AlphaBooks">AlphaBooks</option>
                        <option value="Nhã Nam">Nhã Nam</option>
                    <?php endif; ?>
                </select>
            </div>

            <button type="submit" class="btn btn--primary" style="padding: 8px 24px; font-weight: bold;">Lọc kết quả</button>
            
            <?php if ($minPrice > 0 || $maxPrice > 0 || !empty($publisher)): ?>
                <a href="category.php<?= $categoryId > 0 ? '?id='.$categoryId : '' ?>" class="btn btn--ghost" style="padding: 8px;">Xóa lọc ✖</a>
            <?php endif; ?>
        </form>
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
                <h3 style="margin: var(--spacing-sm) 0; color: var(--color-text);">Không tìm thấy sách phù hợp với bộ lọc</h3>
                <p style="color: var(--color-text-light); margin-bottom: var(--spacing-md);">Vui lòng thử điều chỉnh lại khoảng giá hoặc chọn thương hiệu khác.</p>
                <a href="category.php<?= $categoryId > 0 ? '?id='.$categoryId : '' ?>" class="btn btn--primary">Xóa bộ lọc</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
</body>
</html>