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
    
    // Use bind_result instead of get_result for better compatibility
    $id_result = $name = $description = $is_active = $created_at = $updated_at = null;
    mysqli_stmt_bind_result($stmt, $id_result, $name, $description, $is_active, $created_at, $updated_at);
    
    if (mysqli_stmt_fetch($stmt)) {
        $category = [
            'id' => $id_result,
            'name' => $name,
            'description' => $description,
            'is_active' => $is_active,
            'created_at' => $created_at,
            'updated_at' => $updated_at
        ];
        echo json_encode($category);
    } else {
        echo json_encode(['error' => 'Category not found']);
    }
} else {
    echo json_encode(['error' => 'Database error: ' . mysqli_error($conn)]);
}

mysqli_stmt_close($stmt);
?>
