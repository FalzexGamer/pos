<?php
include '../include/conn.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get POST data
$order_number = isset($_POST['order_number']) ? trim($_POST['order_number']) : '';
$status = isset($_POST['status']) ? $_POST['status'] : '';

if (empty($order_number)) {
    echo json_encode(['success' => false, 'message' => 'Invalid order number']);
    exit;
}

if (!in_array($status, ['active', 'ordered', 'paid', 'abandoned'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
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
    
    // Update order status for all items with this order_number
    $update_query = "UPDATE customer_cart SET status = '" . mysqli_real_escape_string($conn, $status) . "' WHERE order_number = '$order_number'";
    
    if (!mysqli_query($conn, $update_query)) {
        throw new Exception("Failed to update order status: " . mysqli_error($conn));
    }
    
    // If marking as paid, we might want to create a sales record
    if ($status === 'paid') {
        // Check if sales record already exists for this order
        $sales_check = mysqli_query($conn, "SELECT id FROM sales WHERE notes LIKE '%Table {$order['table_id']}%' AND payment_status = 'pending'");
        
        if (!$sales_check || mysqli_num_rows($sales_check) === 0) {
            // Create sales record
            $order_number = 'CUST' . date('YmdHis') . sprintf('%03d', $order['table_id']);
            $tax_amount = $order['subtotal'] * 0.06;
            $total_amount = $order['subtotal'] + $tax_amount;
            
            $sales_query = "INSERT INTO sales (invoice_number, member_id, user_id, subtotal, tax_amount, total_amount, payment_method, payment_status, notes) 
                           VALUES ('$order_number', 0, 0, {$order['subtotal']}, $tax_amount, $total_amount, 'cash', 'paid', 'Customer order from table {$order['table_id']}')";
            
            if (!mysqli_query($conn, $sales_query)) {
                throw new Exception("Failed to create sales record: " . mysqli_error($conn));
            }
            
            $sale_id = mysqli_insert_id($conn);
            
            // Create sale item record
            $sale_item_query = "INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, total_price) 
                               VALUES ($sale_id, {$order['product_id']}, {$order['quantity']}, {$order['price']}, {$order['subtotal']})";
            
            if (!mysqli_query($conn, $sale_item_query)) {
                throw new Exception("Failed to create sale item: " . mysqli_error($conn));
            }
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Order status updated successfully',
        'new_status' => $status
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

mysqli_close($conn);
?>
