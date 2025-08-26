<?php
include '../include/conn.php';
include '../include/session.php';

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$id = $_POST['id'] ?? null;
$name = trim($_POST['name'] ?? '');
$abbreviation = trim($_POST['abbreviation'] ?? '');
$description = trim($_POST['description'] ?? '');
$is_active = isset($_POST['is_active']) ? 1 : 0;

// Validate required fields
if (empty($id)) {
    echo json_encode(['success' => false, 'message' => 'UOM ID is required']);
    exit;
}

if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'UOM name is required']);
    exit;
}

// Validate ID is numeric
if (!is_numeric($id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid UOM ID']);
    exit;
}

// Check if UOM exists
$check_sql = "SELECT id FROM uom WHERE id = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "i", $id);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);

if (!mysqli_fetch_array($check_result)) {
    echo json_encode(['success' => false, 'message' => 'UOM not found']);
    exit;
}

// Check if name already exists (excluding current record)
$name_check_sql = "SELECT id FROM uom WHERE name = ? AND id != ?";
$name_check_stmt = mysqli_prepare($conn, $name_check_sql);
mysqli_stmt_bind_param($name_check_stmt, "si", $name, $id);
mysqli_stmt_execute($name_check_stmt);
$name_check_result = mysqli_stmt_get_result($name_check_stmt);

if (mysqli_fetch_array($name_check_result)) {
    echo json_encode(['success' => false, 'message' => 'UOM name already exists']);
    exit;
}

// Update UOM
$sql = "UPDATE uom SET name = ?, abbreviation = ?, description = ?, is_active = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sssii", $name, $abbreviation, $description, $is_active, $id);

if (mysqli_stmt_execute($stmt)) {
    if (mysqli_affected_rows($conn) > 0) {
        echo json_encode(['success' => true, 'message' => 'UOM updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No changes were made']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Database error occurred: ' . mysqli_error($conn)]);
}

mysqli_stmt_close($check_stmt);
mysqli_stmt_close($name_check_stmt);
mysqli_stmt_close($stmt);
?>
