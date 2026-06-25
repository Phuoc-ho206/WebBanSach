<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('cart/cart.php'));
    exit;
}

$productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$action = isset($_POST['action']) ? trim($_POST['action']) : 'update';
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

if ($productId <= 0) {
    header('Location: ' . url('cart/cart.php'));
    exit;
}

if (!isset($_SESSION['cart']) || !isset($_SESSION['cart'][$productId])) {
    header('Location: ' . url('cart/cart.php'));
    exit;
}

if ($action === 'delete') {
    unset($_SESSION['cart'][$productId]);
    $_SESSION['success'] = 'Đã xóa sản phẩm khỏi giỏ hàng.';
} else {
    // Trường hợp cập nhật số lượng
    if ($quantity <= 0) {
        unset($_SESSION['cart'][$productId]);
        $_SESSION['success'] = 'Đã xóa sản phẩm khỏi giỏ hàng.';
    } else {
        // Kiểm tra tồn kho của sản phẩm
        $stmt = $conn->prepare("SELECT ProductName, Quantity FROM product WHERE ProductID = ?");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if ($res->num_rows > 0) {
            $product = $res->fetch_assoc();
            if ($quantity > $product['Quantity']) {
                $_SESSION['cart'][$productId] = $product['Quantity'];
                $_SESSION['warning'] = 'Số lượng cập nhật vượt quá tồn kho. Tự động điều chỉnh về tối đa: ' . $product['Quantity'] . ' sản phẩm.';
            } else {
                $_SESSION['cart'][$productId] = $quantity;
                $_SESSION['success'] = 'Đã cập nhật số lượng giỏ hàng thành công.';
            }
        }
        $stmt->close();
    }
}

header('Location: ' . url('cart/cart.php'));
exit;
?>
