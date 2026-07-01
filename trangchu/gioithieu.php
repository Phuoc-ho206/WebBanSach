<?php
require_once '../config/db.php';

$pageTitle = 'Về chúng tôi - WebBanSach';
include '../includes/header.php';
?>

<style>
    /* ==========================================================================
       1. THANH ĐIỀU HƯỚNG (BREADCRUMB) NỀN XÁM
       ========================================================================== */
    .breadcrumb-bar {
        background-color: #f5f5f5;
        padding: 12px var(--spacing-md);
        border-bottom: 1px solid #e0e0e0;
    }
    .breadcrumb-container {
        max-width: 1200px;
        margin: 0 auto;
        font-size: 0.9rem;
        color: #666;
    }
    .breadcrumb-container a {
        color: #333;
        text-decoration: none;
        transition: color 0.2s;
    }
    .breadcrumb-container a:hover {
        color: #d95300;
    }
    .breadcrumb-container span {
        color: #d95300;
    }

    /* ==========================================================================
       2. BỐ CỤC BÀI VIẾT CHÍNH
       ========================================================================== */
    .about-wrapper {
        max-width: 900px;
        margin: 30px auto 60px auto;
        padding: 0 var(--spacing-md);
        font-family: Arial, Helvetica, sans-serif;
        color: #111; /* Đen đậm chuẩn báo chí */
    }

    .about-title {
        font-size: 2.2rem;
        font-weight: 700;
        margin: 0 0 5px 0;
        color: #222;
    }

    .about-date {
        font-size: 0.9rem;
        color: #888;
        margin-bottom: 40px;
    }

    /* Khối Slogan nổi bật giữa trang */
    .about-slogan-box {
        text-align: center;
        margin-bottom: 30px;
    }
    .about-slogan-box h2 {
        color: #e67e22; /* Màu cam Alpha Books */
        font-size: 1.3rem;
        font-weight: bold;
        text-transform: uppercase;
        margin: 0 0 5px 0;
        letter-spacing: 0.5px;
    }
    .about-slogan-box p {
        font-style: italic;
        color: #444;
        font-size: 1.05rem;
        margin: 0;
    }

    /* Nội dung văn bản */
    .about-text p {
        font-size: 1.05rem;
        line-height: 1.7;
        text-align: justify;
        margin-bottom: 18px;
    }

    /* ==========================================================================
       3. KHỐI "CÁC DÒNG SẢN PHẨM" MÔ PHỎNG ALPHA BOOKS
       ========================================================================== */
    .product-lines-section {
        margin-top: 60px;
        border-top: 1px solid #e0e0e0;
        padding-top: 40px;
        position: relative;
    }
    
    /* Cụm Tabs góc phải */
    .tabs-top-right {
        position: absolute;
        top: -15px;
        right: 0;
        display: flex;
    }
    .tab-item {
        padding: 6px 15px;
        color: white;
        font-size: 0.85rem;
        font-weight: bold;
    }
    .tab-item.gray { background-color: #a0a0a0; }
    .tab-item.red { background-color: #e74c3c; }

    .product-lines-title {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 40px;
        color: #222;
    }

    /* Lưới sơ đồ sản phẩm giống ảnh mẫu */
    .pl-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
    }

    .pl-box {
        position: relative;
        padding-left: 20px;
        border-left: 1px solid #ff9f89; /* Đường kẻ chỉ màu đỏ nhạt */
    }

    /* Điểm chấm tròn trên đường kẻ */
    .pl-box::before {
        content: '';
        position: absolute;
        left: -4px;
        top: 10px;
        width: 7px;
        height: 7px;
        background-color: #e74c3c;
        border-radius: 50%;
    }

    .pl-box-title {
        color: #e74c3c;
        font-size: 1.2rem;
        font-weight: bold;
        margin: 0 0 15px 0;
        text-align: center;
    }

    .pl-images {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 15px;
    }

    .pl-images img {
        width: 80px;
        height: 120px;
        object-fit: cover;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .pl-desc {
        text-align: center;
        font-size: 0.95rem;
        color: #333;
        line-height: 1.5;
        padding: 0 20px;
    }
</style>

<div class="breadcrumb-bar">
    <div class="breadcrumb-container">
        <a href="<?= url('trangchu/index.php') ?>">Trang chủ</a> &rsaquo; <span>Về chúng tôi</span>
    </div>
</div>

<main class="about-wrapper">
    
    <h1 class="about-title">Về Chúng Tôi</h1>
    <div class="about-date">28/11/2023</div> <div class="about-slogan-box">
        <h2>WEBBANSACH - HÀNH TRÌNH VƯƠN TẦM TRI THỨC</h2>
        <p>Better Knowledge, Better Success</p>
    </div>

    <div class="about-text">
        <p><strong>WebBanSach</strong> được biết đến là một trong những thương hiệu hàng đầu về dòng sách quản trị kinh doanh, phát triển kỹ năng, tài chính, đầu tư... với các cuốn sách hướng dẫn khởi nghiệp, các bài học, phương pháp và kinh nghiệm quản trị của các chuyên gia, và các tập đoàn nổi tiếng trên thế giới. Sau nhiều năm hình thành và phát triển, hệ thống đã từng bước khẳng định tên tuổi của mình, đặc biệt đối với các thế hệ doanh nhân, nhà quản lý và những người trẻ luôn khát khao xây dựng sự nghiệp thành công.</p>
        
        <p>Từ một dự án nhỏ, <strong>WebBanSach</strong> hiện đã phát triển mở rộng thành thương hiệu phân phối sách quy mô lớn. Không chỉ tập trung vào các dòng sách quản trị kinh doanh & kỹ năng, chúng tôi còn mở rộng sang các mảng tinh hoa văn hóa, lịch sử, khoa học công nghệ, sách thiếu nhi và văn học kinh điển.</p>

        <p>Bên cạnh việc sở hữu hàng ngàn đầu sách chọn lọc, WebBanSach còn thành công tiếp cận độc giả với các ấn phẩm in đậm dấu ấn trong tâm trí người đọc, điển hình như: <em>Đắc Nhân Tâm, Cha Giàu Cha Nghèo, Tư Duy Nhanh Và Chậm, Tiểu sử Steve Jobs, Chiến Lược Đại Dương Xanh, Nhà Giả Kim...</em> Chúng tôi luôn nỗ lực mang đến mức giá tốt nhất, dịch vụ giao hàng nhanh chóng và trải nghiệm mua sắm trọn vẹn nhất cho mọi khách hàng.</p>
    </div>

    <div class="product-lines-section">
        <div class="tabs-top-right">
            <div class="tab-item gray">SẢN PHẨM</div>
            <div class="tab-item red">ĐỐI TÁC</div>
        </div>

        <h2 class="product-lines-title">Các Dòng Sản Phẩm</h2>

        <div class="pl-grid">
            <div class="pl-box">
                <h3 class="pl-box-title">WebBanSach Business</h3>
                <div class="pl-images">
                    <img src="https://placehold.co/100x150/e0e0e0/666?text=Business+1" alt="Book">
                    <img src="https://placehold.co/100x150/e0e0e0/666?text=Business+2" alt="Book">
                    <img src="https://placehold.co/100x150/e0e0e0/666?text=Business+3" alt="Book">
                </div>
                <p class="pl-desc">Dòng sách quản trị, kinh doanh, chiến lược dành cho doanh nghiệp và lãnh đạo.</p>
            </div>

            <div class="pl-box">
                <h3 class="pl-box-title">WebBanSach Skill</h3>
                <div class="pl-images">
                    <img src="https://placehold.co/100x150/e0e0e0/666?text=Skill+1" alt="Book">
                    <img src="https://placehold.co/100x150/e0e0e0/666?text=Skill+2" alt="Book">
                    <img src="https://placehold.co/100x150/e0e0e0/666?text=Skill+3" alt="Book">
                </div>
                <p class="pl-desc">Dòng sách tư duy tâm lý, phát triển kỹ năng bản thân và nghệ thuật sống.</p>
            </div>
            
            <div class="pl-box">
                <h3 class="pl-box-title">Văn Học & Kinh Điển</h3>
                <div class="pl-images">
                    <img src="https://placehold.co/100x150/e0e0e0/666?text=Novel+1" alt="Book">
                    <img src="https://placehold.co/100x150/e0e0e0/666?text=Novel+2" alt="Book">
                    <img src="https://placehold.co/100x150/e0e0e0/666?text=Novel+3" alt="Book">
                </div>
                <p class="pl-desc">Ấn phẩm tiểu thuyết, truyện ngắn, tản văn và các tác phẩm kinh điển mọi thời đại.</p>
            </div>
        </div>
    </div>

</main>

<?php include '../includes/footer.php'; ?>