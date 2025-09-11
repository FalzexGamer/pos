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

if ($check_stmt) {
    mysqli_stmt_bind_param($check_stmt, "i", $id);
    mysqli_stmt_execute($check_stmt);
    
    // Use bind_result instead of get_result for better compatibility
    $count = null;
    mysqli_stmt_bind_result($check_stmt, $count);
    
    if (mysqli_stmt_fetch($check_stmt)) {
        if ($count > 0) {
            mysqli_stmt_close($check_stmt);
            echo json_encode(['success' => false, 'message' => 'Cannot delete supplier. It is being used by ' . $count . ' product(s)']);
            exit;
        }
    }
    mysqli_stmt_close($check_stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
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

mysqli_stmt_close($stmt);
?>
