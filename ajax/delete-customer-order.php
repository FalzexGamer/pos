<?php
include '../include/conn.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get POST data
$order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;

if ($order_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
    exit;
}

try {
    // Check if order exists
    $check_query = "SELECT * FROM customer_cart WHERE id = $order_id";
    $check_result = mysqli_query($conn, $check_query);
    
    if (!$check_result || mysqli_num_rows($check_result) === 0) {
        throw new Exception("Order not found");
    }
    
    $order = mysqli_fetch_assoc($check_result);
    
    // Delete the order
    $delete_query = "DELETE FROM customer_cart WHERE id = $order_id";
    
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
