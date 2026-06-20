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
    
    <!-- Hệ thống Biến màu sắc chung -->
    <link rel="stylesheet" href="/WebBanSach/assets/css/variables.css">

    <!-- Hệ thống Layout Component -->
    <link rel="stylesheet" href="<?= asset('css/components/navbar.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">

    <!-- Khối Primitives UI rút từ hệ thống -->
    <link rel="stylesheet" href="<?= asset('css/components/button.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/form.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/card.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/badge.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/alert_toast.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/modal.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/pagination.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/table.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/spinner.css') ?>">

    <?php if (isset($extraCss)): ?>
        <?php foreach ((array) $extraCss as $css): ?>
            <link rel="stylesheet" href="<?= asset($css) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>

    <!-- ====================================================================
         1. HEADER CHÍNH: LOGO, TÌM KIẾM, NÚT CHỨC NĂNG (MÀU TRẮNG)
         ==================================================================== -->
    <header class="header-top">
        <div class="header-top-container">
            
            <!-- LOGO DẠNG TEXT ĐƠN GIẢN -->
            <a href="<?= url('trangchu/index.php') ?>" class="header-logo" style="text-decoration: none;">
                <span style="color: var(--color-primary); font-size: 2.2rem; font-weight: 900; letter-spacing: -0.5px; font-family: Georgia, serif;">📚 WebBanSach</span>
            </a>

            <!-- THANH TÌM KIẾM TRUNG TÂM -->
            <form action="<?= url('trangchu/search.php') ?>" method="GET" class="header-search">
                <input type="text" name="keyword" placeholder="Tìm kiếm tên sách, tác giả, nhà xuất bản..." required>
                <button type="submit" aria-label="Tìm kiếm">
                    <!-- Icon Kính lúp -->
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </button>
            </form>

            <!-- CỤM NÚT CHỨC NĂNG BÊN PHẢI -->
            <div class="header-actions">
                
                <!-- Gọi đặt hàng -->
                <div class="header-action-item">
                    <div class="header-action-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    </div>
                    <div class="header-action-text">
                        <span>Gọi đặt hàng</span>
                        <strong>0932329959</strong>
                    </div>
                </div>

                <!-- Tài khoản / Đăng nhập -->
                <a href="<?= url('auth/pages/login.php') ?>" class="header-action-item">
                    <div class="header-action-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </div>
                    <div class="header-action-text">
                        <?php if(isset($_SESSION['user']) || isset($_SESSION['profile'])): ?>
                            <span>Tài khoản</span>
                            <strong>Của tôi</strong>
                        <?php else: ?>
                            <span style="opacity: 0; height: 0;">&nbsp;</span>
                            <strong style="margin-top: 8px;">Đăng nhập</strong>
                        <?php endif; ?>
                    </div>
                </a>

                <!-- Giỏ hàng -->
                <a href="<?= url('cart/history.php') ?>" class="header-action-item">
                    <div class="header-action-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                        <span class="header-cart-badge">0</span> <!-- Bạn có thể code PHP đếm số item trong session giỏ hàng ở đây -->
                    </div>
                    <div class="header-action-text">
                        <span style="opacity: 0; height: 0;">&nbsp;</span>
                        <strong style="margin-top: 8px;">Giỏ hàng</strong>
                    </div>
                </a>

            </div>
        </div>
    </header>


    <!-- ====================================================================
         2. THANH NAVBAR PHỤ: MEGA MENU & LIÊN KẾT (MÀU CAM)
         ==================================================================== -->
    <div class="orange-bar">
        <div class="orange-bar-container">
            
            <!-- NÚT DANH MỤC: Tự động bung ra nếu là Trang chủ ($isHomepage) -->
            <div class="category-dropdown-wrapper <?= (isset($isHomepage) && $isHomepage) ? 'always-open' : '' ?>">
                <div class="category-dropdown-header">
                    ☰ TẤT CẢ DANH MỤC
                </div>
                
                <ul class="category-sidebar-list">
                    <?php if (!empty($global_categories)): ?>
                        <?php foreach ($global_categories as $cat): 
                            $catName = $cat['CategoryName'];
                            
                            // Phân loại mảng dữ liệu MEGA MENU tự động theo tên
                            $subTopics = []; $authors = [];
                            if (stripos($catName, 'Kinh tế') !== false || stripos($catName, 'Tài chính') !== false) {
                                $subTopics = ['Quản trị lãnh đạo', 'Marketing - Bán hàng', 'Tài chính cá nhân', 'Khởi nghiệp'];
                                $authors = ['Robert Kiyosaki', 'Napoleon Hill', 'Philip Kotler', 'Jim Collins'];
                            } elseif (stripos($catName, 'Kỹ năng') !== false || stripos($catName, 'Sống') !== false || stripos($catName, 'Tâm lý') !== false) {
                                $subTopics = ['Kỹ năng giao tiếp', 'Tâm lý học hành vi', 'Quản lý thời gian', 'Nghệ thuật sống'];
                                $authors = ['Dale Carnegie', 'Daniel Goleman', 'Thích Nhất Hạnh', 'Robin Sharma'];
                            } elseif (stripos($catName, 'Thiếu nhi') !== false) {
                                $subTopics = ['Truyện tranh', 'Phát triển trí tuệ', 'Sách bách khoa', 'Kỹ năng cho bé'];
                                $authors = ['Nguyễn Nhật Ánh', 'Fujiko F. Fujio', 'Roald Dahl', 'Tô Hoài'];
                            } elseif (stripos($catName, 'Văn học') !== false) {
                                $subTopics = ['Tiểu thuyết trinh thám', 'Văn học kinh điển', 'Truyện ngắn', 'Tản văn'];
                                $authors = ['Higashino Keigo', 'Marc Levy', 'Vũ Trọng Phụng', 'Nam Cao'];
                            } else {
                                $subTopics = ['Sách nổi bật', 'Sách bán chạy', 'Sách mới xuất bản', 'Sách giảm giá'];
                                $authors = ['Nhiều tác giả', 'Tác giả trẻ', 'Tác giả kinh điển'];
                            }
                        ?>
                            <li>
                                <a href="<?= url('trangchu/category.php?id=' . $cat['CategoryID']) ?>">
                                    <?= htmlspecialchars($catName) ?>
                                    <span style="font-weight: 300;">›</span>
                                </a>
                                
                                <!-- BẢNG MEGA MENU CHI TIẾT (Trỏ thẳng vào search.php) -->
                                <div class="category-submenu">
                                    <div class="category-submenu-title"><?= htmlspecialchars($catName) ?></div>
                                    
                                    <div>
                                        <h4 class="category-submenu-column-title">Theo chủ đề</h4>
                                        <ul class="category-submenu-list">
                                            <?php foreach($subTopics as $topic): ?>
                                                <li><a href="<?= url('trangchu/search.php?keyword=' . urlencode($topic)) ?>"><?= htmlspecialchars($topic) ?></a></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>

                                    <div>
                                        <h4 class="category-submenu-column-title">Tác giả nổi bật</h4>
                                        <ul class="category-submenu-list">
                                            <?php foreach($authors as $author): ?>
                                                <li><a href="<?= url('trangchu/search.php?keyword=' . urlencode($author)) ?>"><?= htmlspecialchars($author) ?></a></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li><a href="#">Hệ thống đang tải danh mục...</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- MENU LIÊN KẾT PHỤ BÊN PHẢI -->
            <ul class="top-bar-menu">
                <li><a href="#">Giới thiệu</a></li>
                <li><a href="#">Tin tức</a></li>
                <li><a href="#">Review sách</a></li>
                <li><a href="#">Hợp tác</a></li>
                <li><a href="<?= url('cart/tracking.php') ?>">Tra cứu đơn</a></li>
            </ul>

        </div>
    </div>

    <script>
        function toggleMobileMenu() {
            document.querySelector('.navbar').classList.toggle('is-open');
        }
    </script>