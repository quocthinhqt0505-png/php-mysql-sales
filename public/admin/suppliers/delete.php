<?php
require_once '/var/www/src/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/suppliers/');
    exit;
}

$supplierID = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($supplierID > 0) {
    try {
        $sql = "DELETE FROM suppliers WHERE SupplierID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $supplierID);
        $stmt->execute();
        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        $conn->close();
        echo "<script>alert('Không thể xóa nhà cung cấp này vì đang cung cấp sản phẩm trong hệ thống!'); window.location.href='/admin/suppliers/';</script>";
        exit;
    }
}

if (isset($conn) && $conn) { $conn->close(); }
header('Location: /admin/suppliers/');
exit;