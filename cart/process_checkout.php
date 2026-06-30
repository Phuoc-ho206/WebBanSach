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
    // 1. Tính tổng tiền thực tế từ DB để tránh giả mạo giá từ client (Có kết hợp khuyến mãi sản phẩm)
    $productIds = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $sql = "
        SELECT p.ProductID, p.Price AS OriginalPrice, p.Quantity, p.Status, ap.DiscountRate
        FROM product p
        LEFT JOIN (
            SELECT pd.ProductID, MAX(pd.DiscountRate) AS DiscountRate
            FROM promotion_detail pd
            JOIN promotion pr ON pd.PromotionID = pr.PromotionID
            WHERE NOW() BETWEEN COALESCE(pd.StartDate, pr.StartDate) AND COALESCE(pd.EndDate, pr.EndDate)
            GROUP BY pd.ProductID
        ) ap ON p.ProductID = ap.ProductID
        WHERE p.ProductID IN ($placeholders) FOR UPDATE
    ";
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
        
        $discountRate = isset($p['DiscountRate']) ? floatval($p['DiscountRate']) : 0;
        $actualPrice = $p['OriginalPrice'] - ($p['OriginalPrice'] * $discountRate / 100);
        $productsDb[$pId]['ActualPrice'] = $actualPrice;
        
        $totalAmount += $actualPrice * $qty;
    }
    
    // 2. Xác thực và tính toán mã giảm giá (Voucher)
    $voucherId = null;
    $voucherDiscount = 0;
    
    if (isset($_SESSION['applied_voucher']) && $customerId !== null) {
        $sessionVoucher = $_SESSION['applied_voucher'];
        $sqlVoucher = "SELECT * FROM voucher WHERE VoucherID = ? AND (ExpiredDate IS NULL OR ExpiredDate >= NOW()) LIMIT 1 FOR UPDATE";
        $stmtVoucher = $conn->prepare($sqlVoucher);
        $stmtVoucher->bind_param("i", $sessionVoucher['id']);
        $stmtVoucher->execute();
        $resVoucher = $stmtVoucher->get_result();
        
        if ($resVoucher->num_rows === 0) {
            throw new Exception("Mã giảm giá đã áp dụng không hợp lệ hoặc đã hết hạn.");
        }
        
        $voucher = $resVoucher->fetch_assoc();
        $stmtVoucher->close();
        
        // Kiểm tra xem voucher đã được khách hàng sử dụng chưa
        $sqlCheckUsed = "SELECT UsedStatus FROM voucher_detail WHERE CustomerID = ? AND VoucherID = ? LIMIT 1 FOR UPDATE";
        $stmtCheck = $conn->prepare($sqlCheckUsed);
        $stmtCheck->bind_param("ii", $customerId, $voucher['VoucherID']);
        $stmtCheck->execute();
        $resCheck = $stmtCheck->get_result();
        
        if ($resCheck->num_rows > 0) {
            $usedRow = $resCheck->fetch_assoc();
            if (intval($usedRow['UsedStatus']) === 1) {
                throw new Exception("Bạn đã sử dụng mã giảm giá này cho một đơn hàng trước đó.");
            }
        }
        $stmtCheck->close();
        
        $voucherId = intval($voucher['VoucherID']);
        $voucherDiscount = floatval($voucher['DiscountValue']);
    } else {
        // Khách vãng lai hoặc không áp dụng voucher
        unset($_SESSION['applied_voucher']);
    }
    
    $finalTotalAmount = max(0, $totalAmount + $shippingFee - $voucherDiscount);

    // 3. Thêm vào bảng `order`
    $sqlOrder = "INSERT INTO `order` (CustomerID, VoucherID, ShippingAddress, OrderStatus, TotalAmount) VALUES (?, ?, ?, 'Pending', ?)";
    $stmtOrder = $conn->prepare($sqlOrder);
    $stmtOrder->bind_param("iisd", $customerId, $voucherId, $shippingAddress, $finalTotalAmount);
    $stmtOrder->execute();
    $orderId = $conn->insert_id;
    $stmtOrder->close();

    // 4. Cập nhật trạng thái voucher thành đã sử dụng (UsedStatus = 1)
    if ($customerId !== null && $voucherId !== null) {
        $sqlCheckDetail = "SELECT 1 FROM voucher_detail WHERE CustomerID = ? AND VoucherID = ? LIMIT 1";
        $stmtCD = $conn->prepare($sqlCheckDetail);
        $stmtCD->bind_param("ii", $customerId, $voucherId);
        $stmtCD->execute();
        $resCD = $stmtCD->get_result();
        $stmtCD->close();
        
        if ($resCD->num_rows > 0) {
            $sqlUpdateVoucher = "UPDATE voucher_detail SET UsedStatus = 1 WHERE CustomerID = ? AND VoucherID = ?";
            $stmtUV = $conn->prepare($sqlUpdateVoucher);
            $stmtUV->bind_param("ii", $customerId, $voucherId);
            $stmtUV->execute();
            $stmtUV->close();
        } else {
            $sqlInsertVoucher = "INSERT INTO voucher_detail (CustomerID, VoucherID, UsedStatus) VALUES (?, ?, 1)";
            $stmtIV = $conn->prepare($sqlInsertVoucher);
            $stmtIV->bind_param("ii", $customerId, $voucherId);
            $stmtIV->execute();
            $stmtIV->close();
        }
    }

    // 5. Thêm vào bảng `order_detail` & Trừ tồn kho
    $sqlDetail = "INSERT INTO `order_detail` (OrderID, ProductID, Quantity, Price, UnitPrice) VALUES (?, ?, ?, ?, ?)";
    $stmtDetail = $conn->prepare($sqlDetail);
    
    $sqlUpdateStock = "UPDATE product SET Quantity = ?, Status = ? WHERE ProductID = ?";
    $stmtUpdateStock = $conn->prepare($sqlUpdateStock);
    
    foreach ($cart as $pId => $qty) {
        $p = $productsDb[$pId];
        $unitPrice = $p['ActualPrice'];
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

    // 6. Thêm bản ghi `payment`
    $sqlPayment = "INSERT INTO `payment` (OrderID, PaymentMethod, PaymentStatus) VALUES (?, ?, 'Pending')";
    $stmtPayment = $conn->prepare($sqlPayment);
    $stmtPayment->bind_param("is", $orderId, $paymentMethod);
    $stmtPayment->execute();
    $stmtPayment->close();

    // 7. Thêm bản ghi `delivery`
    $sqlDelivery = "INSERT INTO `delivery` (OrderID, DeliveryStatus, ShippingFee) VALUES (?, 'Preparing', ?)";
    $stmtDelivery = $conn->prepare($sqlDelivery);
    $stmtDelivery->bind_param("id", $orderId, $shippingFee);
    $stmtDelivery->execute();
    $stmtDelivery->close();

    // 8. Nếu là COD, đánh dấu giỏ hàng DB đã hoàn thành
    if ($paymentMethod === 'COD' && $customerId !== null && $customerId > 0) {
        $stmtDelCartDetail = $conn->prepare("DELETE FROM cart_detail WHERE CartID IN (SELECT CartID FROM cart WHERE CustomerID = ? AND Status = 'Active')");
        $stmtDelCartDetail->bind_param("i", $customerId);
        $stmtDelCartDetail->execute();
        $stmtDelCartDetail->close();

        $stmtCompleteCart = $conn->prepare("UPDATE cart SET Status = 'Completed' WHERE CustomerID = ? AND Status = 'Active'");
        $stmtCompleteCart->bind_param("i", $customerId);
        $stmtCompleteCart->execute();
        $stmtCompleteCart->close();
    }

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
    unset($_SESSION['applied_voucher']);
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
