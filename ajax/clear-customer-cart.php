<?php
include '../include/conn.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get POST data
$table_id = isset($_POST['table_id']) ? (int)$_POST['table_id'] : 0;

if ($table_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid table ID']);
    exit;
}

try {
    // Update cart items status to 'abandoned' instead of deleting them
    $update_query = "UPDATE customer_cart SET status = 'abandoned' WHERE table_id = $table_id AND status = 'active'";
    
    if (!mysqli_query($conn, $update_query)) {
        throw new Exception("Failed to clear cart: " . mysqli_error($conn));
    }
    
    $affected_rows = mysqli_affected_rows($conn);
    
    echo json_encode([
        'success' => true,
        'message' => 'Cart cleared successfully',
        'items_cleared' => $affected_rows
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

mysqli_close($conn);
?>
