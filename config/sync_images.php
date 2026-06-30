<?php
// Gọi file kết nối cơ sở dữ liệu
require_once 'db.php'; 

echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 30px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: #fff;'>";
echo "<h2 style='color: #f37022; text-align: center;'>HỆ THỐNG ĐỒNG BỘ ẢNH BÌA SÁCH (OPEN LIBRARY API)</h2>";
echo "<hr style='border: 0; border-top: 1px solid #eee; margin-bottom: 20px;'>";

// 1. Hàm cURL xử lý tải dữ liệu, tự động theo dõi chuyển hướng (Follow Location) và bỏ qua SSL của WAMP
function fetch_url_data($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    // Open Library yêu cầu có User-Agent định danh rõ ràng
    curl_setopt($ch, CURLOPT_USERAGENT, 'WebBanSach_Local_Project/1.0 (Student_Project)');
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
    $result = curl_exec($ch);
    if(curl_errno($ch)) return false;
    curl_close($ch);
    return $result;
}

// 2. Dọn dẹp dữ liệu ảnh lỗi cũ
$conn->query("DELETE FROM image");
$conn->query("ALTER TABLE image AUTO_INCREMENT = 1");
echo "<p style='color: #2196f3; font-weight: bold;'>🧹 Đã dọn dẹp sạch các liên kết ảnh cũ trong Database.</p>";

// 3. Truy vấn danh sách sách từ CSDL
$sql = "SELECT ProductID, ProductName FROM product";
$result = $conn->query($sql);

$target_dir = __DIR__ . '/../assets/images/books/';
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

$success_count = 0;
echo "<div style='background: #f9f9f9; padding: 15px; border-radius: 6px; max-height: 400px; overflow-y: auto;'>";

while ($row = $result->fetch_assoc()) {
    $productId = $row['ProductID'];
    $productName = $row['ProductName'];

    echo "<div style='margin-bottom: 10px; border-bottom: 1px dashed #ddd; padding-bottom: 8px;'>";
    echo "📖 Đang quét sách: <strong>" . htmlspecialchars($productName) . "</strong>... ";

    // BƯỚC 1: Dùng Search API của Open Library để tìm ID của ảnh bìa (cover_i)
    $searchUrl = "https://openlibrary.org/search.json?title=" . urlencode($productName) . "&limit=1";
    $searchResponse = fetch_url_data($searchUrl);
    $searchData = $searchResponse !== false ? json_decode($searchResponse, true) : null;

    $download_url = '';
    $file_extension = '.jpg';
    $is_fallback = false;

    // BƯỚC 2: Kiểm tra xem sách có mã cover_i không
    if (isset($searchData['docs'][0]['cover_i'])) {
        $cover_id = $searchData['docs'][0]['cover_i'];
        
        // Gọi đến API Covers để lấy ảnh kích thước L (Large)
        $download_url = "https://covers.openlibrary.org/b/id/{$cover_id}-L.jpg";
    } else {
        // NẾU KHÔNG CÓ TRÊN OPEN LIBRARY: Dùng ảnh dự phòng nền cam chữ trắng
        $download_url = 'https://placehold.co/400x600/f37022/ffffff/png?text=' . urlencode($productName);
        $file_extension = '.png';
        $is_fallback = true;
    }

    // 4. Tiến hành tải ảnh từ URL về WAMP
    $file_name = 'openlib_' . $productId . $file_extension;
    $local_save_path = $target_dir . $file_name;
    
    $img_data = fetch_url_data($download_url);
    
    // Kiểm tra dung lượng file tải về (Open Library đôi khi trả về ảnh 1x1 pixel 43 bytes nếu lỗi)
    if ($img_data !== false && strlen($img_data) > 1000) {
        file_put_contents($local_save_path, $img_data);

        $db_image_url = 'assets/images/books/' . $file_name; // Đường dẫn chuẩn lưu DB
        $alt_text = 'Bìa sách ' . $productName;

        $stmt = $conn->prepare("INSERT INTO image (ProductID, ImageURL, AltText, IsThumbnail, SortOrder) VALUES (?, ?, ?, 1, 1)");
        $stmt->bind_param("iss", $productId, $db_image_url, $alt_text);
        
        if ($stmt->execute()) {
            if ($is_fallback) {
                echo "<span style='color: #ffb703; font-weight: bold;'>✔ Đã tạo ảnh nền cam (Không có trên Open Library)</span>";
            } else {
                echo "<span style='color: #4caf50; font-weight: bold;'>✔ Đã tải bìa sách thật từ Open Library!</span>";
            }
            $success_count++;
        }
        $stmt->close();
    } else {
        echo "<span style='color: #e63946;'>❌ Lỗi tải ảnh (Dữ liệu rỗng hoặc bị chặn)</span>";
    }
    
    echo "</div>";
    
    // Nghỉ 0.5 giây để tránh bị Open Library chặn IP vì spam request
    usleep(500000); 
}

echo "</div>";
echo "<h3 style='color: #4caf50; text-align: center; margin-top: 20px;'>🎉 Hoàn tất! Đã đồng bộ $success_count ảnh bằng Open Library.</h3>";
echo "<div style='text-align: center; margin-top: 15px;'><a href='../trangchu/index.php' style='display: inline-block; background: #f37022; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;'>Trở về trang chủ Website</a></div>";
echo "</div>";
?>