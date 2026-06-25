<?php
session_start();
if (!empty($_SESSION['user_id'])) {
    header('Location: ../sangxuan/index.html'); exit;
}
define('ROOT', dirname(__DIR__));
$pageTitle = 'Đăng Nhập – BookVerse';
require_once ROOT . '/includes/header.php';
?>
<style>
body{background:var(--light,#f5f0eb)}
.auth-wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem 1rem}
.auth-card{background:#fff;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,.10);width:100%;max-width:420px;padding:2.5rem 2rem}
.auth-logo{text-align:center;margin-bottom:1.5rem}
.auth-logo b{font-size:1.8rem;color:var(--orange,#e07b39)}
.auth-logo small{display:block;color:#888;font-size:.85rem;margin-top:.2rem}
.auth-tabs{display:flex;border-bottom:2px solid #eee;margin-bottom:1.5rem}
.auth-tab{flex:1;background:none;border:none;padding:.65rem;font-size:.95rem;font-weight:600;color:#999;cursor:pointer;position:relative;transition:color .2s}
.auth-tab.active{color:var(--orange,#e07b39)}
.auth-tab.active::after{content:'';position:absolute;bottom:-2px;left:0;right:0;height:2px;background:var(--orange,#e07b39);border-radius:2px}
.form-label{font-weight:500;font-size:.9rem}
.form-control{border-radius:8px;border:1.5px solid #ddd;padding:.6rem .9rem;font-size:.95rem;width:100%;box-sizing:border-box;transition:border-color .2s}
.form-control:focus{border-color:var(--orange,#e07b39);box-shadow:0 0 0 3px rgba(224,123,57,.15);outline:none}
.btn-auth{width:100%;padding:.7rem;border:none;border-radius:8px;background:var(--orange,#e07b39);color:#fff;font-weight:700;font-size:1rem;cursor:pointer;margin-top:.75rem;transition:opacity .2s}
.btn-auth:hover{opacity:.88}.btn-auth:disabled{opacity:.6;cursor:not-allowed}
.foot-link{text-align:center;margin-top:1.2rem;font-size:.88rem;color:#666}
.foot-link a,.forgot-link{color:var(--orange,#e07b39);text-decoration:none;font-weight:600}
.foot-link a:hover,.forgot-link:hover{text-decoration:underline}
.forgot-link{display:block;text-align:right;font-size:.83rem;margin-top:.3rem}
.mb-3{margin-bottom:1rem}.mb-1{margin-bottom:.25rem}
.alert-msg{padding:.6rem .9rem;border-radius:8px;font-size:.88rem;margin-bottom:1rem;display:none}
.alert-msg.error{background:#ffeaea;color:#c0392b;border:1px solid #f5c6c6}
.alert-msg.success{background:#eafbea;color:#1a7a2e;border:1px solid #b2e5b2}
</style>

<div class="auth-wrap">
  <div class="auth-card">
    <div class="auth-logo">
      <b>📚 BookVerse</b>
      <small>Đọc sách — mở rộng thế giới</small>
    </div>

    <div class="auth-tabs">
      <button class="auth-tab active" id="tab-login"    onclick="switchTab('login')">Đăng nhập</button>
      <button class="auth-tab"        id="tab-register" onclick="switchTab('register')">Đăng ký</button>
    </div>

    <div id="alert-msg" class="alert-msg"></div>

    <!-- Đăng nhập -->
    <div id="login-form">
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" id="login-email" class="form-control" placeholder="example@email.com">
      </div>
      <div class="mb-1">
        <label class="form-label">Mật khẩu</label>
        <input type="password" id="login-password" class="form-control" placeholder="••••••••">
        <a href="fogetpassword.php" class="forgot-link">Quên mật khẩu?</a>
      </div>
      <button class="btn-auth" id="login-btn" onclick="doLogin()">Đăng Nhập</button>
      <div class="foot-link">Chưa có tài khoản? <a href="#" onclick="switchTab('register');return false">Đăng ký ngay</a></div>
    </div>

    <!-- Đăng ký -->
    <div id="register-form" style="display:none">
      <div class="mb-3">
        <label class="form-label">Họ và tên</label>
        <input type="text" id="reg-name" class="form-control" placeholder="Nguyễn Văn A">
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" id="reg-email" class="form-control" placeholder="example@email.com">
      </div>
      <div class="mb-3">
        <label class="form-label">Số điện thoại</label>
        <input type="tel" id="reg-phone" class="form-control" placeholder="0909 123 456">
      </div>
      <div class="mb-3">
        <label class="form-label">Mật khẩu</label>
        <input type="password" id="reg-password" class="form-control" placeholder="Tối thiểu 6 ký tự">
      </div>
      <button class="btn-auth" id="register-btn" onclick="doRegister()">Tạo Tài Khoản</button>
      <div class="foot-link">Đã có tài khoản? <a href="#" onclick="switchTab('login');return false">Đăng nhập</a></div>
    </div>
  </div>
</div>

<script>
// API base — router dùng PATH_INFO: api/index.php/auth/login
const API = '../sangxuan/api/index.php';

function switchTab(tab) {
  document.getElementById('login-form').style.display    = tab==='login'    ? '' : 'none';
  document.getElementById('register-form').style.display = tab==='register' ? '' : 'none';
  document.getElementById('tab-login').classList.toggle('active',    tab==='login');
  document.getElementById('tab-register').classList.toggle('active', tab==='register');
  hideAlert();
}

function showAlert(msg, type='error') {
  const el = document.getElementById('alert-msg');
  el.textContent = msg; el.className = 'alert-msg '+type; el.style.display = 'block';
}
function hideAlert() { document.getElementById('alert-msg').style.display = 'none'; }

async function callApi(method, route, body=null) {
  const res = await fetch(API + route, {
    method,
    credentials: 'include',
    headers: { 'Content-Type': 'application/json' },
    body: body ? JSON.stringify(body) : undefined,
  });
  const json = await res.json();
  if (!res.ok) throw new Error(json.message || 'Lỗi server');
  return json;
}

async function doLogin() {
  const btn = document.getElementById('login-btn');
  btn.disabled = true; btn.textContent = 'Đang đăng nhập...'; hideAlert();
  try {
    const r = await callApi('POST', '/auth/login', {
      email:    document.getElementById('login-email').value,
      password: document.getElementById('login-password').value,
    });
    if (r.success) {
      showAlert('Xin chào, ' + r.data.name + '! Đang chuyển trang...', 'success');
      setTimeout(() => { window.location.href = '../sangxuan/index.html'; }, 900);
    }
  } catch(e) {
    showAlert(e.message || 'Đăng nhập thất bại');
    btn.disabled = false; btn.textContent = 'Đăng Nhập';
  }
}

async function doRegister() {
  const btn = document.getElementById('register-btn');
  btn.disabled = true; btn.textContent = 'Đang xử lý...'; hideAlert();
  try {
    const r = await callApi('POST', '/auth/register', {
      full_name: document.getElementById('reg-name').value,
      email:     document.getElementById('reg-email').value,
      phone:     document.getElementById('reg-phone').value,
      password:  document.getElementById('reg-password').value,
    });
    if (r.success) {
      showAlert('Đăng ký thành công! Vui lòng đăng nhập.', 'success');
      switchTab('login');
    }
  } catch(e) {
    showAlert(e.message || 'Đăng ký thất bại');
  } finally {
    btn.disabled = false; btn.textContent = 'Tạo Tài Khoản';
  }
}

document.addEventListener('keydown', e => {
  if (e.key !== 'Enter') return;
  document.getElementById('register-form').style.display === 'none' ? doLogin() : doRegister();
});
</script>

<?php require_once ROOT . '/includes/footer.php'; ?>
