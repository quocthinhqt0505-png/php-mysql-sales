<?php
$pageTitle = 'Thêm nhân viên giao hàng';
$error = '';
require_once '/var/www/src/config/database.php';

$shipperName = '';
$phone = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shipperName = trim($_POST['ShipperName'] ?? '');
    $phone = trim($_POST['Phone'] ?? '');

    if ($shipperName === '') {
        $error = 'Vui lòng nhập tên nhân viên.';
    } else {
        $sql = "INSERT INTO shippers (ShipperName, Phone) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $shipperName, $phone);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: /admin/shippers/');
            exit;
        } else {
            $error = 'Không thể thêm nhân viên.';
        }
        $stmt->close();
    }
}

require_once '/var/www/src/includes/admin/header.php';
require_once '/var/www/src/includes/admin/navbar.php';
?>

<div class="container mt-4 mb-5">
    <h2 class="mb-4">Thêm shipper mới</h2>
    <?php if ($error !== ''): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="ShipperName" class="form-label">Tên nhân viên giao hàng</label>
            <input type="text" class="form-control" id="ShipperName" name="ShipperName" value="<?= htmlspecialchars($shipperName) ?>" required>
        </div>
        <div class="mb-3">
            <label for="Phone" class="form-label">Số điện thoại</label>
            <input type="text" class="form-control" id="Phone" name="Phone" value="<?= htmlspecialchars($phone) ?>">
        </div>
        <button type="submit" class="btn btn-primary">Lưu</button>
        <a href="/admin/shippers/" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php
if (isset($conn) && $conn) { $conn->close(); }
require_once '/var/www/src/includes/admin/footer.php';
?>