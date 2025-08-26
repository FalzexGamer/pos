<?php
include '../include/conn.php';
include '../include/session.php';

$id = $_POST['id'] ?? null;
$name = trim($_POST['name']);
$description = trim($_POST['description'] ?? '');
$is_active = isset($_POST['is_active']) ? 1 : 0;

// Validate required fields
if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Category name is required']);
    exit;
}

// Check if name already exists (excluding current record if editing)
$check_sql = "SELECT id FROM categories WHERE name = ? AND id != ?";
$check_stmt = mysqli_prepare($conn, $check_sql);

if ($check_stmt) {
    $check_id = $id ?: 0;
    mysqli_stmt_bind_param($check_stmt, 'si', $name, $check_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_fetch_assoc($check_result)) {
        echo json_encode(['success' => false, 'message' => 'Category name already exists']);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
    exit;
}

if ($id) {
    // Update existing category
    $sql = "UPDATE categories SET name = ?, description = ?, is_active = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ssii', $name, $description, $is_active, $id);
        mysqli_stmt_execute($stmt);
        
        if (mysqli_affected_rows($conn) > 0) {
            echo json_encode(['success' => true, 'message' => 'Category updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No changes made to category']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
    }
} else {
    // Insert new category
    $sql = "INSERT INTO categories (name, description, is_active) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ssi', $name, $description, $is_active);
        mysqli_stmt_execute($stmt);
        
        if (mysqli_affected_rows($conn) > 0) {
            echo json_encode(['success' => true, 'message' => 'Category created successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create category']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
    }
}

mysqli_stmt_close($check_stmt);
if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}
?>
