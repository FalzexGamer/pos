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

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid user ID'
    ]);
    exit;
}

// Check if user exists
$checkQuery = mysqli_query($conn, "SELECT id, username FROM users WHERE id = $id");
if (mysqli_num_rows($checkQuery) == 0) {
    echo json_encode([
        'success' => false,
        'message' => 'User not found'
    ]);
    exit;
}

$user = mysqli_fetch_array($checkQuery);

// Prevent deleting the current logged-in user
if ($id == $_SESSION['user_id']) {
    echo json_encode([
        'success' => false,
        'message' => 'You cannot delete your own account'
    ]);
    exit;
}

// Check if this is the last admin user
if ($user['role'] == 'admin') {
    $adminCountQuery = mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role = 'admin' AND is_active = 1");
    $adminCount = mysqli_fetch_array($adminCountQuery)['count'];
    
    if ($adminCount <= 1) {
        echo json_encode([
            'success' => false,
            'message' => 'Cannot delete the last administrator account'
        ]);
        exit;
    }
}

// Delete user
$deleteQuery = "DELETE FROM users WHERE id = $id";

if (mysqli_query($conn, $deleteQuery)) {
    echo json_encode([
        'success' => true,
        'message' => 'User deleted successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to delete user: ' . mysqli_error($conn)
    ]);
}
?>
