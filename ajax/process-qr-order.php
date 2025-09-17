<?php
include '../include/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qr_data = $_POST['qr_data'];
    
    if (empty($qr_data)) {
        echo json_encode(['success' => false, 'message' => 'No QR code data provided']);
        exit;
    }
    
    try {
        // Try to decode the QR data as JSON
        $order_data = json_decode($qr_data, true);
        
        if (!$order_data) {
            throw new Exception('Invalid QR code format');
        }
        
        // Validate required fields
        if (!isset($order_data['order_id']) || !isset($order_data['items']) || !isset($order_data['table_id'])) {
            throw new Exception('Missing required order information');
        }
        
        // Verify the order exists in the database
        $order_id = $order_data['order_id'];
        $table_id = $order_data['table_id'];
        
        $verify_query = "SELECT COUNT(*) as count FROM customer_cart WHERE order_id = '$order_id' AND table_id = $table_id";
        $verify_result = mysqli_query($conn, $verify_query);
        $verify_count = mysqli_fetch_array($verify_result)['count'];
        
        if ($verify_count == 0) {
            throw new Exception('Order not found in database');
        }
        
        // Get detailed order information from database
        $order_details_query = "
            SELECT 
                cc.order_id,
                cc.table_id,
                cc.product_id,
                p.name as product_name,
                p.sku,
                cc.quantity,
                cc.price,
                cc.subtotal,
                cc.status,
                cc.created_at
            FROM customer_cart cc
            JOIN products p ON cc.product_id = p.id
            WHERE cc.order_id = '$order_id' AND cc.table_id = $table_id
            ORDER BY cc.created_at ASC
        ";
        
        $order_details_result = mysqli_query($conn, $order_details_query);
        $order_items = [];
        $total_subtotal = 0;
        
        while ($item = mysqli_fetch_array($order_details_result)) {
            $order_items[] = [
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'sku' => $item['sku'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['subtotal'],
                'status' => $item['status']
            ];
            $total_subtotal += $item['subtotal'];
        }
        
        $tax = $total_subtotal * 0.06; // 6% tax
        $total_amount = $total_subtotal + $tax;
        
        // Return order data for POS
        echo json_encode([
            'success' => true,
            'message' => 'Order loaded successfully',
            'order_data' => [
                'order_id' => $order_id,
                'table_id' => $table_id,
                'items' => $order_items,
                'subtotal' => $total_subtotal,
                'tax' => $tax,
                'total' => $total_amount,
                'status' => $item['status'] ?? 'ordered',
                'created_at' => $item['created_at'] ?? date('Y-m-d H:i:s')
            ],
            'summary' => [
                'total_items' => count($order_items),
                'total_quantity' => array_sum(array_column($order_items, 'quantity')),
                'order_status' => $item['status'] ?? 'ordered'
            ]
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>
