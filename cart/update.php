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

$customerId = isset($_SESSION['user']) ? intval($_SESSION['user']['id']) : null;

if ($action === 'delete') {
    if ($customerId !== null && $customerId > 0) {
        // Xóa trong database
        $stmtCart = $conn->prepare("SELECT CartID FROM cart WHERE CustomerID = ? AND Status = 'Active' LIMIT 1");
        $stmtCart->bind_param("i", $customerId);
        $stmtCart->execute();
        $resCart = $stmtCart->get_result();
        if ($resCart->num_rows > 0) {
            $rowCart = $resCart->fetch_assoc();
            $cartId = intval($rowCart['CartID']);
            $stmtDel = $conn->prepare("DELETE FROM cart_detail WHERE CartID = ? AND ProductID = ?");
            $stmtDel->bind_param("ii", $cartId, $productId);
            $stmtDel->execute();
            $stmtDel->close();
        }
        $stmtCart->close();
    }
    unset($_SESSION['cart'][$productId]);
    unset($_SESSION['applied_voucher']);
    $_SESSION['success'] = 'Đã xóa sản phẩm khỏi giỏ hàng.';
} else {
    // Trường hợp cập nhật số lượng
    if ($quantity <= 0) {
        if ($customerId !== null && $customerId > 0) {
            // Xóa trong database
            $stmtCart = $conn->prepare("SELECT CartID FROM cart WHERE CustomerID = ? AND Status = 'Active' LIMIT 1");
            $stmtCart->bind_param("i", $customerId);
            $stmtCart->execute();
            $resCart = $stmtCart->get_result();
            if ($resCart->num_rows > 0) {
                $rowCart = $resCart->fetch_assoc();
                $cartId = intval($rowCart['CartID']);
                $stmtDel = $conn->prepare("DELETE FROM cart_detail WHERE CartID = ? AND ProductID = ?");
                $stmtDel->bind_param("ii", $cartId, $productId);
                $stmtDel->execute();
                $stmtDel->close();
            }
            $stmtCart->close();
        }
        unset($_SESSION['cart'][$productId]);
        unset($_SESSION['applied_voucher']);
        $_SESSION['success'] = 'Đã xóa sản phẩm khỏi giỏ hàng.';
    } else {
        // Kiểm tra tồn kho của sản phẩm
        $stmt = $conn->prepare("SELECT ProductName, Quantity FROM product WHERE ProductID = ?");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if ($res->num_rows > 0) {
            $product = $res->fetch_assoc();
            $finalQty = $quantity;
            if ($quantity > $product['Quantity']) {
                $finalQty = $product['Quantity'];
                $_SESSION['warning'] = 'Số lượng cập nhật vượt quá tồn kho. Tự động điều chỉnh về tối đa: ' . $product['Quantity'] . ' sản phẩm.';
            } else {
                $_SESSION['success'] = 'Đã cập nhật số lượng giỏ hàng thành công.';
            }
            
            if ($customerId !== null && $customerId > 0) {
                // Cập nhật trong database
                $stmtCart = $conn->prepare("SELECT CartID FROM cart WHERE CustomerID = ? AND Status = 'Active' LIMIT 1");
                $stmtCart->bind_param("i", $customerId);
                $stmtCart->execute();
                $resCart = $stmtCart->get_result();
                if ($resCart->num_rows > 0) {
                    $rowCart = $resCart->fetch_assoc();
                    $cartId = intval($rowCart['CartID']);
                    
                    // Đảm bảo có dòng ghi trong cart_detail để UPDATE
                    $stmtCheck = $conn->prepare("SELECT 1 FROM cart_detail WHERE CartID = ? AND ProductID = ?");
                    $stmtCheck->bind_param("ii", $cartId, $productId);
                    $stmtCheck->execute();
                    $resCheck = $stmtCheck->get_result();
                    $stmtCheck->close();
                    
                    if ($resCheck->num_rows > 0) {
                        $stmtUp = $conn->prepare("UPDATE cart_detail SET Quantity = ? WHERE CartID = ? AND ProductID = ?");
                        $stmtUp->bind_param("iii", $finalQty, $cartId, $productId);
                        $stmtUp->execute();
                        $stmtUp->close();
                    } else {
                        $stmtIn = $conn->prepare("INSERT INTO cart_detail (CartID, ProductID, Quantity) VALUES (?, ?, ?)");
                        $stmtIn->bind_param("iii", $cartId, $productId, $finalQty);
                        $stmtIn->execute();
                        $stmtIn->close();
                    }
                }
                $stmtCart->close();
            }
            
            $_SESSION['cart'][$productId] = $finalQty;
        }
        $stmt->close();
    }
}

header('Location: ' . url('cart/cart.php'));
exit;
?>
