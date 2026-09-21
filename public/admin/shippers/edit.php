<?php
$pageTitle = 'Sửa nhân viên giao hàng';
$error = '';
require_once '/var/www/src/config/database.php';

$shipperID = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($shipperID <= 0) {
    header('Location: /admin/shippers/');
    exit;
}

$shipperName = '';
$phone = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shipperName = trim($_POST['ShipperName'] ?? '');
    $phone = trim($_POST['Phone'] ?? '');

    if ($shipperName === '') {
        $error = 'Vui lòng nhập tên nhân viên.';
    } else {
        $sql = "UPDATE shippers SET ShipperName = ?, Phone = ? WHERE ShipperID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssi', $shipperName, $phone, $shipperID);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: /admin/shippers/');
            exit;
        } else {
            $error = 'Không thể cập nhật.';
        }
        $stmt->close();
    }
} else {
    $sql = "SELECT ShipperName, Phone FROM shippers WHERE ShipperID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $shipperID);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($shipper = $res->fetch_assoc()) {
        $shipperName = $shipper['ShipperName'];
        $phone = $shipper['Phone'];
    } else {
        $stmt->close();
        header('Location: /admin/shippers/');
        exit;
    }
    $stmt->close();
}

require_once '/var/www/src/includes/admin/header.php';
require_once '/var/www/src/includes/admin/navbar.php';
?>

<div class="container mt-4 mb-5">
    <h2 class="mb-4">Sửa thông tin shipper</h2>
    <?php if ($error !== ''): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="ShipperName" class="form-label">Tên shipper</label>
            <input type="text" class="form-control" id="ShipperName" name="ShipperName" value="<?= htmlspecialchars($shipperName) ?>" required>
        </div>
        <div class="mb-3">
            <label for="Phone" class="form-label">Số điện thoại</label>
            <input type="text" class="form-control" id="Phone" name="Phone" value="<?= htmlspecialchars($phone) ?>">
        </div>
        <button type="submit" class="btn btn-warning">Cập nhật</button>
        <a href="/admin/shippers/" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php
if (isset($conn) && $conn) { $conn->close(); }
require_once '/var/www/src/includes/admin/footer.php';
?>