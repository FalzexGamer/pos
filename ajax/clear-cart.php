<?php
include '../include/conn.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? 0;

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

// Delete all cart items for this user
$delete_items_query = mysqli_query($conn, "DELETE FROM cart WHERE user_id = $user_id AND status = 'active'");

if ($delete_items_query) {
    echo json_encode(['success' => true, 'message' => 'Cart cleared successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to clear cart']);
}
?>
