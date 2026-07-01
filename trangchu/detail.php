<?php
require_once '../config/db.php';

$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($productId <= 0) {
    header('Location: index.php');
    exit;
}

$sql_product = "
    SELECT p.ProductID, p.ProductName, p.Price, p.Description, p.Status, c.CategoryName, i.ImageURL,
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
$extraCss = ['css/components/button.css', 'css/components/badge.css', 'css/components/form.css', 'css/components/card.css', 'css/components/review.css'];

// Tính toán đánh giá trung bình và tổng số lượng bình luận
$avgRating = 0;
$totalReviews = 0;
$starCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

$sql_rating_stats = "SELECT AVG(Rating) AS avg_rating, COUNT(*) AS total_reviews FROM review WHERE ProductID = ?";
$stmt_stats = $conn->prepare($sql_rating_stats);
$stmt_stats->bind_param("i", $productId);
$stmt_stats->execute();
$res_stats = $stmt_stats->get_result()->fetch_assoc();
if ($res_stats) {
    $avgRating = $res_stats['avg_rating'] !== null ? round(floatval($res_stats['avg_rating']), 1) : 0;
    $totalReviews = intval($res_stats['total_reviews']);
}
$stmt_stats->close();

if ($totalReviews > 0) {
    $sql_star_distribution = "SELECT Rating, COUNT(*) AS count FROM review WHERE ProductID = ? GROUP BY Rating";
    $stmt_dist = $conn->prepare($sql_star_distribution);
    $stmt_dist->bind_param("i", $productId);
    $stmt_dist->execute();
    $res_dist = $stmt_dist->get_result();
    while ($row = $res_dist->fetch_assoc()) {
        $star = intval($row['Rating']);
        if ($star >= 1 && $star <= 5) {
            $starCounts[$star] = intval($row['count']);
        }
    }
    $stmt_dist->close();
}

// Truy vấn danh sách đánh giá kèm theo thông tin kiểm tra Đã mua hàng
$reviews = [];
$sql_reviews = "
    SELECT r.ReviewID, r.CustomerID, r.Rating, r.Comment, r.ReviewDate, u.FirstName, u.LastName,
           (SELECT COUNT(*) 
            FROM `order` o 
            JOIN `order_detail` od ON o.OrderID = od.OrderID 
            WHERE o.CustomerID = r.CustomerID AND od.ProductID = r.ProductID AND o.OrderStatus = 'Delivered'
           ) AS VerifiedPurchase
    FROM review r
    LEFT JOIN user u ON r.CustomerID = u.CustomerID
    WHERE r.ProductID = ?
    ORDER BY r.ReviewDate DESC
";
$stmt_rev = $conn->prepare($sql_reviews);
$stmt_rev->bind_param("i", $productId);
$stmt_rev->execute();
$res_rev = $stmt_rev->get_result();
while ($row = $res_rev->fetch_assoc()) {
    $reviews[] = $row;
}
$stmt_rev->close();

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
            <?php $imgSrc = getProductImage($product['ImageURL'] ?? ''); ?>
            <img src="<?= $imgSrc ?>" class="product-main-image" alt="<?= htmlspecialchars($product['ProductName']) ?>">
        </div>

        <div class="product-info-wrapper">
            <h1 class="product-detail-title"><?= htmlspecialchars($product['ProductName']) ?></h1>
            
            <div class="product-rating-meta">
                <div class="product-rating-stars">
                    <?php 
                    $fullStars = floor($avgRating);
                    $halfStar = ($avgRating - $fullStars) >= 0.5 ? 1 : 0;
                    $emptyStars = 5 - $fullStars - $halfStar;
                    for ($i = 0; $i < $fullStars; $i++) echo '<i class="fa-solid fa-star"></i>';
                    if ($halfStar) echo '<i class="fa-solid fa-star-half-stroke"></i>';
                    for ($i = 0; $i < $emptyStars; $i++) echo '<i class="fa-regular fa-star"></i>';
                    ?>
                </div>
                <span class="product-rating-average"><?= $avgRating > 0 ? $avgRating : '0.0' ?></span>
                <span class="product-rating-count">(<?= $totalReviews ?> đánh giá)</span>
            </div>

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

            <?php 
            $originalPrice = $product['Price'];
            $discountRate = isset($product['DiscountRate']) ? floatval($product['DiscountRate']) : 0;
            $discountedPrice = $originalPrice - ($originalPrice * $discountRate / 100);
            ?>

            <?php if ($discountRate > 0): ?>
                <div style="display: flex; align-items: baseline; gap: var(--spacing-md); margin: var(--spacing-xs) 0;">
                    <div class="product-detail-price" style="margin: 0;"><?= number_format($discountedPrice, 0, ',', '.') ?> đ</div>
                    <div style="text-decoration: line-through; color: var(--color-text-light); font-size: 1.2rem;"><?= number_format($originalPrice, 0, ',', '.') ?> đ</div>
                    <span class="badge" style="background-color: var(--color-primary); color: white; font-weight: bold; font-size: 0.95rem; padding: 4px 10px; border-radius: var(--border-radius-sm);">-<?= number_format($discountRate, 0) ?>%</span>
                </div>
                <div style="color: #2e7d32; font-weight: bold; font-size: 0.95rem; margin-top: -6px; margin-bottom: var(--spacing-sm);">
                    <i class="fa-solid fa-tags"></i> Áp dụng chương trình: <?= htmlspecialchars($product['PromotionName']) ?>
                </div>
            <?php else: ?>
                <div class="product-detail-price"><?= number_format($originalPrice, 0, ',', '.') ?> đ</div>
            <?php endif; ?>

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
                        <i class="fa-solid fa-cart-plus" style="margin-right: 8px;"></i>Thêm vào giỏ hàng
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- PHẦN ĐÁNH GIÁ & BÌNH LUẬN -->
    <section class="reviews-section">
        <h2 class="reviews-section-title">
            <i class="fa-solid fa-comments"></i> Đánh giá từ độc giả
        </h2>

        <!-- Khối thống kê tổng quan -->
        <div class="rating-summary-box">
            <div class="rating-average-card">
                <div class="rating-average-number"><?= $avgRating > 0 ? $avgRating : '0.0' ?></div>
                <div class="rating-average-stars">
                    <?php 
                    for ($i = 0; $i < $fullStars; $i++) echo '<i class="fa-solid fa-star"></i>';
                    if ($halfStar) echo '<i class="fa-solid fa-star-half-stroke"></i>';
                    for ($i = 0; $i < $emptyStars; $i++) echo '<i class="fa-regular fa-star"></i>';
                    ?>
                </div>
                <div class="rating-average-count">Tất cả <?= $totalReviews ?> đánh giá</div>
            </div>

            <div class="rating-bars-list">
                <?php for ($star = 5; $star >= 1; $star--): 
                    $count = $starCounts[$star];
                    $percent = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
                ?>
                    <div class="rating-bar-row">
                        <span class="rating-bar-label"><?= $star ?> sao <i class="fa-solid fa-star" style="color: var(--color-secondary); font-size: 0.8rem;"></i></span>
                        <div class="rating-bar-track">
                            <div class="rating-bar-fill" style="width: <?= $percent ?>%;"></div>
                        </div>
                        <span class="rating-bar-percent"><?= $percent ?>%</span>
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Thông báo từ session -->
        <?php if (isset($_SESSION['review_success'])): ?>
            <div class="alert alert--success" style="margin-bottom: var(--spacing-md);">
                <i class="alert__icon fa-solid fa-circle-check"></i>
                <div class="alert__content">
                    <div class="alert__title">Thành công</div>
                    <div><?= htmlspecialchars($_SESSION['review_success']) ?></div>
                </div>
            </div>
            <?php unset($_SESSION['review_success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['review_error'])): ?>
            <div class="alert alert--error" style="margin-bottom: var(--spacing-md);">
                <i class="alert__icon fa-solid fa-circle-xmark"></i>
                <div class="alert__content">
                    <div class="alert__title">Lỗi</div>
                    <div><?= htmlspecialchars($_SESSION['review_error']) ?></div>
                </div>
            </div>
            <?php unset($_SESSION['review_error']); ?>
        <?php endif; ?>

        <!-- Danh sách bình luận -->
        <div class="reviews-list">
            <?php if (empty($reviews)): ?>
                <div class="no-reviews-placeholder">
                    <i class="fa-regular fa-comment-dots"></i>
                    <span>Chưa có đánh giá nào cho cuốn sách này. Hãy là người đầu tiên chia sẻ cảm nhận của bạn!</span>
                </div>
            <?php else: ?>
                <?php foreach ($reviews as $rev): 
                    // Tạo avatar viết tắt từ tên người dùng
                    $fullName = trim(($rev['LastName'] ?? '') . ' ' . ($rev['FirstName'] ?? ''));
                    if (empty($fullName)) {
                        $fullName = 'Khách hàng';
                    }
                    $words = explode(' ', $fullName);
                    $initials = '';
                    if (count($words) >= 2) {
                        $initials = mb_substr($words[0], 0, 1) . mb_substr(end($words), 0, 1);
                    } else {
                        $initials = mb_substr($fullName, 0, 2);
                    }
                    $initials = mb_strtoupper($initials);
                ?>
                    <div class="review-item">
                        <div class="review-avatar"><?= htmlspecialchars($initials) ?></div>
                        <div class="review-body">
                            <div class="review-header">
                                <div class="review-user-info">
                                    <span class="review-username"><?= htmlspecialchars($fullName) ?></span>
                                    <?php if (intval($rev['VerifiedPurchase']) > 0): ?>
                                        <span class="review-verified-badge">
                                            <i class="fa-solid fa-circle-check"></i> Đã mua hàng
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <span class="review-date"><?= date('d/m/Y H:i', strtotime($rev['ReviewDate'])) ?></span>
                            </div>
                            <div class="review-stars">
                                <?php 
                                $r = intval($rev['Rating']);
                                for ($i = 0; $i < $r; $i++) echo '<i class="fa-solid fa-star"></i>';
                                for ($i = 0; $i < (5 - $r); $i++) echo '<i class="fa-regular fa-star" style="color: #ccc;"></i>';
                                ?>
                            </div>
                            <p class="review-comment"><?= htmlspecialchars($rev['Comment']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Biểu mẫu gửi đánh giá mới -->
        <div class="review-form-container" id="review-form-section">
            <h3 class="review-form-title">Để lại nhận xét của bạn</h3>
            <?php if (isset($_SESSION['user'])): ?>
                <form action="add_review.php" method="POST">
                    <input type="hidden" name="product_id" value="<?= $productId ?>">
                    
                    <div class="star-selector-group">
                        <span class="star-selector-label">Đánh giá của bạn về cuốn sách này:</span>
                        <div class="rating-input">
                            <input type="radio" id="star5" name="rating" value="5" required>
                            <label for="star5" title="5 sao"><i class="fa-solid fa-star"></i></label>
                            
                            <input type="radio" id="star4" name="rating" value="4">
                            <label for="star4" title="4 sao"><i class="fa-solid fa-star"></i></label>
                            
                            <input type="radio" id="star3" name="rating" value="3">
                            <label for="star3" title="3 sao"><i class="fa-solid fa-star"></i></label>
                            
                            <input type="radio" id="star2" name="rating" value="2">
                            <label for="star2" title="2 sao"><i class="fa-solid fa-star"></i></label>
                            
                            <input type="radio" id="star1" name="rating" value="1">
                            <label for="star1" title="1 sao"><i class="fa-solid fa-star"></i></label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="comment" style="font-weight: bold;">Nội dung bình luận:</label>
                        <textarea class="form-control" id="comment" name="comment" rows="4" placeholder="Nhập cảm nhận của bạn về nội dung cuốn sách, dịch vụ giao hàng hoặc đóng gói..." required style="min-height: 100px; padding: 10px; width: 100%; border: var(--border-width) solid var(--color-border); border-radius: var(--border-radius-sm); font-family: inherit; font-size: 14px; box-sizing: border-box;"></textarea>
                    </div>
                    
                    <div style="margin-top: var(--spacing-md); text-align: right;">
                        <button type="submit" class="btn btn--primary" style="padding: 10px 24px; font-weight: bold; min-height: 40px;">Gửi đánh giá</button>
                    </div>
                </form>
            <?php else: ?>
                <div style="text-align: center; padding: var(--spacing-md) 0;">
                    <p style="color: var(--color-text-light); margin-bottom: var(--spacing-sm);">Bạn phải đăng nhập tài khoản thành viên để gửi bình luận và đánh giá cho cuốn sách này.</p>
                    <a href="<?= url('auth/pages/login.php') ?>" class="btn btn--outline" style="padding: 8px 20px; font-weight: bold; text-decoration: none; display: inline-block;">Đăng nhập ngay</a>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>
<script>
    const qtyInput = document.getElementById('quantity');
    function increaseQty() { let c = parseInt(qtyInput.value) || 1; if(c < 99) qtyInput.value = c + 1; }
    function decreaseQty() { let c = parseInt(qtyInput.value) || 1; if (c > 1) qtyInput.value = c - 1; }

    // Tự động cuộn mượt và focus vào ô bình luận khi url có #review-form-section
    window.addEventListener('DOMContentLoaded', () => {
        if (window.location.hash === '#review-form-section') {
            const reviewForm = document.getElementById('review-form-section');
            if (reviewForm) {
                setTimeout(() => {
                    reviewForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    const commentArea = document.getElementById('comment');
                    if (commentArea) {
                        commentArea.focus();
                    }
                }, 300); // Trì hoãn nhẹ để trang ổn định giao diện trước khi cuộn
            }
        }
    });
</script>
</body>
</html>