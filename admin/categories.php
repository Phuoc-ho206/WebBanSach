<?php
require_once 'data.php';
require_once 'partials.php';

$editCategory = null;
if (isset($_GET['edit'])) {
  $stmt = $conn->prepare("SELECT * FROM category WHERE CategoryID = ?");
  $stmt->bind_param("i", $_GET['edit']);
  $stmt->execute();
  $editCategory = $stmt->get_result()->fetch_assoc();
  $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'save') {
    $id = $_POST['id'] !== '' ? (int) $_POST['id'] : null;
    $name = trim($_POST['name']);
    $description = trim($_POST['description'] ?? '');

    if ($id) {
      $stmt = $conn->prepare("UPDATE category SET CategoryName = ?, Description = ? WHERE CategoryID = ?");
      $stmt->bind_param("ssi", $name, $description, $id);
      $stmt->execute();
      $stmt->close();
      write_user_log($conn, "Cập nhật danh mục ID " . $id . " - Tên: " . $name);
    } else {
      $stmt = $conn->prepare("INSERT INTO category (CategoryName, Description) VALUES (?, ?)");
      $stmt->bind_param("ss", $name, $description);
      $stmt->execute();
      $newId = $stmt->insert_id;
      $stmt->close();
      write_user_log($conn, "Thêm mới danh mục ID " . $newId . " - Tên: " . $name);
    }
  }

  if ($action === 'delete') {
    $id = (int)$_POST['id'];
    $stmt = $conn->prepare("DELETE FROM category WHERE CategoryID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    write_user_log($conn, "Xóa danh mục ID " . $id);
  }

  redirectTo('categories.php');
}

// Lấy danh sách danh mục và số lượng sản phẩm liên kết
$categories = [];
$res = $conn->query("
  SELECT c.CategoryID, c.CategoryName, c.Description, COUNT(p.ProductID) AS ProductCount 
  FROM category c 
  LEFT JOIN product p ON c.CategoryID = p.CategoryID 
  GROUP BY c.CategoryID 
  ORDER BY c.CategoryID DESC
");
if ($res) {
  while ($row = $res->fetch_assoc()) {
    $categories[] = $row;
  }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản lý danh mục</title><?= adminCssLinks() ?>
</head>
<body>
  <div class="app-layout">
    <?php adminSidebar(); ?>
    <main class="page-content">
      <header class="page-header"><div><h1>Quản lý danh mục</h1><p>Thêm, sửa, xóa danh mục sách</p></div></header>
      <form method="post" class="card">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= h($editCategory['CategoryID'] ?? '') ?>">
        <div class="card__body form">
          <div class="form-row">
            <div class="form-group" style="flex: 1;">
              <label class="form-label">Tên danh mục</label>
              <input class="form-control" type="text" name="name" value="<?= h($editCategory['CategoryName'] ?? '') ?>" required>
            </div>
            <div class="form-group" style="flex: 2;">
              <label class="form-label">Mô tả</label>
              <input class="form-control" type="text" name="description" value="<?= h($editCategory['Description'] ?? '') ?>">
            </div>
          </div>
          <div class="btn-group">
            <button class="btn btn--primary" type="submit"><?= $editCategory ? 'Cập nhật danh mục' : 'Thêm danh mục' ?></button>
            <?php if ($editCategory): ?><a class="btn btn--ghost" href="categories.php">Hủy sửa</a><?php endif; ?>
          </div>
        </div>
      </form>
      <div class="table-wrapper">
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Tên danh mục</th>
              <th>Mô tả</th>
              <th>Sản phẩm liên kết</th>
              <th>Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($categories as $category): ?>
              <tr>
                <td><?= h($category['CategoryID']) ?></td>
                <td><strong><?= h($category['CategoryName']) ?></strong></td>
                <td><?= h($category['Description'] ?? '') ?></td>
                <td><span class="badge badge--info"><?= h($category['ProductCount']) ?> sản phẩm</span></td>
                <td class="table__actions">
                  <a class="btn btn--sm btn--outline" href="categories.php?edit=<?= h($category['CategoryID']) ?>">Sửa</a>
                  <form method="post" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?')" style="display: inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= h($category['CategoryID']) ?>">
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
