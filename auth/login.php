<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Đăng nhập';
$errors = [];

// Xử lý khi người dùng gửi form (PHP thuần — chưa nối database)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '') {
        $errors['email'] = 'Vui lòng nhập email.';
    }
    if ($password === '') {
        $errors['password'] = 'Vui lòng nhập mật khẩu.';
    }

    if ($errors === []) {
        // kiểm tra tài khoản trong database
        header('Location: ' . url('index.php'));
        exit;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<main class="container" style="max-width: 400px; margin: 2rem auto; padding: 1rem;">
    <h1>Đăng nhập</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert--error" role="alert">
            Vui lòng kiểm tra lại thông tin đăng nhập.
        </div>
    <?php endif; ?>

    <form class="form" method="post" action="">
        <div class="form-group">
            <label class="form-label form-label--required" for="email">Email</label>
            <input
                class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>"
                type="email"
                id="email"
                name="email"
                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                placeholder="you@example.com"
                required
            >
            <?php if (isset($errors['email'])): ?>
                <p class="form-error"><?= htmlspecialchars($errors['email']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label class="form-label form-label--required" for="password">Mật khẩu</label>
            <input
                class="form-control<?= isset($errors['password']) ? ' is-invalid' : '' ?>"
                type="password"
                id="password"
                name="password"
                required
            >
            <?php if (isset($errors['password'])): ?>
                <p class="form-error"><?= htmlspecialchars($errors['password']) ?></p>
            <?php endif; ?>
        </div>

        <div class="auth-actions">
            <button type="submit" class="btn btn--primary">Đăng nhập</button>
            <a class="btn btn--secondary" href="<?= url('auth/register.php') ?>">Đăng ký</a>
            <a class="btn btn--accent" href="<?= url('auth/fogetpassword.php') ?>">Quên mật khẩu</a>
        </div>
    </form>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
