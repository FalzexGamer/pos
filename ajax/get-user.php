<?php
include '../include/conn.php';
include '../include/session.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid user ID'
    ]);
    exit;
}

$query = mysqli_query($conn, "SELECT * FROM users WHERE id = $id");

if (mysqli_num_rows($query) > 0) {
    $user = mysqli_fetch_array($query);
    
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'role' => $user['role'],
            'is_active' => $user['is_active'],
            'last_login' => $user['last_login'],
            'created_at' => $user['created_at'],
            'updated_at' => $user['updated_at']
        ]
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'User not found'
    ]);
}
?>
