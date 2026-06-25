<?php
require_once '../config/db.php';
require_once '../config/vnpay.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('cart/cart.php'));
    exit;
}

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    $_SESSION['error'] = 'Giỏ hàng của bạn đang trống!';
    header('Location: ' . url('cart/cart.php'));
    exit;
}

$fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$address = isset($_POST['address']) ? trim($_POST['address']) : '';
$saveInfo = isset($_POST['save_info']) ? $_POST['save_info'] === '1' : false;
$paymentMethod = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : 'COD';
$note = isset($_POST['note']) ? trim($_POST['note']) : '';

if (empty($fullname) || empty($phone) || empty($address)) {
    $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin giao hàng!';
    header('Location: ' . url('cart/checkout.php'));
    exit;
}

// Xử lý cookie lưu thông tin cho khách vãng lai
if (!isset($_SESSION['user'])) {
    if ($saveInfo) {
        $guestData = json_encode([
            'fullname' => $fullname,
            'phone' => $phone,
            'address' => $address
        ]);
        setcookie('guest_checkout', $guestData, time() + 30 * 86400, '/');
    } else {
        setcookie('guest_checkout', '', time() - 3600, '/');
    }
    $_SESSION['guest_checkout'] = [
        'fullname' => $fullname,
        'phone' => $phone,
        'address' => $address
    ];
}

// Lấy ID người dùng (nếu đã đăng nhập)
$customerId = isset($_SESSION['user']) ? intval($_SESSION['user']['id']) : null;

// Định dạng địa chỉ giao hàng
$shippingAddress = $address;
if (!$customerId) {
    $shippingAddress = "Người nhận: " . $fullname . " | SĐT: " . $phone . " | Địa chỉ: " . $address;
}

// Phí vận chuyển mặc định
$shippingFee = 0;
$totalAmount = 0;

// Bắt đầu Transaction
$conn->begin_transaction();

try {
    // 1. Tính tổng tiền thực tế từ DB để tránh giả mạo giá từ client
    $productIds = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $sql = "SELECT ProductID, Price, Quantity, Status FROM product WHERE ProductID IN ($placeholders) FOR UPDATE";
    $stmt = $conn->prepare($sql);
    $types = str_repeat('i', count($productIds));
    $stmt->bind_param($types, ...$productIds);
    $stmt->execute();
    $res = $stmt->get_result();
    
    $productsDb = [];
    while ($row = $res->fetch_assoc()) {
        $productsDb[$row['ProductID']] = $row;
    }
    $stmt->close();
    
    foreach ($cart as $pId => $qty) {
        if (!isset($productsDb[$pId])) {
            throw new Exception("Sản phẩm mã #$pId không tồn tại trong hệ thống.");
        }
        $p = $productsDb[$pId];
        if ($p['Quantity'] < $qty || $p['Status'] === 'Hết hàng') {
            throw new Exception("Sản phẩm mã #$pId không đủ tồn kho thực tế.");
        }
        $totalAmount += $p['Price'] * $qty;
    }
    
    $finalTotalAmount = $totalAmount + $shippingFee;

    // 2. Thêm vào bảng `order`
    $sqlOrder = "INSERT INTO `order` (CustomerID, VoucherID, ShippingAddress, OrderStatus, TotalAmount) VALUES (?, NULL, ?, 'Pending', ?)";
    $stmtOrder = $conn->prepare($sqlOrder);
    
    // PHP bind_param xử lý kiểu dữ liệu nullable
    if ($customerId === null) {
        $nullVal = null;
        $stmtOrder->bind_param("ssd", $nullVal, $shippingAddress, $finalTotalAmount);
    } else {
        $stmtOrder->bind_param("isd", $customerId, $shippingAddress, $finalTotalAmount);
    }
    $stmtOrder->execute();
    $orderId = $conn->insert_id;
    $stmtOrder->close();

    // 3. Thêm vào bảng `order_detail` & Trừ tồn kho
    $sqlDetail = "INSERT INTO `order_detail` (OrderID, ProductID, Quantity, Price, UnitPrice) VALUES (?, ?, ?, ?, ?)";
    $stmtDetail = $conn->prepare($sqlDetail);
    
    $sqlUpdateStock = "UPDATE product SET Quantity = ?, Status = ? WHERE ProductID = ?";
    $stmtUpdateStock = $conn->prepare($sqlUpdateStock);
    
    foreach ($cart as $pId => $qty) {
        $p = $productsDb[$pId];
        $unitPrice = $p['Price'];
        $linePrice = $unitPrice * $qty;
        
        // Thêm chi tiết đơn
        $stmtDetail->bind_param("iiidd", $orderId, $pId, $qty, $linePrice, $unitPrice);
        $stmtDetail->execute();
        
        // Cập nhật tồn kho
        $newQty = $p['Quantity'] - $qty;
        $newStatus = ($newQty <= 0) ? 'Hết hàng' : $p['Status'];
        $stmtUpdateStock->bind_param("isi", $newQty, $newStatus, $pId);
        $stmtUpdateStock->execute();
    }
    $stmtDetail->close();
    $stmtUpdateStock->close();

    // 4. Thêm bản ghi `payment`
    $sqlPayment = "INSERT INTO `payment` (OrderID, PaymentMethod, PaymentStatus) VALUES (?, ?, 'Pending')";
    $stmtPayment = $conn->prepare($sqlPayment);
    $stmtPayment->bind_param("is", $orderId, $paymentMethod);
    $stmtPayment->execute();
    $stmtPayment->close();

    // 5. Thêm bản ghi `delivery`
    $sqlDelivery = "INSERT INTO `delivery` (OrderID, DeliveryStatus, ShippingFee) VALUES (?, 'Preparing', ?)";
    $stmtDelivery = $conn->prepare($sqlDelivery);
    $stmtDelivery->bind_param("id", $orderId, $shippingFee);
    $stmtDelivery->execute();
    $stmtDelivery->close();

    // Commit Transaction thành công
    $conn->commit();
    
    // Nếu chọn thanh toán VNPAY
    if ($paymentMethod === 'VNPAY') {
        // Thiết lập múi giờ Việt Nam để khớp thời gian với cổng VNPAY
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        
        $vnp_TxnRef = $orderId;
        $vnp_OrderInfo = "Thanh toan don hang WBS-" . $orderId;
        $vnp_OrderType = "billpayment";
        $vnp_Amount = $finalTotalAmount * 100; // VNPAY nhận số tiền nhân với 100
        $vnp_Locale = "vn";
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => VNP_TMNCODE,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => VNP_RETURNURL,
            "vnp_TxnRef" => $vnp_TxnRef
        );

        // Sắp xếp dữ liệu theo thứ tự bảng chữ cái để tạo hash checksum
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $query .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $query .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $hashdata .= urlencode($key) . '=' . urlencode($value) . '&';
        }
        $hashdata = rtrim($hashdata, '&');

        $vnp_PaymentUrl = VNP_URL . "?" . $query;
        if (defined('VNP_HASHSECRET') && VNP_HASHSECRET !== 'YOUR_HASHSECRET' && !empty(VNP_HASHSECRET)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, VNP_HASHSECRET);
            $vnp_PaymentUrl .= '&vnp_SecureHash=' . $vnpSecureHash;
        }
        
        // Lưu giữ OrderID trong session để xác thực ở trang vnpay_return
        $_SESSION['pending_vnpay_order_id'] = $orderId;
        
        // Chuyển hướng sang VNPAY
        header('Location: ' . $vnp_PaymentUrl);
        exit;
    }

    // Nếu chọn thanh toán COD
    unset($_SESSION['cart']);
    $_SESSION['success_order_id'] = $orderId;
    header('Location: ' . url('cart/success.php?id=' . $orderId));
    exit;

} catch (Exception $e) {
    // Rollback nếu có lỗi xảy ra
    $conn->rollback();
    $_SESSION['error'] = 'Đặt hàng không thành công: ' . $e->getMessage();
    header('Location: ' . url('cart/checkout.php'));
    exit;
}
?>
