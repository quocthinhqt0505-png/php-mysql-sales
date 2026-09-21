<?php

$pageTitle = 'Quản lý nhân viên';

require_once '/var/www/src/config/database.php';

// Truy vấn lấy dữ liệu từ bảng employees
$sql = "
    SELECT
        EmployeeID,
        LastName,
        FirstName,
        BirthDate,
        Photo,
        Notes
    FROM employees
    ORDER BY EmployeeID DESC
";

$result = $conn->query($sql);

require_once '/var/www/src/includes/admin/header.php';
require_once '/var/www/src/includes/admin/navbar.php';

?>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Quản lý nhân viên</h2>

        <a href="/admin/employees/create.php" class="btn btn-primary">
            Thêm nhân viên
        </a>
    </div>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'has_orders'): ?>
        <div class="alert alert-danger">
            Không thể xóa nhân viên này vì đang chịu trách nhiệm cho các đơn hàng trong hệ thống!
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['success']) && $_GET['success'] === 'deleted'): ?>
        <div class="alert alert-success">
            Đã xóa thông tin nhân viên thành công.
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Ảnh</th>
                    <th>Họ và Tên</th>
                    <th>Ngày sinh</th>
                    <th>Ghi chú</th>
                    <th>Thao tác</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($employee = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($employee['EmployeeID']) ?></td>

                            <!-- Xử lý hiển thị Ảnh -->
                            <td class="text-center">
                                <?php if (!empty($employee['Photo'])): ?>
                                    <img 
                                        src="/uploads/employees/<?= htmlspecialchars($employee['Photo']) ?>" 
                                        alt="Avatar" 
                                        style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;"
                                    >
                                <?php else: ?>
                                    <span class="text-muted fst-italic">Không có</span>
                                <?php endif; ?>
                            </td>

                            <!-- Nối LastName và FirstName -->
                            <td>
                                <?= htmlspecialchars($employee['LastName'] . ' ' . $employee['FirstName']) ?>
                            </td>

                            <!-- Định dạng lại Ngày sinh sang dd/mm/yyyy -->
                            <td>
                                <?= !empty($employee['BirthDate']) ? date('d/m/Y', strtotime($employee['BirthDate'])) : '' ?>
                            </td>

                            <!-- Cắt bớt Ghi chú nếu quá dài -->
                            <td>
                                <?php 
                                    $notes = htmlspecialchars($employee['Notes'] ?? '');
                                    echo mb_strimwidth($notes, 0, 50, '...'); 
                                ?>
                            </td>

                            <td>
                                <a
                                    href="/admin/employees/edit.php?id=<?= $employee['EmployeeID'] ?>"
                                    class="btn btn-sm btn-warning"
                                >
                                    Sửa
                                </a>

                                <form
                                    action="/admin/employees/delete.php"
                                    method="post"
                                    class="d-inline"
                                    onsubmit="return confirm('Bạn có chắc muốn xóa nhân viên này?');"
                                >
                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= $employee['EmployeeID'] ?>"
                                    >

                                    <button type="submit" class="btn btn-sm btn-danger">
                                        Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">
                            Chưa có nhân viên.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php

require_once '/var/www/src/includes/admin/footer.php';
$conn->close();