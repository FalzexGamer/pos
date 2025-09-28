<?php
include '../include/conn.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get POST data
$order_number = isset($_POST['order_number']) ? trim($_POST['order_number']) : '';

if (empty($order_number)) {
    echo json_encode(['success' => false, 'message' => 'Invalid order number']);
    exit;
}

try {
    // Sanitize order number
    $order_number = mysqli_real_escape_string($conn, $order_number);
    
    // Check if order exists
    $check_query = "SELECT * FROM customer_cart WHERE order_number = '$order_number' LIMIT 1";
    $check_result = mysqli_query($conn, $check_query);
    
    if (!$check_result || mysqli_num_rows($check_result) === 0) {
        throw new Exception("Order not found");
    }
    
    $order = mysqli_fetch_assoc($check_result);
    
    // Delete all items with this order_number
    $delete_query = "DELETE FROM customer_cart WHERE order_number = '$order_number'";
    
    if (!mysqli_query($conn, $delete_query)) {
        throw new Exception("Failed to delete order: " . mysqli_error($conn));
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Order deleted successfully'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

mysqli_close($conn);
?>
