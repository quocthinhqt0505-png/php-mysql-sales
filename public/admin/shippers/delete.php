<?php
require_once '/var/www/src/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/shippers/');
    exit;
}

$shipperID = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($shipperID > 0) {
    try {
        $sql = "DELETE FROM shippers WHERE ShipperID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $shipperID);
        $stmt->execute();
        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        $conn->close();
        echo "<script>alert('Không thể xóa shipper này vì đã có trong đơn hàng!'); window.location.href='/admin/shippers/';</script>";
        exit;
    }
}

if (isset($conn) && $conn) { $conn->close(); }
header('Location: /admin/shippers/');
exit;