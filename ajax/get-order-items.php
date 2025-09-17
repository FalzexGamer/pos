<?php
include '../include/conn.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get order number from POST data
$order_number = isset($_POST['order_number']) ? trim($_POST['order_number']) : '';

if (empty($order_number)) {
    echo json_encode(['success' => false, 'message' => 'Order number is required']);
    exit;
}

try {
    // Sanitize the order number
    $order_number = mysqli_real_escape_string($conn, $order_number);
    
    // Get all items for this order from customer_cart table
    $query = "SELECT 
                cc.product_id,
                cc.quantity,
                cc.price,
                cc.subtotal,
                p.name as product_name,
                p.sku,
                p.barcode,
                p.selling_price,
                p.stock_quantity,
                c.name as category_name
              FROM customer_cart cc 
              LEFT JOIN products p ON cc.product_id = p.id 
              LEFT JOIN categories c ON p.category_id = c.id
              WHERE cc.order_id = '$order_number'
              AND cc.status IN ('active', 'ordered', 'preparing', 'ready', 'served')
              ORDER BY cc.created_at ASC";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        throw new Exception("Database query failed: " . mysqli_error($conn));
    }
    
    $items = [];
    $total_items = 0;
    $total_amount = 0;
    
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = [
            'product_id' => (int)$row['product_id'],
            'quantity' => (int)$row['quantity'],
            'price' => (float)$row['price'],
            'subtotal' => (float)$row['subtotal'],
            'product_name' => $row['product_name'],
            'sku' => $row['sku'],
            'barcode' => $row['barcode'],
            'selling_price' => (float)$row['selling_price'],
            'stock_quantity' => (int)$row['stock_quantity'],
            'category_name' => $row['category_name']
        ];
        
        $total_items += (int)$row['quantity'];
        $total_amount += (float)$row['subtotal'];
    }
    
    if (empty($items)) {
        echo json_encode([
            'success' => false, 
            'message' => 'Order not found or has no active items'
        ]);
        exit;
    }
    
    // Get order summary information
    $order_info_query = "SELECT 
                            cc.table_id,
                            MIN(cc.created_at) as created_at,
                            MAX(cc.updated_at) as updated_at,
                            GROUP_CONCAT(DISTINCT cc.status) as statuses
                         FROM customer_cart cc 
                         WHERE cc.order_id = '$order_number'
                         GROUP BY cc.order_id, cc.table_id";
    
    $order_info_result = mysqli_query($conn, $order_info_query);
    $order_info = null;
    
    if ($order_info_result && mysqli_num_rows($order_info_result) > 0) {
        $order_info = mysqli_fetch_assoc($order_info_result);
    }
    
    echo json_encode([
        'success' => true,
        'order_number' => $order_number,
        'items' => $items,
        'total_items' => $total_items,
        'total_amount' => $total_amount,
        'order_info' => $order_info,
        'message' => "Found {$total_items} items in order {$order_number}"
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

mysqli_close($conn);
?>
