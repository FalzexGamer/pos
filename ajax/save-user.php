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
$username = trim($_POST['username']);
$full_name = trim($_POST['full_name']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);
$role = trim($_POST['role']);
$password = trim($_POST['password']);
$confirm_password = trim($_POST['confirm_password']);
$is_active = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;

// Validation
$errors = [];

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

if (empty($password)) {
    $errors[] = 'Password is required';
} elseif (strlen($password) < 6) {
    $errors[] = 'Password must be at least 6 characters long';
}

if ($password !== $confirm_password) {
    $errors[] = 'Passwords do not match';
}

// Check if username already exists
$checkQuery = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username'");
if (mysqli_num_rows($checkQuery) > 0) {
    $errors[] = 'Username already exists';
}

// Check if email already exists (if provided)
if (!empty($email)) {
    $checkEmailQuery = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
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

// Get permissions
$permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];

// Build permission fields for SQL
$permissionFields = [
    'access_dashboard', 'access_pos', 'access_sales', 'access_opening_closing',
    'access_products', 'access_categories', 'access_suppliers', 'access_uom',
    'access_stock_take', 'access_inventory_report', 'access_members',
    'access_customer_orders', 'access_customer_order', 'access_sales_report',
    'access_member_report', 'access_profit_loss'
];

$permissionValues = [];
foreach ($permissionFields as $field) {
    $value = in_array($field, $permissions) ? 1 : 0;
    $permissionValues[] = "$field = $value";
}

$permissionSql = implode(', ', $permissionValues);

// Insert new user
$insertQuery = "INSERT INTO users (username, password, full_name, email, phone, role, is_active, $permissionSql, created_at, updated_at) 
                VALUES ('$username', '$password', '$full_name', '$email', '$phone', '$role', $is_active, NOW(), NOW())";

if (mysqli_query($conn, $insertQuery)) {
    echo json_encode([
        'success' => true,
        'message' => 'User created successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to create user: ' . mysqli_error($conn)
    ]);
}
?>
