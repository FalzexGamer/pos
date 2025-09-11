<?php
include '../include/conn.php';
include '../include/session.php';

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Category ID is required']);
    exit;
}

$id = intval($_GET['id']);

// Check if category is being used by any products
$check_sql = "SELECT COUNT(*) as count FROM products WHERE category_id = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);

if ($check_stmt) {
    mysqli_stmt_bind_param($check_stmt, 'i', $id);
    mysqli_stmt_execute($check_stmt);
    
    // Use bind_result instead of get_result for better compatibility
    $count = null;
    mysqli_stmt_bind_result($check_stmt, $count);
    
    if (mysqli_stmt_fetch($check_stmt)) {
        if ($count > 0) {
            mysqli_stmt_close($check_stmt);
            echo json_encode(['success' => false, 'message' => 'Cannot delete category. It is being used by ' . $count . ' product(s)']);
            exit;
        }
    }
    mysqli_stmt_close($check_stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
    exit;
}

// Delete the category
$sql = "DELETE FROM categories WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    
    if (mysqli_affected_rows($conn) > 0) {
        echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Category not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
}

mysqli_stmt_close($stmt);
?>
