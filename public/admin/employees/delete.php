<?php

require_once '/var/www/src/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/employees/');
    exit;
}

$employeeID = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($employeeID <= 0) {
    header('Location: /admin/employees/');
    exit;
}

try {
    // 1. Kiểm tra xem nhân viên này có đang liên kết với đơn hàng nào không
    $sqlCheck = "SELECT COUNT(*) AS total FROM orders WHERE EmployeeID = ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param('i', $employeeID);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result()->fetch_assoc();
    $stmtCheck->close();

    if ($resultCheck['total'] > 0) {
        // Nếu nhân viên đã có đơn hàng, không cho xóa để bảo toàn dữ liệu
        header('Location: /admin/employees/?error=has_orders');
        exit;
    }

    // 2. Lấy tên file ảnh đại diện để xóa khỏi ổ đĩa trước khi xóa dòng dữ liệu
    $sqlGetPhoto = "SELECT Photo FROM employees WHERE EmployeeID = ?";
    $stmtPhoto = $conn->prepare($sqlGetPhoto);
    $stmtPhoto->bind_param('i', $employeeID);
    $stmtPhoto->execute();
    $photoResult = $stmtPhoto->get_result()->fetch_assoc();
    $stmtPhoto->close();

    // 3. Thực hiện xóa dòng nhân viên trong CSDL
    $sql = "
        DELETE FROM employees
        WHERE EmployeeID = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $employeeID);
    $stmt->execute();
    $stmt->close();

    // 4. Nếu xóa DB thành công và nhân viên có ảnh, tiến hành xóa file ảnh trên server
    if (!empty($photoResult['Photo'])) {
        $filePath = '/var/www/html/uploads/employees/' . $photoResult['Photo'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    header('Location: /admin/employees/?success=deleted');
    exit;

} catch (Throwable $e) {
    header('Location: /admin/employees/?error=delete_failed');
    exit;
} finally {
    $conn->close();
}