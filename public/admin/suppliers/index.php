<?php
$pageTitle = 'Quản lý nhà cung cấp';
require_once '/var/www/src/config/database.php';

$sql = "SELECT * FROM suppliers ORDER BY SupplierID DESC";
$result = $conn->query($sql);

require_once '/var/www/src/includes/admin/header.php';
require_once '/var/www/src/includes/admin/navbar.php';
?>

<div class="container mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Quản lý nhà cung cấp</h2>
        <a href="/admin/suppliers/create.php" class="btn btn-primary">Thêm nhà cung cấp</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th style="width: 80px;" class="text-center">ID</th>
                    <th>Tên nhà cung cấp</th>
                    <th>Người liên hệ</th>
                    <th>Số điện thoại</th>
                    <th>Địa chỉ</th>
                    <th style="width: 150px;" class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($sup = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="text-center"><?= $sup['SupplierID'] ?></td>
                        <td><?= htmlspecialchars($sup['SupplierName']) ?></td>
                        <td><?= htmlspecialchars($sup['ContactName'] ?? '') ?></td>
                        <td><?= htmlspecialchars($sup['Phone'] ?? '') ?></td>
                        <td><?= htmlspecialchars($sup['Address'] ?? '') ?></td>
                        <td class="text-center">
                            <a href="/admin/suppliers/edit.php?id=<?= $sup['SupplierID'] ?>" class="btn btn-sm btn-warning">Sửa</a>
                            <form action="/admin/suppliers/delete.php" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa?');">
                                <input type="hidden" name="id" value="<?= $sup['SupplierID'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center">Chưa có dữ liệu nhà cung cấp.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
if (isset($conn) && $conn) { $conn->close(); }
require_once '/var/www/src/includes/admin/footer.php';
?>