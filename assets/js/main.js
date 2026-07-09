function showToast(message, type = 'success') {
    // Xóa container cũ nếu có
    const existing = document.getElementById('session-toast-container');
    if (existing) {
        existing.remove();
    }

    const toastColor = type === 'error' ? 'var(--color-error)' : (type === 'warning' ? 'var(--color-warning)' : 'var(--color-success)');
    const toastIcon = type === 'error' ? 'fa-circle-xmark' : 'fa-circle-check';
    const toastTitle = type === 'error' ? 'Thông báo hệ thống' : 'Nhật ký hoạt động';
    const toastClass = type === 'error' ? 'toast--error' : (type === 'warning' ? 'toast--warning' : 'toast--success');

    const container = document.createElement('div');
    container.id = 'session-toast-container';
    container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 10000; display: block; max-width: 380px; width: 100%;';

    container.innerHTML = `
        <div class="toast ${toastClass}" id="session-toast" style="animation: toast-in 0.3s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-left: 4px solid ${toastColor}; background: var(--color-surface); padding: 16px; border-radius: var(--border-radius); display: flex; gap: 12px; align-items: flex-start;">
            <i class="fa-solid ${toastIcon}" style="color: ${toastColor}; font-size: 1.25rem; margin-top: 2px;"></i>
            <div style="flex: 1;">
                <div style="font-weight: bold; margin-bottom: 4px; color: var(--color-text); text-align: left;">
                    ${toastTitle}
                </div>
                <div style="font-size: 0.9rem; color: var(--color-text-light); line-height: 1.4; text-align: left;">${message}</div>
            </div>
            <button onclick="document.getElementById('session-toast-container').remove()" style="background: none; border: none; font-size: 1.1rem; cursor: pointer; color: var(--color-text-light); padding: 0; line-height: 1; opacity: 0.7;">&times;</button>
        </div>
    `;

    document.body.appendChild(container);

    // Tự động tắt sau 4 giây
    setTimeout(function() {
        container.style.opacity = '0';
        container.style.transition = 'opacity 0.5s ease';
        setTimeout(function() { container.remove(); }, 500);
    }, 4000);
}

document.addEventListener('DOMContentLoaded', function() {
    // Lắng nghe sự kiện submit của mọi form trên trang
    document.addEventListener('submit', function(event) {
        const form = event.target;
        
        // Kiểm tra xem form có gửi đến trang add.php của giỏ hàng hay không
        if (form.action && form.action.includes('cart/add.php')) {
            // Chặn chuyển trang
            event.preventDefault();
            
            const formData = new FormData(form);
            
            // Gửi yêu cầu AJAX qua Fetch API
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Cập nhật số lượng giỏ hàng trên badge
                const badge = document.querySelector('.header-cart-badge');
                if (badge && typeof data.cart_count !== 'undefined') {
                    badge.textContent = data.cart_count;
                }
                
                // Hiển thị Toast thông báo
                if (data.message) {
                    showToast(data.message, data.status);
                }
            })
            .catch(error => {
                console.error('Lỗi khi thêm giỏ hàng AJAX:', error);
                // Fallback nếu có lỗi xảy ra thì submit form truyền thống
                form.submit();
            });
        }
    });
});
