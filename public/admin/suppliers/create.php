<?php
$pageTitle = 'Thêm nhà cung cấp';
$error = '';
require_once '/var/www/src/config/database.php';

$supplierName = '';
$contactName = '';
$phone = '';
$address = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplierName = trim($_POST['SupplierName'] ?? '');
    $contactName = trim($_POST['ContactName'] ?? '');
    $phone = trim($_POST['Phone'] ?? '');
    $address = trim($_POST['Address'] ?? '');

    if ($supplierName === '') {
        $error = 'Vui lòng nhập tên nhà cung cấp.';
    } else {
        $sql = "INSERT INTO suppliers (SupplierName, ContactName, Phone, Address) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssss', $supplierName, $contactName, $phone, $address);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: /admin/suppliers/');
            exit;
        } else {
            $error = 'Không thể thêm nhà cung cấp.';
        }
        $stmt->close();
    }
}

require_once '/var/www/src/includes/admin/header.php';
require_once '/var/www/src/includes/admin/navbar.php';
?>

<div class="container mt-4 mb-5">
    <h2 class="mb-4">Thêm nhà cung cấp mới</h2>
    <?php if ($error !== ''): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="SupplierName" class="form-label">Tên nhà cung cấp</label>
            <input type="text" class="form-control" id="SupplierName" name="SupplierName" value="<?= htmlspecialchars($supplierName) ?>" required>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="ContactName" class="form-label">Người liên hệ</label>
                <input type="text" class="form-control" id="ContactName" name="ContactName" value="<?= htmlspecialchars($contactName) ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="Phone" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control" id="Phone" name="Phone" value="<?= htmlspecialchars($phone) ?>">
            </div>
        </div>
        <div class="mb-3">
            <label for="Address" class="form-label">Địa chỉ</label>
            <input type="text" class="form-control" id="Address" name="Address" value="<?= htmlspecialchars($address) ?>">
        </div>
        <button type="submit" class="btn btn-primary">Lưu nhà cung cấp</button>
        <a href="/admin/suppliers/" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php
if (isset($conn) && $conn) { $conn->close(); }
require_once '/var/www/src/includes/admin/footer.php';
?>