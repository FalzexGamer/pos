<?php
include '../include/conn.php';

$id = $_GET['id'] ?? 0;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Product ID required']);
    exit;
}

// Check if product exists
$check = mysqli_query($conn, "SELECT id FROM products WHERE id = $id");
if (mysqli_num_rows($check) == 0) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}

// Check if product is used in sales
$check_sales = mysqli_query($conn, "SELECT id FROM sale_items WHERE product_id = $id LIMIT 1");
if (mysqli_num_rows($check_sales) > 0) {
    echo json_encode(['success' => false, 'message' => 'Cannot delete product that has been sold']);
    exit;
}

// Delete product
$delete = mysqli_query($conn, "DELETE FROM products WHERE id = $id");

if ($delete) {
    echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete product']);
}
?>
