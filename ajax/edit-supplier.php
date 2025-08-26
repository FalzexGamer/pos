<?php
include '../include/conn.php';
include '../include/session.php';

$id = $_POST['id'] ?? null;
$name = trim($_POST['name']);
$contact_person = trim($_POST['contact_person'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$address = trim($_POST['address'] ?? '');
$is_active = isset($_POST['is_active']) ? 1 : 0;

// Validate required fields
if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Supplier name is required']);
    exit;
}

// Check if name already exists (excluding current record if editing)
$check_sql = "SELECT id FROM suppliers WHERE name = ? AND id != ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
$check_id = $id ?: 0;
mysqli_stmt_bind_param($check_stmt, "si", $name, $check_id);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);

if (mysqli_fetch_assoc($check_result)) {
    echo json_encode(['success' => false, 'message' => 'Supplier name already exists']);
    exit;
}

// Update existing supplier
$sql = "UPDATE suppliers SET name = ?, contact_person = ?, phone = ?, email = ?, address = ?, is_active = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sssssii", $name, $contact_person, $phone, $email, $address, $is_active, $id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'message' => 'Supplier updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}

mysqli_stmt_close($check_stmt);
mysqli_stmt_close($stmt);
?>
