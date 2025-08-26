<?php
include '../include/conn.php';

$id = $_GET['id'] ?? 0;

if (!$id) {
    echo json_encode(['error' => 'Product ID required']);
    exit;
}

// Use prepared statement for security
$stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_array($result);

if (!$product) {
    echo json_encode(['error' => 'Product not found']);
    exit;
}

echo json_encode($product);
mysqli_stmt_close($stmt);
?>
