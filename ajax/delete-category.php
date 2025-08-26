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
    $check_result = mysqli_stmt_get_result($check_stmt);
    $result = mysqli_fetch_assoc($check_result);
    
    if ($result['count'] > 0) {
        echo json_encode(['success' => false, 'message' => 'Cannot delete category. It is being used by ' . $result['count'] . ' product(s)']);
        exit;
    }
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

mysqli_stmt_close($check_stmt);
mysqli_stmt_close($stmt);
?>
