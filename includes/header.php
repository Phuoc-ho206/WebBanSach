<?php
/** @var string $pageTitle Tiêu đề trang định nghĩa riêng trước khi include */
$pageTitle = $pageTitle ?? 'WebBanSach';

// Thực hiện kết nối và lấy danh mục dùng chung toàn hệ thống từ $conn
$global_categories = [];
if (isset($conn)) {
    $conn->set_charset("utf8mb4");
    $res_cat = $conn->query("SELECT * FROM category ORDER BY CategoryID ASC");
    if ($res_cat && $res_cat->num_rows > 0) {
        while ($c = $res_cat->fetch_assoc()) {
            $global_categories[] = $c;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    
    <link rel="stylesheet" href="/WebBanSach/assets/css/variables.css">
    <link rel="stylesheet" href="<?= asset('css/components/navbar.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/button.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/form.css') ?>">
    
    <?php
    // Tự động tải thêm các file CSS bổ sung được khai báo riêng ở từng trang
    if (isset($extraCss) && is_array($extraCss)) {
        foreach ($extraCss as $cssPath) {
            echo '<link rel="stylesheet" href="' . asset($cssPath) . '">';
        }
    }
    ?>
</head>
<body>

    <header class="header-top">
        <div class="header-top-container">
            <a href="<?= url('/') ?>" style="text-decoration: none; font-size: 1.5rem; font-weight: bold; color: var(--color-primary);">
                📚 WEBBANSACH
            </a>

            <form action="<?= url('trangchu/search.php') ?>" method="GET" class="header-search">
                <input type="text" name="keyword" placeholder="Tìm kiếm tựa sách, tác giả hoặc danh mục..." value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>" required>
                <button type="submit">Tìm kiếm</button>
            </form>

            <div class="header-actions">
                <a href="<?= url('auth/pages/profile.php') ?>" class="header-action-item">
                    <div class="header-action-icon">👤</div>
                    <div class="header-action-text">
                        <span>Tài khoản</span>
                        <strong><?= isset($_SESSION['user']['full_name']) ? htmlspecialchars($_SESSION['user']['full_name']) : 'Đăng nhập' ?></strong>
                    </div>
                </a>

                <a href="<?= url('cart/detail.php') ?>" class="header-action-item">
                    <div class="header-action-icon">
                        🛒
                        <span class="header-cart-badge">0</span>
                    </div>
                    <div class="header-action-text">
                        <span>Giỏ hàng</span>
                        <strong>Đơn hàng</strong>
                    </div>
                </a>
            </div>
        </div>
    </header>

    <nav class="orange-bar">
        <div class="orange-bar-container">
            
            <div class="category-dropdown-wrapper">
                <div class="category-dropdown-header">
                    <span>☰</span> DANH MỤC SẢN PHẨM
                </div>
                
                <ul class="category-sidebar-list">
                    <?php if(!empty($global_categories)): ?>
                        <?php foreach($global_categories as $cat): ?>
                            <li>
                                <a href="<?= url('trangchu/category.php?id=' . $cat['CategoryID']) ?>">
                                    <div style="display: flex; align-items: center; gap: var(--spacing-sm);">
                                        <img src="<?= asset('images/icon/cat_' . $cat['CategoryID'] . '.png') ?>" class="category-menu-icon" alt="" onerror="this.style.display='none'">
                                        <span><?= htmlspecialchars($cat['CategoryName']) ?></span>
                                    </div>
                                    <span>&gt;</span>
                                </a>
                                
                                <div class="category-submenu">
                                    <div class="category-submenu-title"><?= htmlspecialchars($cat['CategoryName']) ?></div>
                                    
                                    <div>
                                        <h4 class="category-submenu-column-title">Xu hướng chọn lựa</h4>
                                        <ul class="category-submenu-list">
                                            <li><a href="<?= url('trangchu/category.php?id=' . $cat['CategoryID']) ?>">Sách mới xuất bản</a></li>
                                            <li><a href="<?= url('trangchu/category.php?id=' . $cat['CategoryID']) ?>">Sách đọc nhiều nhất</a></li>
                                        </ul>
                                    </div>

                                    <div>
                                        <h4 class="category-submenu-column-title">Lọc nhanh theo giá</h4>
                                        <ul class="category-submenu-list">
                                            <li><a href="<?= url('trangchu/category.php?id=' . $cat['CategoryID'] . '&min_price=0&max_price=100000') ?>">Sách dưới 100.000 đ</a></li>
                                            <li><a href="<?= url('trangchu/category.php?id=' . $cat['CategoryID'] . '&min_price=100000&max_price=300000') ?>">Từ 100.000 đ - 300.000 đ</a></li>
                                        </ul>
                                    </div>

                                    <div class="category-submenu-banner" style="display: flex; align-items: center; justify-content: center;">
                                        <a href="<?= url('trangchu/category.php?id=' . $cat['CategoryID']) ?>">
                                            <img src="<?= asset('images/uploads/banner_menu_' . $cat['CategoryID'] . '.jpg') ?>" 
                                                 alt="Quảng cáo danh mục" 
                                                 style="width: 100%; height: auto; border-radius: var(--border-radius-sm); object-fit: cover; box-shadow: var(--box-shadow-sm);"
                                                 onerror="this.style.display='none';">
                                        </a>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li><a href="#">Hệ thống đang tải danh mục...</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <ul class="top-bar-menu">
                <li><a href="<?= url('trangchu/gioithieu.php') ?>">Giới thiệu</a></li>
                <li><a href="#">Tin tức</a></li>
                <li><a href="#">Review sách</a></li>
                <li><a href="#">Hợp tác</a></li>
                <li><a href="<?= url('cart/tracking.php') ?>">Tra cứu đơn</a></li>
            </ul>

        </div>
    </nav>