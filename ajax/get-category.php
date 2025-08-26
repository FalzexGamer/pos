<?php
include '../include/conn.php';
include '../include/session.php';

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'Category ID is required']);
    exit;
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM categories WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $category = mysqli_fetch_assoc($result);
    
    if ($category) {
        echo json_encode($category);
    } else {
        echo json_encode(['error' => 'Category not found']);
    }
} else {
    echo json_encode(['error' => 'Database error: ' . mysqli_error($conn)]);
}

mysqli_stmt_close($stmt);
?>
