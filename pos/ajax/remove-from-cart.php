<?php
include '../include/conn.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$cart_id = $_POST['cart_id'] ?? 0;
$user_id = $_SESSION['user_id'] ?? 0;

if (!$cart_id || !$user_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

// Verify cart item belongs to current user
$query_verify = mysqli_query($conn, "
    SELECT id 
    FROM cart 
    WHERE id = $cart_id AND user_id = $user_id AND status = 'active'
");

$cart_item = mysqli_fetch_array($query_verify);

if (!$cart_item) {
    echo json_encode(['success' => false, 'message' => 'Cart item not found']);
    exit;
}

// Delete cart item
$delete_query = mysqli_query($conn, "DELETE FROM cart WHERE id = $cart_id");

if ($delete_query) {
    echo json_encode(['success' => true, 'message' => 'Item removed from cart successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to remove item from cart']);
}
?>
