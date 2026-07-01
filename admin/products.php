<?php
require_once 'data.php';
require_once 'partials.php';

$editProduct = null;
$editProductImage = null;
if (isset($_GET['edit'])) {
  $stmt = $conn->prepare("SELECT * FROM product WHERE ProductID = ?");
  $stmt->bind_param("i", $_GET['edit']);
  $stmt->execute();
  $editProduct = $stmt->get_result()->fetch_assoc();
  $stmt->close();
  
  if ($editProduct) {
    $imgStmt = $conn->prepare("SELECT ImageURL FROM image WHERE ProductID = ? AND IsThumbnail = 1 LIMIT 1");
    $imgStmt->bind_param("i", $editProduct['ProductID']);
    $imgStmt->execute();
    $imgRes = $imgStmt->get_result();
    if ($imgRes->num_rows > 0) {
      $editProductImage = $imgRes->fetch_assoc()['ImageURL'];
    }
    $imgStmt->close();
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'save') {
    $id = $_POST['id'] !== '' ? (int) $_POST['id'] : null;
    $name = trim($_POST['name']);
    $categoryId = (int) $_POST['category_id'];
    $price = (int) $_POST['price'];
    $stock = (int) $_POST['stock'];
    $publisher = trim($_POST['publisher'] ?? 'AlphaBooks');
    $description = trim($_POST['description'] ?? '');
    
    // Tự động cập nhật trạng thái nếu hết hàng
    $status = ($stock <= 0) ? 'Hết hàng' : trim($_POST['status'] ?? 'Còn hàng');

    // Xử lý upload ảnh bìa (Cloudinary)
    $uploadedImageUrl = null;
    $uploadFailed = false;
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
      require_once __DIR__ . '/../config/cloudinary.php';
      $uploadedImageUrl = CloudinaryHelper::uploadImage($_FILES['product_image']['tmp_name']);
      if (!$uploadedImageUrl) {
        $uploadFailed = true;
      }
    }

    if ($id) {
      $stmt = $conn->prepare("UPDATE product SET CategoryID = ?, ProductName = ?, Price = ?, Quantity = ?, Status = ?, Publisher = ?, Description = ? WHERE ProductID = ?");
      $stmt->bind_param("isiisssi", $categoryId, $name, $price, $stock, $status, $publisher, $description, $id);
      $stmt->execute();
      $stmt->close();
      
      // Nếu có ảnh mới upload thành công
      if ($uploadedImageUrl) {
        $checkStmt = $conn->prepare("SELECT ImageID FROM image WHERE ProductID = ? AND IsThumbnail = 1 LIMIT 1");
        $checkStmt->bind_param("i", $id);
        $checkStmt->execute();
        $checkRes = $checkStmt->get_result();
        if ($checkRes->num_rows > 0) {
          $imgId = $checkRes->fetch_assoc()['ImageID'];
          $updateImgStmt = $conn->prepare("UPDATE image SET ImageURL = ? WHERE ImageID = ?");
          $updateImgStmt->bind_param("si", $uploadedImageUrl, $imgId);
          $updateImgStmt->execute();
          $updateImgStmt->close();
        } else {
          $altText = 'Bìa sách ' . $name;
          $insertImgStmt = $conn->prepare("INSERT INTO image (ProductID, ImageURL, AltText, IsThumbnail, SortOrder) VALUES (?, ?, ?, 1, 1)");
          $insertImgStmt->bind_param("iss", $id, $uploadedImageUrl, $altText);
          $insertImgStmt->execute();
          $insertImgStmt->close();
        }
        $checkStmt->close();
      }
      
      if ($uploadFailed) {
        $_SESSION['log_toast'] = "Lỗi: Cập nhật sản phẩm thành công nhưng không thể upload ảnh lên Cloudinary (hãy kiểm tra credentials trong .env).";
      } else {
        write_user_log($conn, "Cập nhật sản phẩm ID " . $id . " - Tên: " . $name);
      }
    } else {
      $stmt = $conn->prepare("INSERT INTO product (CategoryID, ProductName, Price, Quantity, Status, Publisher, Description) VALUES (?, ?, ?, ?, ?, ?, ?)");
      $stmt->bind_param("isiisss", $categoryId, $name, $price, $stock, $status, $publisher, $description);
      $stmt->execute();
      $newId = $stmt->insert_id;
      $stmt->close();
      
      // Nếu có ảnh mới upload thành công
      if ($uploadedImageUrl) {
        $altText = 'Bìa sách ' . $name;
        $insertImgStmt = $conn->prepare("INSERT INTO image (ProductID, ImageURL, AltText, IsThumbnail, SortOrder) VALUES (?, ?, ?, 1, 1)");
        $insertImgStmt->bind_param("iss", $newId, $uploadedImageUrl, $altText);
        $insertImgStmt->execute();
        $insertImgStmt->close();
      }
      
      if ($uploadFailed) {
        $_SESSION['log_toast'] = "Lỗi: Thêm sản phẩm thành công nhưng không thể upload ảnh lên Cloudinary (hãy kiểm tra credentials trong .env).";
      } else {
        write_user_log($conn, "Thêm mới sản phẩm ID " . $newId . " - Tên: " . $name);
      }
    }
  }

  if ($action === 'delete') {
    $id = (int)$_POST['id'];
    $stmt = $conn->prepare("DELETE FROM product WHERE ProductID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    write_user_log($conn, "Xóa sản phẩm ID " . $id);
  }

  redirectTo('products.php');
}

// Lấy danh sách danh mục để điền vào select dropdown
$categoriesSelect = [];
$resCats = $conn->query("SELECT CategoryID, CategoryName FROM category ORDER BY CategoryName ASC");
if ($resCats) {
  while ($row = $resCats->fetch_assoc()) {
    $categoriesSelect[] = $row;
  }
}

// Lấy danh sách sản phẩm thực tế từ DB kèm tên danh mục
$products = [];
$resProds = $conn->query("
  SELECT p.ProductID, p.ProductName, p.Price, p.Quantity, p.Status, p.Publisher, p.Description, c.CategoryName 
  FROM product p 
  LEFT JOIN category c ON p.CategoryID = c.CategoryID 
  ORDER BY p.ProductID DESC
");
if ($resProds) {
  while ($row = $resProds->fetch_assoc()) {
    $products[] = $row;
  }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản lý sản phẩm</title><?= adminCssLinks() ?>
</head>
<body>
  <div class="app-layout">
    <?php adminSidebar(); ?>

    <main class="page-content">
      <header class="page-header"><div><h1>Quản lý sản phẩm</h1><p>Thêm, sửa, xóa sản phẩm sách</p></div></header>

      <form method="post" class="card" enctype="multipart/form-data">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= h($editProduct['ProductID'] ?? '') ?>">
        <div class="card__body form">
          <div class="form-row">
            <div class="form-group" style="flex: 2;">
              <label class="form-label">Tên sách</label>
              <input class="form-control" type="text" name="name" value="<?= h($editProduct['ProductName'] ?? '') ?>" required>
            </div>
            <div class="form-group" style="flex: 1;">
              <label class="form-label">Danh mục</label>
              <select class="form-control" name="category_id" required>
                <option value="">-- Chọn danh mục --</option>
                <?php foreach ($categoriesSelect as $cat): ?>
                  <option value="<?= h($cat['CategoryID']) ?>" <?= ($editProduct['CategoryID'] ?? '') == $cat['CategoryID'] ? 'selected' : '' ?>><?= h($cat['CategoryName']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Giá sách</label>
              <div class="input-unit">
                <input class="form-control" type="number" name="price" value="<?= h($editProduct['Price'] ?? '') ?>" required>
                <span class="input-unit__text">đ</span>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Số lượng tồn kho</label>
              <div class="input-unit">
                <input class="form-control" type="number" name="stock" value="<?= h($editProduct['Quantity'] ?? '') ?>" required>
                <span class="input-unit__text">Cuốn</span>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Nhà xuất bản</label>
              <input class="form-control" type="text" name="publisher" value="<?= h($editProduct['Publisher'] ?? 'AlphaBooks') ?>">
            </div>
            <div class="form-group">
              <label class="form-label">Trạng thái</label>
              <select class="form-control" name="status">
                <?php foreach (['Còn hàng', 'Hết hàng'] as $status): ?>
                  <option value="<?= h($status) ?>" <?= ($editProduct['Status'] ?? 'Còn hàng') === $status ? 'selected' : '' ?>><?= h($status) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group" style="width: 100%;">
              <label class="form-label">Mô tả sản phẩm</label>
              <textarea class="form-control" name="description" rows="3"><?= h($editProduct['Description'] ?? '') ?></textarea>
            </div>
          </div>
          <div class="form-row" style="gap: 20px; align-items: flex-end;">
            <?php if ($editProductImage): ?>
              <div class="form-group" style="flex: 0 0 auto;">
                <label class="form-label">Ảnh bìa hiện tại</label>
                <?php $previewSrc = getProductImage($editProductImage); ?>
                <img src="<?= $previewSrc ?>" style="max-height: 100px; max-width: 150px; object-fit: contain; border-radius: 4px; border: 1px solid var(--color-border); display: block; background: #f9f9f9; padding: 4px;">
              </div>
            <?php endif; ?>
            <div class="form-group" style="flex: 1;">
              <label class="form-label">Ảnh bìa sản phẩm (Tải lên Cloudinary)</label>
              <input class="form-control" type="file" name="product_image" accept="image/*">
              <small style="color: var(--color-text-light); margin-top: 4px; display: block;">Nếu không chọn ảnh bìa mới, ảnh cũ (nếu có) sẽ được giữ nguyên.</small>
            </div>
          </div>
          <div class="btn-group">
            <button class="btn btn--primary" type="submit"><?= $editProduct ? 'Cập nhật sản phẩm' : 'Thêm sản phẩm' ?></button>
            <?php if ($editProduct): ?><a class="btn btn--ghost" href="products.php">Hủy sửa</a><?php endif; ?>
          </div>
        </div>
      </form>

      <div class="table-wrapper">
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Tên sách</th>
              <th>Danh mục</th>
              <th>Giá bán</th>
              <th>Tồn kho</th>
              <th>Trạng thái</th>
              <th>Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $product): ?>
              <tr>
                <td><?= h($product['ProductID']) ?></td>
                <td><strong><?= h($product['ProductName']) ?></strong><br><small style="color:var(--color-text-light);">NXB: <?= h($product['Publisher']) ?></small></td>
                <td><?= h($product['CategoryName'] ?? 'Không có') ?></td>
                <td><?= number_format($product['Price'], 0, ',', '.') ?> đ</td>
                <td><?= h($product['Quantity']) ?></td>
                <td><span class="badge <?= badgeClass($product['Status']) ?>"><?= h($product['Status']) ?></span></td>
                <td class="table__actions">
                  <a class="btn btn--sm btn--outline" href="products.php?edit=<?= h($product['ProductID']) ?>">Sửa</a>
                  <form method="post" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')" style="display:inline">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= h($product['ProductID']) ?>">
                    <button class="btn btn--sm btn--danger" type="submit">Xóa</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</body>
</html>
