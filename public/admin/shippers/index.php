<?php
$pageTitle = 'Quản lý shipper giao hàng';
require_once '/var/www/src/config/database.php';

$sql = "SELECT * FROM shippers ORDER BY ShipperID DESC";
$result = $conn->query($sql);

require_once '/var/www/src/includes/admin/header.php';
require_once '/var/www/src/includes/admin/navbar.php';
?>

<div class="container mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Quản lý shipper</h2>
        <a href="/admin/shippers/create.php" class="btn btn-primary">Thêm nhân viên mới</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th style="width: 150px;">Mã nhân viên</th>
                    <th>Tên nhân viên giao hàng</th>
                    <th>Số điện thoại</th>
                    <th style="width: 150px;" class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($shipper = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $shipper['ShipperID'] ?></td>
                        <td><?= htmlspecialchars($shipper['ShipperName'] ?? '') ?></td>
                        <td><?= htmlspecialchars($shipper['Phone'] ?? '') ?></td>
                        <td class="text-center">
                            <a href="/admin/shippers/edit.php?id=<?= $shipper['ShipperID'] ?>" class="btn btn-sm btn-warning">Sửa</a>
                            <form action="/admin/shippers/delete.php" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa nhân viên này?');">
                                <input type="hidden" name="id" value="<?= $shipper['ShipperID'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" class="text-center">Chưa có dữ liệu.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
if (isset($conn) && $conn) { $conn->close(); }
require_once '/var/www/src/includes/admin/footer.php';
?>