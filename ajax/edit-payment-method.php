<?php
include '../include/conn.php';
include '../include/session.php';

$id = $_POST['id'] ?? null;
$name = $_POST['name'] ?? '';
$description = $_POST['description'] ?? '';
$is_active = $_POST['is_active'] ?? 1;

if (!$id || empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Payment method ID and name are required']);
    exit;
}

// Check if name already exists (excluding current record)
$checkQuery = mysqli_query($conn, "SELECT id FROM payment_methods WHERE name = '" . mysqli_real_escape_string($conn, $name) . "' AND id != $id");
if (mysqli_num_rows($checkQuery) > 0) {
    echo json_encode(['success' => false, 'message' => 'Payment method name already exists']);
    exit;
}

$name_escaped = mysqli_real_escape_string($conn, $name);
$description_escaped = mysqli_real_escape_string($conn, $description);

$query = mysqli_query($conn, "
    UPDATE payment_methods 
    SET name = '$name_escaped', 
        description = '$description_escaped', 
        is_active = $is_active,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = $id
");

if ($query) {
    echo json_encode(['success' => true, 'message' => 'Payment method updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update payment method']);
}
?>