<?php
include '../include/conn.php';

// Set proper headers for JSON response
header('Content-Type: application/json');

// Function to generate QR code using QR Server API
function generateQRCode($data, $size = 200) {
    $encoded_data = urlencode($data);
    return "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data={$encoded_data}";
}

// Function to generate unique order ID
function generateOrderId() {
    return 'ORD' . date('YmdHi') . str_pad(mt_rand(1, 99), 2, '0', STR_PAD_LEFT);
}

// Check if request method is POST and required data is present
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['table_id']) && isset($_POST['items'])) {
    $table_id = (int)$_POST['table_id'];
    $items = json_decode($_POST['items'], true);
    
    if (!$items || count($items) == 0) {
        echo json_encode(['success' => false, 'message' => 'No items in cart']);
        exit;
    }
    
    // Generate unique order ID
    $order_id = generateOrderId();
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        $total_amount = 0;
        $subtotal = 0;
        
        // Process each item
        foreach ($items as $item) {
            $product_id = (int)$item['product_id'];
            $quantity = (int)$item['quantity'];
            $price = (float)$item['price'];
            $item_subtotal = $price * $quantity;
            
            // Get product details (using escaped values for compatibility)
            $product_id_escaped = mysqli_real_escape_string($conn, $product_id);
            $product_query = mysqli_query($conn, "SELECT name, sku, stock_quantity FROM products WHERE id = $product_id_escaped");
            $product = mysqli_fetch_array($product_query);
            
            if (!$product) {
                throw new Exception("Product not found: ID $product_id");
            }
            
            // Check stock
            if ($product['stock_quantity'] < $quantity) {
                throw new Exception("Insufficient stock for {$product['name']}. Available: {$product['stock_quantity']}, Required: $quantity");
            }
            
            // Update stock (using escaped values for compatibility)
            $quantity_escaped = mysqli_real_escape_string($conn, $quantity);
            $product_id_escaped = mysqli_real_escape_string($conn, $product_id);
            if (!mysqli_query($conn, "UPDATE products SET stock_quantity = stock_quantity - $quantity_escaped WHERE id = $product_id_escaped")) {
                throw new Exception("Failed to update stock: " . mysqli_error($conn));
            }
            
            // Insert into customer_cart with order_number (order_id should be NULL or a separate numeric ID)
            $order_id_escaped = mysqli_real_escape_string($conn, $order_id);
            $table_id_escaped = mysqli_real_escape_string($conn, $table_id);
            $sku_escaped = mysqli_real_escape_string($conn, $product['sku']);
            $price_escaped = mysqli_real_escape_string($conn, $price);
            $item_subtotal_escaped = mysqli_real_escape_string($conn, $item_subtotal);
            
            $insert_query = "INSERT INTO customer_cart (order_number, table_id, product_id, sku, quantity, price, subtotal, status, created_at) 
                            VALUES ('$order_id_escaped', $table_id_escaped, $product_id_escaped, '$sku_escaped', $quantity_escaped, $price_escaped, $item_subtotal_escaped, 'ordered', NOW())";
            
            if (!mysqli_query($conn, $insert_query)) {
                throw new Exception("Failed to insert cart item: " . mysqli_error($conn));
            }
            
            $subtotal += $item_subtotal;
        }
        
        $tax = $subtotal * 0.06; // 6% tax
        $total_amount = $subtotal + $tax;
        
        // Create order data for QR code
        $order_data = [
            'order_id' => $order_id,
            'table_id' => $table_id,
            'items' => $items,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total_amount,
            'timestamp' => date('Y-m-d H:i:s'),
            'status' => 'ordered'
        ];
        
        // Generate QR code data - use order_number for scanning
        $qr_data = $order_id; // Just the order number, not full JSON
        $qr_code_url = generateQRCode($qr_data, 300);
        
        // Commit transaction
        mysqli_commit($conn);
        
        // Return success with QR code
        echo json_encode([
            'success' => true,
            'message' => 'Order placed successfully',
            'order_id' => $order_id,
            'qr_code_url' => $qr_code_url,
            'order_data' => $order_data,
            'receipt_data' => [
                'order_id' => $order_id,
                'table_id' => $table_id,
                'items' => $items,
                'subtotal' => number_format($subtotal, 2),
                'tax' => number_format($tax, 2),
                'total' => number_format($total_amount, 2),
                'date' => date('Y-m-d H:i:s'),
                'qr_code_url' => $qr_code_url
            ]
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    // Provide more detailed error information
    $error_message = 'Invalid request method';
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $error_message = 'Request method must be POST, received: ' . $_SERVER['REQUEST_METHOD'];
    } elseif (!isset($_POST['table_id'])) {
        $error_message = 'Missing table_id parameter';
    } elseif (!isset($_POST['items'])) {
        $error_message = 'Missing items parameter';
    }
    
    echo json_encode([
        'success' => false, 
        'message' => $error_message,
        'debug' => [
            'method' => $_SERVER['REQUEST_METHOD'],
            'post_data' => $_POST,
            'get_data' => $_GET
        ]
    ]);
}
?>