<?php
$pageTitle = 'Sửa sản phẩm';
$error = '';

require_once '/var/www/src/config/database.php';

$productID = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($productID <= 0) {
    header('Location: /admin/products/');
    exit;
}

// Khai báo biến mặc định
$productCode = '';
$productName = '';
$categoryID = 0;
$supplierID = 0;
$unit = '';
$price = 0;
$stock = 0;
$status = 1;
$image = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productCode = trim($_POST['ProductCode'] ?? '');
    $productName = trim($_POST['ProductName'] ?? '');
    $categoryID = (int)($_POST['CategoryID'] ?? 0);
    $supplierID = (int)($_POST['SupplierID'] ?? 0);
    $unit = trim($_POST['Unit'] ?? '');
    $price = (float)($_POST['Price'] ?? 0);
    $stock = (int)($_POST['Stock'] ?? 0);
    $status = (int)($_POST['Status'] ?? 1);

    if ($productCode === '' || $productName === '') {
        $error = 'Vui lòng nhập đầy đủ mã sản phẩm và tên sản phẩm.';
    } else {
        // Cập nhật thông tin sản phẩm
        $sql = "UPDATE products 
                SET ProductCode = ?, ProductName = ?, CategoryID = ?, SupplierID = ?, Unit = ?, Price = ?, Stock = ?, Status = ?
                WHERE ProductID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssiisdiii', $productCode, $productName, $categoryID, $supplierID, $unit, $price, $stock, $status, $productID);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: /admin/products/');
            exit;
        } else {
            $error = 'Không thể cập nhật sản phẩm.';
        }
        $stmt->close();
    }
} else {
    // Lấy thông tin sản phẩm hiện tại
    $sql = "SELECT * FROM products WHERE ProductID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $productID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($product = $result->fetch_assoc()) {
        $productCode = $product['ProductCode'];
        $productName = $product['ProductName'];
        $categoryID = $product['CategoryID'];
        $supplierID = $product['SupplierID'];
        $unit = $product['Unit'];
        $price = $product['Price'];
        $stock  = $product['Stock'] ?? 0;
        $status = $product['Status'] ?? 1;
    } else {
        $stmt->close();
        header('Location: /admin/products/');
        exit;
    }
    $stmt->close();
}

// Lấy danh sách danh mục & nhà cung cấp cho ô chọn (Select)
$categories = $conn->query("SELECT CategoryID, CategoryName FROM categories ORDER BY CategoryName ASC");
$suppliers = $conn->query("SELECT SupplierID, SupplierName FROM suppliers ORDER BY SupplierName ASC");

require_once '/var/www/src/includes/admin/header.php';
require_once '/var/www/src/includes/admin/navbar.php';
?>

<div class="container mt-4 mb-5">
    <h2 class="mb-4">Sửa thông tin sản phẩm</h2>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="ProductCode" class="form-label">Mã sản phẩm</label>
                <input type="text" class="form-control" id="ProductCode" name="ProductCode" value="<?= htmlspecialchars($productCode) ?>" required>
            </div>
            <div class="col-md-8 mb-3">
                <label for="ProductName" class="form-label">Tên sản phẩm</label>
                <input type="text" class="form-control" id="ProductName" name="ProductName" value="<?= htmlspecialchars($productName) ?>" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="CategoryID" class="form-label">Danh mục</label>
                <select class="form-select" id="CategoryID" name="CategoryID">
                    <option value="0">-- Chọn danh mục --</option>
                    <?php if ($categories): ?>
                        <?php while ($cat = $categories->fetch_assoc()): ?>
                            <option value="<?= $cat['CategoryID'] ?>" <?= $cat['CategoryID'] == $categoryID ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['CategoryName']) ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label for="SupplierID" class="form-label">Nhà cung cấp</label>
                <select class="form-select" id="SupplierID" name="SupplierID">
                    <option value="0">-- Chọn nhà cung cấp --</option>
                    <?php if ($suppliers): ?>
                        <?php while ($sup = $suppliers->fetch_assoc()): ?>
                            <option value="<?= $sup['SupplierID'] ?>" <?= $sup['SupplierID'] == $supplierID ? 'selected' : '' ?>>
                                <?= htmlspecialchars($sup['SupplierName']) ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3 mb-3">
                <label for="Unit" class="form-label">Đơn vị</label>
                <input type="text" class="form-control" id="Unit" name="Unit" value="<?= htmlspecialchars($unit) ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label for="Price" class="form-label">Giá bán (VNĐ)</label>
                <input type="number" step="0.01" class="form-control" id="Price" name="Price" value="<?= htmlspecialchars($price) ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label for="Stock" class="form-label">Tồn kho</label>
                <input type="number" class="form-control" id="Stock" name="Stock" value="<?= htmlspecialchars($stock) ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label for="Status" class="form-label">Trạng thái</label>
                <select class="form-select" id="Status" name="Status">
                    <option value="1" <?= $status == 1 ? 'selected' : '' ?>>Đang bán</option>
                    <option value="0" <?= $status == 0 ? 'selected' : '' ?>>Ngừng bán</option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-warning">Cập nhật sản phẩm</button>
        <a href="/admin/products/" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php
if (isset($conn) && $conn) {
    $conn->close();
}
require_once '/var/www/src/includes/admin/footer.php';
?>