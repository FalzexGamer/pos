<?php
include '../include/conn.php';
include '../include/session.php';

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode([
        'success' => false,
        'message' => 'Access denied. Admin privileges required.'
    ]);
    exit;
}

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

// Check if user exists (using prepared statement)
$checkQuery = mysqli_prepare($conn, "SELECT id, username, role FROM users WHERE id = ?");
if (!$checkQuery) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . mysqli_error($conn)
    ]);
    exit;
}

mysqli_stmt_bind_param($checkQuery, "i", $id);
mysqli_stmt_execute($checkQuery);
$result = mysqli_stmt_get_result($checkQuery);

if (mysqli_num_rows($result) == 0) {
    echo json_encode([
        'success' => false,
        'message' => 'User not found'
    ]);
    exit;
}

$user = mysqli_fetch_array($result);
mysqli_stmt_close($checkQuery);

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
    $adminCountQuery = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM users WHERE role = 'admin' AND is_active = 1");
    if (!$adminCountQuery) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . mysqli_error($conn)
        ]);
        exit;
    }
    
    mysqli_stmt_execute($adminCountQuery);
    $adminResult = mysqli_stmt_get_result($adminCountQuery);
    $adminCount = mysqli_fetch_array($adminResult)['count'];
    mysqli_stmt_close($adminCountQuery);
    
    if ($adminCount <= 1) {
        echo json_encode([
            'success' => false,
            'message' => 'Cannot delete the last administrator account'
        ]);
        exit;
    }
}

// Delete user (using prepared statement)
$deleteQuery = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
if (!$deleteQuery) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . mysqli_error($conn)
    ]);
    exit;
}

mysqli_stmt_bind_param($deleteQuery, "i", $id);

if (mysqli_stmt_execute($deleteQuery)) {
    echo json_encode([
        'success' => true,
        'message' => 'User deleted successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to delete user: ' . mysqli_stmt_error($deleteQuery)
    ]);
}

mysqli_stmt_close($deleteQuery);
?>
