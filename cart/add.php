<?php
require_once '../config/db.php';

// Kiểm tra phương thức gửi lên
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('trangchu/index.php'));
    exit;
}

$productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

if ($productId <= 0) {
    header('Location: ' . url('trangchu/index.php'));
    exit;
}

if ($quantity <= 0) {
    $quantity = 1;
}

// Kiểm tra sản phẩm trong CSDL
$stmt = $conn->prepare("SELECT ProductID, ProductName, Price, Quantity, Status FROM product WHERE ProductID = ?");
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Sản phẩm không tồn tại
    header('Location: ' . url('trangchu/index.php'));
    exit;
}

$product = $result->fetch_assoc();
$stmt->close();

// Kiểm tra tồn kho và trạng thái
if ($product['Quantity'] <= 0 || $product['Status'] === 'Hết hàng') {
    // Hết hàng
    $_SESSION['error'] = 'Sản phẩm "' . $product['ProductName'] . '" đã hết hàng!';
    header('Location: ' . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : url('trangchu/index.php')));
    exit;
}

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Kiểm tra số lượng hiện tại đã có trong giỏ
$currentQtyInCart = isset($_SESSION['cart'][$productId]) ? $_SESSION['cart'][$productId] : 0;
$newQty = $currentQtyInCart + $quantity;

// Số lượng không được vượt quá số lượng trong kho
if ($newQty > $product['Quantity']) {
    $_SESSION['cart'][$productId] = $product['Quantity'];
    $_SESSION['warning'] = 'Chỉ có thể thêm tối đa ' . $product['Quantity'] . ' sản phẩm vào giỏ hàng do giới hạn tồn kho.';
} else {
    $_SESSION['cart'][$productId] = $newQty;
    $_SESSION['success'] = 'Đã thêm "' . $product['ProductName'] . '" vào giỏ hàng thành công!';
}

header('Location: ' . url('cart/cart.php'));
exit;
?>
