<?php
include '../include/conn.php';
include '../include/session.php';

$name = trim($_POST['name'] ?? '');
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

// Check if name already exists
$check_sql = "SELECT id FROM suppliers WHERE name = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);

if ($check_stmt) {
    mysqli_stmt_bind_param($check_stmt, "s", $name);
    mysqli_stmt_execute($check_stmt);
    
    // Use bind_result instead of get_result for better compatibility
    $existing_id = null;
    mysqli_stmt_bind_result($check_stmt, $existing_id);
    
    if (mysqli_stmt_fetch($check_stmt)) {
        mysqli_stmt_close($check_stmt);
        echo json_encode(['success' => false, 'message' => 'Supplier name already exists']);
        exit;
    }
    mysqli_stmt_close($check_stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
    exit;
}

// Insert new supplier
$sql = "INSERT INTO suppliers (name, contact_person, phone, email, address, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sssssi", $name, $contact_person, $phone, $email, $address, $is_active);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'message' => 'Supplier created successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}

mysqli_stmt_close($stmt);
?>
