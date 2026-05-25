<?php
/** @var string $pageTitle Tiêu đề trang  */
?>
<footer class="footer">
    <div class="footer__grid">

        <!-- Cột 1: Thương hiệu -->
        <div>
            <div class="footer__brand">Web bán sách</div>
            <p class="footer__desc">Nơi mang đến những cuốn sách hay nhất với giá tốt nhất. Đọc sách — mở rộng thế giới.
            </p>
            <div class="footer__social">
                <a href="#" class="footer__social-link" aria-label="Facebook">f</a>
                <a href="#" class="footer__social-link" aria-label="Zalo">Z</a>
                <a href="#" class="footer__social-link" aria-label="Youtube">▶</a>
            </div>
        </div>

        <!-- Cột 2: Danh mục -->
        <div>
            <div class="footer__title">Danh mục</div>
            <ul class="footer__links">
                <li><a href="#" class="footer__link">Văn học</a></li>
                <li><a href="#" class="footer__link">Kinh tế</a></li>
                <li><a href="#" class="footer__link">Kỹ năng sống</a></li>
                <li><a href="#" class="footer__link">Thiếu nhi</a></li>
                <li><a href="#" class="footer__link">Khoa học</a></li>
            </ul>
        </div>

        <!-- Cột 3: Hỗ trợ -->
        <div>
            <div class="footer__title">Hỗ trợ</div>
            <ul class="footer__links">
                <li><a href="#" class="footer__link">Hướng dẫn mua hàng</a></li>
                <li><a href="#" class="footer__link">Chính sách đổi trả</a></li>
                <li><a href="#" class="footer__link">Chính sách bảo mật</a></li>
                <li><a href="#" class="footer__link">Câu hỏi thường gặp</a></li>
            </ul>
        </div>

        <!-- Cột 4: Liên hệ -->
        <div>
            <div class="footer__title">Liên hệ</div>
            <ul class="footer__links">
                <li><span class="footer__link">📍 123 Nguyễn Văn A, TP.HCM</span></li>
                <li><span class="footer__link">📞 0909 123 456</span></li>
                <li><span class="footer__link">✉️ hello@webansach.vn</span></li>
            </ul>
        </div>

    </div>

    <div class="footer__bottom">
        <span>© <?= date('Y') ?> WebBanSach. All rights reserved.</span>
        <span>Thiết kế bởi nhóm WebBanSach</span>
    </div>
</footer>