<?php
include '../include/conn.php';
include '../include/session.php';

$id = $_POST['id'] ?? null;
$name = trim($_POST['name']);
$abbreviation = trim($_POST['abbreviation'] ?? '');
$description = trim($_POST['description'] ?? '');
$is_active = isset($_POST['is_active']) ? 1 : 0;

// Validate required fields
if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'UOM name is required']);
    exit;
}

// Check if name already exists (excluding current record if editing)
$check_sql = "SELECT id FROM uom WHERE name = ? AND id != ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
$check_id = $id ?: 0;
mysqli_stmt_bind_param($check_stmt, "si", $name, $check_id);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);

if (mysqli_fetch_array($check_result)) {
    echo json_encode(['success' => false, 'message' => 'UOM name already exists']);
    exit;
}

if ($id) {
    // Update existing UOM
    $sql = "UPDATE uom SET name = ?, abbreviation = ?, description = ?, is_active = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssii", $name, $abbreviation, $description, $is_active, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'UOM updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error occurred']);
    }
} else {
    // Insert new UOM
    $sql = "INSERT INTO uom (name, abbreviation, description, is_active) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $name, $abbreviation, $description, $is_active);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'UOM created successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error occurred']);
    }
}

mysqli_stmt_close($check_stmt);
mysqli_stmt_close($stmt);
?>
