<?php
include '../include/conn.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? 0;
$cart_id = $_GET['cart_id'] ?? 0;

if (!$user_id) {
    echo json_encode([
        'success' => false,
        'message' => 'User not authenticated',
        'note' => ''
    ]);
    exit;
}

if (!$cart_id) {
    echo json_encode([
        'success' => false,
        'message' => 'Cart item ID required',
        'note' => ''
    ]);
    exit;
}

// Get cart item note
$query = mysqli_query($conn, "
    SELECT notes 
    FROM cart 
    WHERE id = '$cart_id' AND user_id = '$user_id' AND status = 'active'
");

if ($item = mysqli_fetch_array($query)) {
    echo json_encode([
        'success' => true,
        'note' => $item['notes'] ?? ''
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Cart item not found',
        'note' => ''
    ]);
}
?>
