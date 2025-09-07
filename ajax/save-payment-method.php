<?php
include '../include/conn.php';
include '../include/session.php';

$name = $_POST['name'] ?? '';
$description = $_POST['description'] ?? '';
$is_active = $_POST['is_active'] ?? 1;

if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Payment method name is required']);
    exit;
}

// Check if name already exists
$checkQuery = mysqli_query($conn, "SELECT id FROM payment_methods WHERE name = '" . mysqli_real_escape_string($conn, $name) . "'");
if (mysqli_num_rows($checkQuery) > 0) {
    echo json_encode(['success' => false, 'message' => 'Payment method name already exists']);
    exit;
}

$name_escaped = mysqli_real_escape_string($conn, $name);
$description_escaped = mysqli_real_escape_string($conn, $description);

$query = mysqli_query($conn, "
    INSERT INTO payment_methods (name, description, is_active) 
    VALUES ('$name_escaped', '$description_escaped', $is_active)
");

if ($query) {
    echo json_encode(['success' => true, 'message' => 'Payment method added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add payment method']);
}
?>