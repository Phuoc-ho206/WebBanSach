<?php
session_start();
if (!empty($_SESSION['user_id'])) {
    header('Location: ../sangxuan/index.html'); exit;
}
define('ROOT', dirname(__DIR__));
$pageTitle = 'Quên Mật Khẩu – BookVerse';
require_once ROOT . '/includes/header.php';
?>
<style>
body{background:var(--light,#f5f0eb)}
.auth-wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem 1rem}
.auth-card{background:#fff;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,.10);width:100%;max-width:420px;padding:2.5rem 2rem}
.auth-logo{text-align:center;margin-bottom:1.5rem}
.auth-logo b{font-size:1.8rem;color:var(--orange,#e07b39)}
.auth-logo small{display:block;color:#888;font-size:.85rem;margin-top:.2rem}
.auth-title{font-size:1.2rem;font-weight:700;margin-bottom:.4rem;color:#222}
.auth-desc{color:#666;font-size:.9rem;margin-bottom:1.5rem}
.form-label{font-weight:500;font-size:.9rem}
.form-control{border-radius:8px;border:1.5px solid #ddd;padding:.6rem .9rem;font-size:.95rem;width:100%;box-sizing:border-box;transition:border-color .2s}
.form-control:focus{border-color:var(--orange,#e07b39);box-shadow:0 0 0 3px rgba(224,123,57,.15);outline:none}
.btn-auth{width:100%;padding:.7rem;border:none;border-radius:8px;background:var(--orange,#e07b39);color:#fff;font-weight:700;font-size:1rem;cursor:pointer;margin-top:.75rem;transition:opacity .2s}
.btn-auth:hover{opacity:.88}.btn-auth:disabled{opacity:.6;cursor:not-allowed}
.btn-back{width:100%;padding:.65rem;border:1.5px solid #ddd;border-radius:8px;background:#fff;color:#555;font-weight:600;font-size:.95rem;cursor:pointer;margin-top:.6rem;transition:background .2s}
.btn-back:hover{background:#f5f5f5}
.mb-3{margin-bottom:1rem}
.alert-msg{padding:.6rem .9rem;border-radius:8px;font-size:.88rem;margin-bottom:1rem;display:none}
.alert-msg.error{background:#ffeaea;color:#c0392b;border:1px solid #f5c6c6}
.alert-msg.success{background:#eafbea;color:#1a7a2e;border:1px solid #b2e5b2}
.step{display:none}.step.active{display:block}
.otp-inputs{display:flex;gap:.5rem;justify-content:center;margin:1rem 0}
.otp-input{width:48px;height:52px;text-align:center;font-size:1.4rem;font-weight:700;border:1.5px solid #ddd;border-radius:8px;outline:none;transition:border-color .2s}
.otp-input:focus{border-color:var(--orange,#e07b39);box-shadow:0 0 0 3px rgba(224,123,57,.15)}
</style>

<div class="auth-wrap">
  <div class="auth-card">
    <div class="auth-logo">
      <b>📚 BookVerse</b>
      <small>Đọc sách — mở rộng thế giới</small>
    </div>

    <div id="alert-msg" class="alert-msg"></div>

    <!-- Bước 1: Nhập email -->
    <div class="step active" id="step-1">
      <div class="auth-title">Quên mật khẩu?</div>
      <div class="auth-desc">Nhập email đã đăng ký, chúng tôi sẽ gửi mã xác nhận để đặt lại mật khẩu.</div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" id="fp-email" class="form-control" placeholder="example@email.com">
      </div>
      <button class="btn-auth" id="send-otp-btn" onclick="sendOtp()">Gửi Mã Xác Nhận</button>
      <button class="btn-back" onclick="window.location.href='login.php'">← Quay lại đăng nhập</button>
    </div>

    <!-- Bước 2: Nhập OTP -->
    <div class="step" id="step-2">
      <div class="auth-title">Nhập mã xác nhận</div>
      <div class="auth-desc">Mã 6 chữ số đã được gửi đến <b id="show-email"></b></div>
      <div class="otp-inputs">
        <input class="otp-input" id="otp-0" maxlength="1" type="text" inputmode="numeric">
        <input class="otp-input" id="otp-1" maxlength="1" type="text" inputmode="numeric">
        <input class="otp-input" id="otp-2" maxlength="1" type="text" inputmode="numeric">
        <input class="otp-input" id="otp-3" maxlength="1" type="text" inputmode="numeric">
        <input class="otp-input" id="otp-4" maxlength="1" type="text" inputmode="numeric">
        <input class="otp-input" id="otp-5" maxlength="1" type="text" inputmode="numeric">
      </div>
      <button class="btn-auth" id="verify-otp-btn" onclick="verifyOtp()">Xác Nhận</button>
      <button class="btn-back" onclick="goStep(1)">← Nhập lại email</button>
    </div>

    <!-- Bước 3: Đặt mật khẩu mới -->
    <div class="step" id="step-3">
      <div class="auth-title">Đặt mật khẩu mới</div>
      <div class="auth-desc">Mật khẩu mới phải có ít nhất 6 ký tự.</div>
      <div class="mb-3">
        <label class="form-label">Mật khẩu mới</label>
        <input type="password" id="new-password" class="form-control" placeholder="••••••••">
      </div>
      <div class="mb-3">
        <label class="form-label">Xác nhận mật khẩu</label>
        <input type="password" id="confirm-password" class="form-control" placeholder="••••••••">
      </div>
      <button class="btn-auth" id="reset-btn" onclick="resetPassword()">Đặt Lại Mật Khẩu</button>
    </div>

    <!-- Bước 4: Thành công -->
    <div class="step" id="step-4" style="text-align:center;padding:1rem 0">
      <div style="font-size:3rem;margin-bottom:1rem">✅</div>
      <div class="auth-title">Đặt lại thành công!</div>
      <div class="auth-desc">Mật khẩu của bạn đã được cập nhật.</div>
      <button class="btn-auth" onclick="window.location.href='login.php'">Đăng nhập ngay</button>
    </div>

  </div>
</div>

<script>
const API = '../sangxuan/api/index.php';
let _token = ''; // token trả về sau khi xác nhận OTP

function goStep(n) {
  document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
  document.getElementById('step-' + n).classList.add('active');
  hideAlert();
}

function showAlert(msg, type='error') {
  const el = document.getElementById('alert-msg');
  el.textContent = msg; el.className = 'alert-msg '+type; el.style.display = 'block';
}
function hideAlert() { document.getElementById('alert-msg').style.display = 'none'; }

async function callApi(method, route, body=null) {
  const res = await fetch(API + route, {
    method, credentials: 'include',
    headers: { 'Content-Type': 'application/json' },
    body: body ? JSON.stringify(body) : undefined,
  });
  const json = await res.json();
  if (!res.ok) throw new Error(json.message || 'Lỗi server');
  return json;
}

async function sendOtp() {
  const email = document.getElementById('fp-email').value.trim();
  if (!email) { showAlert('Vui lòng nhập email'); return; }
  const btn = document.getElementById('send-otp-btn');
  btn.disabled = true; btn.textContent = 'Đang gửi...'; hideAlert();
  try {
    const r = await callApi('POST', '/auth/forgot-password', { email });
    if (r.success) {
      document.getElementById('show-email').textContent = email;
      goStep(2);
      initOtpInputs();
    }
  } catch(e) {
    showAlert(e.message || 'Không tìm thấy email này');
  } finally {
    btn.disabled = false; btn.textContent = 'Gửi Mã Xác Nhận';
  }
}

async function verifyOtp() {
  const otp = [0,1,2,3,4,5].map(i => document.getElementById('otp-'+i).value).join('');
  if (otp.length < 6) { showAlert('Vui lòng nhập đủ 6 chữ số'); return; }
  const btn = document.getElementById('verify-otp-btn');
  btn.disabled = true; btn.textContent = 'Đang xác nhận...'; hideAlert();
  try {
    const email = document.getElementById('fp-email').value.trim();
    const r = await callApi('POST', '/auth/verify-otp', { email, otp });
    if (r.success) {
      _token = r.data.token || '';
      goStep(3);
    }
  } catch(e) {
    showAlert(e.message || 'Mã xác nhận không đúng hoặc đã hết hạn');
  } finally {
    btn.disabled = false; btn.textContent = 'Xác Nhận';
  }
}

async function resetPassword() {
  const pw  = document.getElementById('new-password').value;
  const pw2 = document.getElementById('confirm-password').value;
  if (pw.length < 6)  { showAlert('Mật khẩu phải có ít nhất 6 ký tự'); return; }
  if (pw !== pw2)     { showAlert('Mật khẩu xác nhận không khớp'); return; }
  const btn = document.getElementById('reset-btn');
  btn.disabled = true; btn.textContent = 'Đang cập nhật...'; hideAlert();
  try {
    const email = document.getElementById('fp-email').value.trim();
    const r = await callApi('POST', '/auth/reset-password', { email, token: _token, password: pw });
    if (r.success) goStep(4);
  } catch(e) {
    showAlert(e.message || 'Đặt lại mật khẩu thất bại');
  } finally {
    btn.disabled = false; btn.textContent = 'Đặt Lại Mật Khẩu';
  }
}

// Auto-focus OTP inputs
function initOtpInputs() {
  for (let i = 0; i < 6; i++) {
    const el = document.getElementById('otp-' + i);
    el.value = '';
    el.oninput = () => {
      el.value = el.value.replace(/\D/g, '').slice(-1);
      if (el.value && i < 5) document.getElementById('otp-' + (i+1)).focus();
    };
    el.onkeydown = e => {
      if (e.key === 'Backspace' && !el.value && i > 0)
        document.getElementById('otp-' + (i-1)).focus();
    };
  }
  document.getElementById('otp-0').focus();
}
</script>

<?php require_once ROOT . '/includes/footer.php'; ?>
