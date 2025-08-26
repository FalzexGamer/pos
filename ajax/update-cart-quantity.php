<?php
include '../include/conn.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$cart_id = $_POST['cart_id'] ?? 0;
$quantity = $_POST['quantity'] ?? 0;
$user_id = $_SESSION['user_id'] ?? 0;

if (!$cart_id || $quantity <= 0 || !$user_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

// Verify cart item belongs to current user
$query_verify = mysqli_query($conn, "
    SELECT c.*, p.stock_quantity 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.id = $cart_id AND c.user_id = $user_id AND c.status = 'active'
");

$cart_item = mysqli_fetch_array($query_verify);

if (!$cart_item) {
    echo json_encode(['success' => false, 'message' => 'Cart item not found']);
    exit;
}

// Check stock availability
if ($quantity > $cart_item['stock_quantity']) {
    echo json_encode(['success' => false, 'message' => 'Requested quantity exceeds available stock']);
    exit;
}

// Update quantity and subtotal
$new_subtotal = $quantity * $cart_item['price'];

$update_query = mysqli_query($conn, "
    UPDATE cart 
    SET quantity = $quantity, subtotal = $new_subtotal, updated_at = CURRENT_TIMESTAMP 
    WHERE id = $cart_id
");

if ($update_query) {
    echo json_encode(['success' => true, 'message' => 'Quantity updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update quantity']);
}
?>
