<?php
include '../include/conn.php';

$id = $_GET['id'] ?? 0;

if (!$id) {
    echo json_encode(['error' => 'Product ID required']);
    exit;
}

// Use proper escaping for security
$escaped_id = mysqli_real_escape_string($conn, $id);
$sql = "SELECT * FROM products WHERE id = '$escaped_id'";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode(['error' => 'Database error']);
    exit;
}

$product = mysqli_fetch_array($result);

if (!$product) {
    echo json_encode(['error' => 'Product not found']);
    exit;
}

echo json_encode($product);
?>
