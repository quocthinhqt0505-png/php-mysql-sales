<?php

require_once '/var/www/src/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/categories/');
    exit;
}

$categoryID = isset($_POST['id'])
    ? (int) $_POST['id']
    : 0;

if ($categoryID <= 0) {
    header('Location: /admin/categories/');
    exit;
}

try {
    // 1. Kiểm tra xem danh mục có đang chứa sản phẩm nào không
    $sqlCheck = "SELECT COUNT(*) AS total FROM products WHERE CategoryID = ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param('i', $categoryID);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result()->fetch_assoc();
    $stmtCheck->close();

    if ($resultCheck['total'] > 0) {
        // Nếu có sản phẩm, không cho xóa và chuyển hướng kèm thông báo lỗi
        header('Location: /admin/categories/?error=has_products');
        exit;
    }

    // 2. Thực hiện xóa nếu không có ràng buộc sản phẩm
    $sql = "
        DELETE FROM categories
        WHERE CategoryID = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $categoryID);
    $stmt->execute();
    $stmt->close();

    header('Location: /admin/categories/?success=deleted');
    exit;

} catch (Throwable $e) {
    // Xử lý nếu xảy ra lỗi ngoài ý muốn
    header('Location: /admin/categories/?error=delete_failed');
    exit;
} finally {
    $conn->close();
}