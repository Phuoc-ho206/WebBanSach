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

<?php if (isset($_SESSION['log_toast'])): 
    $toastMsg = $_SESSION['log_toast'];
    $toastClass = 'toast--success';
    $toastColor = 'var(--color-success)';
    $toastIcon = 'fa-circle-check';
    
    // Kiểm tra các từ khóa mang tính chất tiêu cực/cảnh báo/lỗi
    if (preg_match('/(thất bại|hủy|lỗi|xóa|vượt quá|giới hạn|hết hàng|cảnh báo)/i', $toastMsg)) {
        $toastClass = 'toast--error';
        $toastColor = 'var(--color-error)';
        $toastIcon = 'fa-circle-xmark';
    }
?>
    <div class="toast-container" id="session-toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 10000; display: block; max-width: 380px; width: 100%;">
        <div class="toast <?= $toastClass ?>" id="session-toast" style="animation: toast-in 0.3s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-left: 4px solid <?= $toastColor ?>; background: var(--color-surface); padding: 16px; border-radius: var(--border-radius); display: flex; gap: 12px; align-items: flex-start;">
            <i class="fa-solid <?= $toastIcon ?>" style="color: <?= $toastColor ?>; font-size: 1.25rem; margin-top: 2px;"></i>
            <div style="flex: 1;">
                <div style="font-weight: bold; margin-bottom: 4px; color: var(--color-text); text-align: left;">
                    <?= ($toastClass === 'toast--error') ? 'Thông báo hệ thống' : 'Nhật ký hoạt động' ?>
                </div>
                <div style="font-size: 0.9rem; color: var(--color-text-light); line-height: 1.4; text-align: left;"><?= htmlspecialchars($toastMsg) ?></div>
            </div>
            <button onclick="document.getElementById('session-toast-container').remove()" style="background: none; border: none; font-size: 1.1rem; cursor: pointer; color: var(--color-text-light); padding: 0; line-height: 1; opacity: 0.7;">&times;</button>
        </div>
    </div>
    <script>
        setTimeout(function() {
            var el = document.getElementById('session-toast-container');
            if (el) {
                el.style.opacity = '0';
                el.style.transition = 'opacity 0.5s ease';
                setTimeout(function() { el.remove(); }, 500);
            }
        }, 4000);
    </script>
    <?php unset($_SESSION['log_toast']); ?>
<?php endif; ?>
<script src="/WebBanSach/assets/js/main.js?v=<?= time() ?>"></script>