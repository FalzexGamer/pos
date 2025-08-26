<?php
include '../include/conn.php';
include '../include/session.php';

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Supplier ID is required']);
    exit;
}

$id = $_GET['id'];

// Check if supplier is being used by any products
$check_sql = "SELECT COUNT(*) as count FROM products WHERE supplier_id = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "i", $id);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);
$result = mysqli_fetch_array($check_result);

if ($result['count'] > 0) {
    echo json_encode(['success' => false, 'message' => 'Cannot delete supplier. It is being used by ' . $result['count'] . ' product(s)']);
    exit;
}

// Delete the supplier
$sql = "DELETE FROM suppliers WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

if (mysqli_affected_rows($conn) > 0) {
    echo json_encode(['success' => true, 'message' => 'Supplier deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Supplier not found']);
}

mysqli_stmt_close($check_stmt);
mysqli_stmt_close($stmt);
?>
