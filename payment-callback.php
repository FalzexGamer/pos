<?php
include 'include/conn.php';

// Handle ToyyibPay callback/webhook
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Also check for GET parameters (ToyaibPay sometimes sends via GET)
if (empty($data)) {
    $data = $_GET;
}

// Log the callback for debugging (optional)
$log_data = [
    'timestamp' => date('Y-m-d H:i:s'),
    'method' => $_SERVER['REQUEST_METHOD'],
    'data' => $data,
    'raw_input' => $input
];
error_log('ToyyibPay Callback: ' . json_encode($log_data));

// Extract payment information
$refno = isset($data['refno']) ? mysqli_real_escape_string($conn, $data['refno']) : '';
$status = isset($data['status']) ? mysqli_real_escape_string($conn, $data['status']) : '';
$reason = isset($data['reason']) ? mysqli_real_escape_string($conn, $data['reason']) : '';
$billcode = isset($data['billcode']) ? mysqli_real_escape_string($conn, $data['billcode']) : '';
$transaction_id = isset($data['transaction_id']) ? mysqli_real_escape_string($conn, $data['transaction_id']) : '';

// Validate required parameters
if (empty($refno)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing reference number']);
    exit;
}

// Get order information
$order_query = mysqli_query($conn, "SELECT * FROM customer_order_summary WHERE order_number = '$refno'");

if (!$order_query || mysqli_num_rows($order_query) == 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Order not found']);
    exit;
}

$order_info = mysqli_fetch_assoc($order_query);

// Determine payment status
$payment_successful = false;
if ($status == '1' || strtoupper($status) == 'SUCCESS') {
    $payment_successful = true;
}

// Update order status in database
if ($payment_successful) {
    // Check if we already processed this payment to prevent double stock decrease
    $cart_status_check = mysqli_query($conn, "SELECT status FROM customer_cart WHERE order_number = '$refno' LIMIT 1");
    $already_processed = false;
    
    if ($cart_status_check && mysqli_num_rows($cart_status_check) > 0) {
        $status_row = mysqli_fetch_assoc($cart_status_check);
        if ($status_row['status'] == 'paid') {
            $already_processed = true;
            error_log("Order $refno already processed (status: paid) - skipping stock decrease");
        }
    }
    
    // IMPORTANT: Decrease stock FIRST when payment is successful (only if not already processed)
    $stock_updated = false;
    if (!$already_processed) {
        // Get all items in this order from customer_cart table
        $order_items_query = mysqli_query($conn, "SELECT product_id, quantity FROM customer_cart WHERE order_number = '$refno'");
        
        if ($order_items_query && mysqli_num_rows($order_items_query) > 0) {
            while ($item = mysqli_fetch_assoc($order_items_query)) {
                $product_id = (int)$item['product_id'];
                $quantity = (int)$item['quantity'];
                
                // Check current stock before decreasing
                $stock_check = mysqli_query($conn, "SELECT stock_quantity FROM products WHERE id = $product_id");
                if ($stock_check && mysqli_num_rows($stock_check) > 0) {
                    $stock_info = mysqli_fetch_assoc($stock_check);
                    $current_stock = (int)$stock_info['stock_quantity'];
                    
                    // Only decrease if sufficient stock
                    if ($current_stock >= $quantity) {
                        $update_stock = mysqli_query($conn, "UPDATE products SET stock_quantity = stock_quantity - $quantity WHERE id = $product_id");
                        
                        if (!$update_stock) {
                            error_log("Failed to decrease stock for product ID $product_id in order $refno: " . mysqli_error($conn));
                        } else {
                            error_log("Successfully decreased stock for product ID $product_id by $quantity for order $refno");
                            $stock_updated = true;
                        }
                    } else {
                        error_log("Warning: Insufficient stock to decrease for product ID $product_id in order $refno. Current: $current_stock, Required: $quantity");
                    }
                }
            }
        }
    }
    
    // Try to update customer_orders table if it exists
    $update_order_query = "UPDATE customer_orders SET 
                          payment_status = 'paid', 
                          payment_method = 'ToyyibPay',
                          updated_at = NOW() 
                          WHERE order_number = '$refno'";
    
    $order_updated = mysqli_query($conn, $update_order_query);
    
    if (!$order_updated) {
        error_log("Note: Could not update customer_orders table for order $refno - " . mysqli_error($conn));
    }
    
    // Update customer_cart status to paid
    $update_cart_query = "UPDATE customer_cart SET status = 'paid' WHERE order_number = '$refno'";
    $cart_updated = mysqli_query($conn, $update_cart_query);
    
    if (!$cart_updated) {
        error_log("Failed to update customer_cart status for order $refno: " . mysqli_error($conn));
    }
    
    // Log successful payment
    $log_message = "Payment successful for order: $refno, Transaction ID: $transaction_id, Stock updated: " . ($stock_updated ? 'Yes' : 'No');
    error_log($log_message);
    
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Payment processed successfully',
        'order_number' => $refno,
        'stock_updated' => $stock_updated
    ]);
} else {
    // Payment failed - update to pending status
    $update_query = "UPDATE customer_orders SET 
                    payment_status = 'pending',
                    updated_at = NOW() 
                    WHERE order_number = '$refno'";
    
    mysqli_query($conn, $update_query);
    
    // Log failed payment
    $log_message = "Payment failed for order: $refno, Reason: $reason, Error: " . mysqli_error($conn);
    error_log($log_message);
    
    http_response_code(200);
    echo json_encode([
        'status' => 'failed',
        'message' => 'Payment failed',
        'order_number' => $refno,
        'reason' => $reason
    ]);
}

mysqli_close($conn);
?>
