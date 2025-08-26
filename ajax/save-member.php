<?php
include '../include/conn.php';

// Get form data
$member_code = $_POST['member_code'] ?? '';
$name = $_POST['name'] ?? '';
$phone = $_POST['phone'] ?? '';
$email = $_POST['email'] ?? '';
$address = $_POST['address'] ?? '';
$membership_tier_id = $_POST['membership_tier_id'] ?? '';
$is_active = isset($_POST['is_active']) ? 1 : 0;

// Validation
if (empty($member_code) || empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Member Code and Name are required']);
    exit;
}

// Check if member code already exists
$stmt_check = mysqli_prepare($conn, "SELECT id FROM members WHERE member_code = ?");
mysqli_stmt_bind_param($stmt_check, "s", $member_code);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);

if (mysqli_num_rows($result_check) > 0) {
    echo json_encode(['success' => false, 'message' => 'Member Code already exists']);
    exit;
}

// Insert new member
$stmt_insert = mysqli_prepare($conn, "
    INSERT INTO members (member_code, name, phone, email, address, membership_tier_id, is_active, total_spent, total_points)
    VALUES (?, ?, ?, ?, ?, ?, ?, 0, 0)
");

mysqli_stmt_bind_param($stmt_insert, "sssssii",
    $member_code,
    $name,
    $phone,
    $email,
    $address,
    $membership_tier_id,
    $is_active
);

if (mysqli_stmt_execute($stmt_insert)) {
    echo json_encode(['success' => true, 'message' => 'Member saved successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save member: ' . mysqli_error($conn)]);
}

mysqli_stmt_close($stmt_check);
mysqli_stmt_close($stmt_insert);
?>
