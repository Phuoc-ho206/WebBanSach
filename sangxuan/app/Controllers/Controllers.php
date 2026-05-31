<?php

require_once __DIR__ . '/../Core/BaseController.php';
require_once __DIR__ . '/../Models/BookModel.php';
require_once __DIR__ . '/../Models/Models.php';

/**
 * BookController - Single Responsibility: xử lý request về Books
 */
class BookController extends BaseController {
    private BookModel $bookModel;

    public function __construct() {
        $this->bookModel = new BookModel();
    }

    public function index(): void {
        $limit    = (int)($_GET['limit']   ?? 20);
        $offset   = (int)($_GET['offset']  ?? 0);
        $category = (int)($_GET['category'] ?? 0);

        $books = $category > 0
            ? $this->bookModel->findByCategory($category, $limit, $offset)
            : $this->bookModel->findAllWithDetails($limit, $offset);

        $this->json(['books' => $books, 'total' => $this->bookModel->count()]);
    }

    public function show(int $id): void {
        $book = $this->bookModel->findById($id);
        if (!$book) $this->jsonError('Book not found', 404);
        $this->json($book);
    }

    public function search(): void {
        $kw    = trim($_GET['q'] ?? '');
        $books = $kw ? $this->bookModel->search($kw) : [];
        $this->json(['books' => $books]);
    }

    public function bestSellers(): void {
        $this->json($this->bookModel->findBestSellers(8));
    }

    public function promotions(): void {
        $this->json($this->bookModel->findWithPromotion());
    }
}

// ─────────────────────────────────────────────
/**
 * CategoryController - Single Responsibility
 */
class CategoryController extends BaseController {
    private CategoryModel $model;

    public function __construct() {
        $this->model = new CategoryModel();
    }

    public function index(): void {
        $this->json($this->model->findAllWithCount());
    }
}

// ─────────────────────────────────────────────
/**
 * CartController - Single Responsibility
 */
class CartController extends BaseController {
    private CartModel $cartModel;

    public function __construct() {
        $this->cartModel = new CartModel();
    }

    private function getOrCreateCart(int $userId): int {
        $cart = $this->cartModel->findByUser($userId);
        if ($cart) return $cart['cart_id'];
        return $this->cartModel->create(['user_id' => $userId]);
    }

    public function get(): void {
        $this->requireAuth();
        $uid    = $_SESSION['user_id'];
        $cartId = $this->getOrCreateCart($uid);
        $items  = $this->cartModel->getCartItems($cartId);
        $total  = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));
        $this->json(['items' => $items, 'total' => $total, 'cart_id' => $cartId]);
    }

    public function add(): void {
        $this->requireAuth();
        $data   = $this->getJson();
        $bookId = (int)($data['book_id'] ?? 0);
        $qty    = (int)($data['quantity'] ?? 1);
        if (!$bookId) $this->jsonError('Invalid book_id');

        $cartId = $this->getOrCreateCart($_SESSION['user_id']);
        $this->cartModel->addItem($cartId, $bookId, $qty);
        $this->json(null, 200);
    }

    public function update(): void {
        $this->requireAuth();
        $data = $this->getJson();
        $this->cartModel->updateItem((int)$data['cart_item_id'], (int)$data['quantity']);
        $this->json(null);
    }

    public function remove(): void {
        $this->requireAuth();
        $data = $this->getJson();
        $this->cartModel->removeItem((int)$data['cart_item_id']);
        $this->json(null);
    }
}

// ─────────────────────────────────────────────
/**
 * OrderController - Single Responsibility
 */
class OrderController extends BaseController {
    private OrderModel $orderModel;
    private CartModel  $cartModel;

    public function __construct() {
        $this->orderModel = new OrderModel();
        $this->cartModel  = new CartModel();
    }

    public function checkout(): void {
        $this->requireAuth();
        $data   = $this->getJson();
        $uid    = $_SESSION['user_id'];
        $method = $data['payment_method'] ?? 'cod';

        $cart   = $this->cartModel->findByUser($uid);
        if (!$cart) $this->jsonError('Giỏ hàng trống');

        $items = $this->cartModel->getCartItems($cart['cart_id']);
        if (!$items) $this->jsonError('Giỏ hàng trống');

        $total   = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));
        $orderId = $this->orderModel->createOrder($uid, $total, $method);
        foreach ($items as $item) {
            $this->orderModel->addOrderItem($orderId, $item['book_id'], $item['quantity'], $item['price']);
        }
        $this->cartModel->clearCart($cart['cart_id']);
        $this->json(['order_id' => $orderId], 201);
    }

    public function myOrders(): void {
        $this->requireAuth();
        $this->json($this->orderModel->findByUser($_SESSION['user_id']));
    }
}

// ─────────────────────────────────────────────
/**
 * ImageController - Single Responsibility: xử lý upload/xóa ảnh sách
 */
class ImageController extends BaseController {

    /**
     * Upload ảnh cho sách: POST /books/{id}/image
     * Form-data: file = ảnh (jpg/png/webp/gif), is_primary (optional, default 0)
     */
    public function upload(int $bookId): void {
        $this->requireAuth();
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $this->jsonError('Forbidden: chỉ admin mới được upload ảnh', 403);
        }

        if (empty($_FILES['file'])) {
            $this->jsonError('Không tìm thấy file upload');
        }

        $file      = $_FILES['file'];
        $allowed   = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $maxSize   = 5 * 1024 * 1024; // 5 MB

        if (!in_array($file['type'], $allowed)) {
            $this->jsonError('Chỉ chấp nhận ảnh JPG, PNG, WEBP, GIF');
        }
        if ($file['size'] > $maxSize) {
            $this->jsonError('File quá lớn (tối đa 5MB)');
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->jsonError('Lỗi khi upload file: ' . $file['error']);
        }

        // Tạo thư mục nếu chưa có
        $uploadDir = __DIR__ . '/../../../../assets/images/books/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'book_' . $bookId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $destPath = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            $this->jsonError('Không thể lưu file. Kiểm tra quyền thư mục.');
        }

        // Đường dẫn lưu vào DB (relative từ gốc web)
        $imageUrl  = 'assets/images/books/' . $filename;
        $isPrimary = (int)($_POST['is_primary'] ?? 0);

        $pdo = Database::getInstance()->getConnection();

        // Nếu là ảnh primary thì bỏ primary cũ
        if ($isPrimary) {
            $pdo->prepare("UPDATE bookstore_book_images SET is_primary = 0 WHERE book_id = ?")
                ->execute([$bookId]);
        }

        $stmt = $pdo->prepare(
            "INSERT INTO bookstore_book_images (book_id, image_url, is_primary) VALUES (?, ?, ?)"
        );
        $stmt->execute([$bookId, $imageUrl, $isPrimary]);
        $imageId = $pdo->lastInsertId();

        $this->json([
            'image_id'  => $imageId,
            'image_url' => $imageUrl,
            'book_id'   => $bookId,
        ], 201);
    }

    /**
     * Xóa ảnh: DELETE /books/{bookId}/image/{imageId}
     */
    public function delete(int $bookId, int $imageId): void {
        $this->requireAuth();
        if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $this->jsonError('Forbidden', 403);
        }

        $pdo  = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT image_url FROM bookstore_book_images WHERE image_id = ? AND book_id = ?");
        $stmt->execute([$imageId, $bookId]);
        $row  = $stmt->fetch();

        if (!$row) {
            $this->jsonError('Không tìm thấy ảnh', 404);
        }

        // Xóa file thực trên disk
        $filePath = __DIR__ . '/../../../../' . $row['image_url'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $pdo->prepare("DELETE FROM bookstore_book_images WHERE image_id = ?")
            ->execute([$imageId]);

        $this->json(['deleted' => $imageId]);
    }

    /**
     * Lấy danh sách ảnh của sách: GET /books/{id}/images
     */
    public function list(int $bookId): void {
        $pdo  = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM bookstore_book_images WHERE book_id = ? ORDER BY is_primary DESC, image_id ASC");
        $stmt->execute([$bookId]);
        $this->json($stmt->fetchAll());
    }
}

// ─────────────────────────────────────────────
/**
 * AuthController - Single Responsibility
 */
class AuthController extends BaseController {
    private UserModel $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function login(): void {
        session_start();
        $data  = $this->getJson();
        $user  = $this->userModel->findByEmail($data['email'] ?? '');
        if (!$user || !password_verify($data['password'] ?? '', $user['password'])) {
            $this->jsonError('Email hoặc mật khẩu không đúng', 401);
        }
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role']    = $user['role'];
        $_SESSION['name']    = $user['full_name'];
        $this->json(['name' => $user['full_name'], 'role' => $user['role']]);
    }

    public function register(): void {
        $data = $this->getJson();
        if ($this->userModel->findByEmail($data['email'] ?? '')) {
            $this->jsonError('Email đã tồn tại');
        }
        $id = $this->userModel->register(
            $data['full_name'] ?? '',
            $data['email']     ?? '',
            $data['phone']     ?? '',
            $data['password']  ?? ''
        );
        $this->json(['user_id' => $id], 201);
    }

    public function logout(): void {
        session_start();
        session_destroy();
        $this->json(null);
    }

    public function me(): void {
        session_start();
        if (empty($_SESSION['user_id'])) {
            $this->json(null);
        } else {
            $this->json([
                'user_id' => $_SESSION['user_id'],
                'name'    => $_SESSION['name'],
                'role'    => $_SESSION['role'],
            ]);
        }
    }

    /**
     * Bước 1: Nhận email → tạo OTP → lưu vào DB (bảng bookstore_password_resets)
     */
    public function forgotPassword(): void {
        $data  = $this->getJson();
        $email = trim($data['email'] ?? '');
        $user  = $this->userModel->findByEmail($email);
        if (!$user) {
            $this->jsonError('Không tìm thấy tài khoản với email này', 404);
        }

        $otp     = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires = date('Y-m-d H:i:s', time() + 600); // hết hạn sau 10 phút
        $pdo     = Database::getInstance()->getConnection();

        // Xóa OTP cũ nếu có
        $pdo->prepare("DELETE FROM bookstore_password_resets WHERE email = ?")
            ->execute([$email]);
        $pdo->prepare("INSERT INTO bookstore_password_resets (email, token, expires_at) VALUES (?, ?, ?)")
            ->execute([$email, $otp, $expires]);

        // TODO: Gửi email thực — hiện tại log ra file để test
        file_put_contents(
            __DIR__ . '/../../../../otp_debug.log',
            date('[Y-m-d H:i:s]') . " OTP cho $email: $otp\n",
            FILE_APPEND
        );

        $this->json(['message' => 'Đã gửi mã xác nhận (xem otp_debug.log để test)']);
    }

    /**
     * Bước 2: Xác nhận OTP → trả về token tạm thời
     */
    public function verifyOtp(): void {
        $data  = $this->getJson();
        $email = trim($data['email'] ?? '');
        $otp   = trim($data['otp']   ?? '');

        $pdo  = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare(
            "SELECT * FROM bookstore_password_resets WHERE email = ? AND token = ? AND expires_at > NOW()"
        );
        $stmt->execute([$email, $otp]);
        $row = $stmt->fetch();

        if (!$row) {
            $this->jsonError('Mã xác nhận không đúng hoặc đã hết hạn', 400);
        }

        // Tạo token đổi mật khẩu (dùng 1 lần)
        $resetToken = bin2hex(random_bytes(16));
        $pdo->prepare("UPDATE bookstore_password_resets SET token = ? WHERE email = ?")
            ->execute([$resetToken, $email]);

        $this->json(['token' => $resetToken]);
    }

    /**
     * Bước 3: Đặt mật khẩu mới
     */
    public function resetPassword(): void {
        $data     = $this->getJson();
        $email    = trim($data['email']    ?? '');
        $token    = trim($data['token']    ?? '');
        $password = trim($data['password'] ?? '');

        if (strlen($password) < 6) {
            $this->jsonError('Mật khẩu phải có ít nhất 6 ký tự');
        }

        $pdo  = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare(
            "SELECT * FROM bookstore_password_resets WHERE email = ? AND token = ? AND expires_at > NOW()"
        );
        $stmt->execute([$email, $token]);
        if (!$stmt->fetch()) {
            $this->jsonError('Phiên đặt lại mật khẩu không hợp lệ hoặc đã hết hạn', 400);
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE bookstore_users SET password = ? WHERE email = ?")
            ->execute([$hashed, $email]);
        $pdo->prepare("DELETE FROM bookstore_password_resets WHERE email = ?")
            ->execute([$email]);

        $this->json(['message' => 'Đặt lại mật khẩu thành công']);
    }
}
