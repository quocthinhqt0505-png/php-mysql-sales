<?php

require_once '/var/www/src/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/products/');
    exit;
}

$productID = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($productID <= 0) {
    header('Location: /admin/products/');
    exit;
}

try {
    // 1. Lấy danh sách file ảnh của sản phẩm để xóa khỏi ổ đĩa sau khi xóa DB
    $sqlImages = "SELECT ImageFile FROM product_images WHERE ProductID = ?";
    $stmtImg = $conn->prepare($sqlImages);
    $stmtImg->bind_param('i', $productID);
    $stmtImg->execute();
    $resImages = $stmtImg->get_result();
    
    $filesToDelete = [];
    while ($row = $resImages->fetch_assoc()) {
        $filesToDelete[] = $row['ImageFile'];
    }
    $stmtImg->close();

    // 2. Tiến hành xóa sản phẩm (Ràng buộc ON DELETE CASCADE trong DB sẽ tự xóa bản ghi ở product_images)
    $sql = "DELETE FROM products WHERE ProductID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $productID);

    if ($stmt->execute()) {
        $stmt->close();

        // 3. Xóa các file ảnh vật lý trên server nếu xóa DB thành công
        $uploadDir = '/var/www/html/uploads/products/';
        foreach ($filesToDelete as $fileName) {
            $filePath = $uploadDir . $fileName;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $conn->close();
        header('Location: /admin/products/');
        exit;
    }
} catch (mysqli_sql_exception $e) {
    // Mã lỗi 1451: Lỗi ràng buộc khóa ngoại (Sản phẩm đã có trong đơn hàng)
    if ($e->getCode() === 1451) {
        $message = 'Không thể xóa sản phẩm này vì đã có trong lịch sử đơn hàng!';
    } else {
        $message = 'Lỗi hệ thống: Không thể xóa sản phẩm.';
    }

    if (isset($stmt) && $stmt) {
        $stmt->close();
    }
    $conn->close();

    echo "<script>
            alert('{$message}');
            window.location.href = '/admin/products/';
          </script>";
    exit;
}