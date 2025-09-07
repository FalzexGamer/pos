<?php
include '../include/conn.php';
include '../include/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

// Get form data
$id = (int)$_POST['id'];
$username = trim($_POST['username']);
$full_name = trim($_POST['full_name']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);
$role = trim($_POST['role']);
$is_active = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;

// Validation
$errors = [];

if ($id <= 0) {
    $errors[] = 'Invalid user ID';
}

if (empty($username)) {
    $errors[] = 'Username is required';
} elseif (strlen($username) < 3) {
    $errors[] = 'Username must be at least 3 characters long';
} elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    $errors[] = 'Username can only contain letters, numbers, and underscores';
}

if (empty($full_name)) {
    $errors[] = 'Full name is required';
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address';
}

if (empty($role) || !in_array($role, ['admin', 'manager', 'cashier'])) {
    $errors[] = 'Please select a valid role';
}

// Check if username already exists (excluding current user)
$checkQuery = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username' AND id != $id");
if (mysqli_num_rows($checkQuery) > 0) {
    $errors[] = 'Username already exists';
}

// Check if email already exists (if provided, excluding current user)
if (!empty($email)) {
    $checkEmailQuery = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email' AND id != $id");
    if (mysqli_num_rows($checkEmailQuery) > 0) {
        $errors[] = 'Email already exists';
    }
}

if (!empty($errors)) {
    echo json_encode([
        'success' => false,
        'message' => implode(', ', $errors)
    ]);
    exit;
}

// Update user
$updateQuery = "UPDATE users SET 
                username = '$username',
                full_name = '$full_name',
                email = '$email',
                phone = '$phone',
                role = '$role',
                is_active = $is_active,
                updated_at = NOW()
                WHERE id = $id";

if (mysqli_query($conn, $updateQuery)) {
    echo json_encode([
        'success' => true,
        'message' => 'User updated successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update user: ' . mysqli_error($conn)
    ]);
}
?>
