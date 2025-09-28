<?php
include '../include/conn.php';

// Get filter parameters
$status = isset($_GET['status']) ? $_GET['status'] : '';
$table_id = isset($_GET['table_id']) ? (int)$_GET['table_id'] : 0;
$date_range = isset($_GET['date_range']) ? $_GET['date_range'] : 'today';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    // Build date filter
    $date_condition = '';
    switch ($date_range) {
        case 'today':
            $date_condition = "DATE(cc.created_at) = CURDATE()";
            break;
        case 'yesterday':
            $date_condition = "DATE(cc.created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
            break;
        case 'week':
            $date_condition = "cc.created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
            break;
        case 'month':
            $date_condition = "cc.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
            break;
        default:
            $date_condition = "1=1"; // All time
            break;
    }
    
    // Build where conditions
    $where_conditions = [$date_condition];
    
    // Filter by status in customer_cart table
    if (!empty($status)) {
        $where_conditions[] = "cc.status = '" . mysqli_real_escape_string($conn, $status) . "'";
    }
    
    if ($table_id > 0) {
        $where_conditions[] = "cc.table_id = $table_id";
    }
    
    if (!empty($search)) {
        $search = mysqli_real_escape_string($conn, $search);
        $where_conditions[] = "(cc.order_number LIKE '%$search%' OR p.name LIKE '%$search%' OR cc.sku LIKE '%$search%')";
    }
    
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
    
    // Get orders grouped by order_number (distinct order_number for each order)
    $query = "SELECT 
                cc.order_number,
                cc.table_id,
                COUNT(*) as total_items,
                SUM(cc.subtotal) as subtotal,
                SUM(cc.subtotal) * 0.06 as tax_amount,
                SUM(cc.subtotal) * 1.06 as total_amount,
                MIN(cc.created_at) as created_at,
                MAX(cc.updated_at) as last_updated_at,
                GROUP_CONCAT(DISTINCT cc.status ORDER BY cc.status SEPARATOR ', ') as statuses,
                CASE 
                    WHEN COUNT(DISTINCT cc.status) = 1 AND MAX(cc.status) = 'paid' THEN 'paid'
                    WHEN COUNT(DISTINCT cc.status) = 1 AND MAX(cc.status) = 'cancelled' THEN 'cancelled'
                    WHEN COUNT(DISTINCT cc.status) = 1 AND MAX(cc.status) = 'abandoned' THEN 'abandoned'
                    WHEN MAX(cc.status) = 'served' THEN 'served'
                    WHEN MAX(cc.status) = 'ready' THEN 'ready'
                    WHEN MAX(cc.status) = 'preparing' THEN 'preparing'
                    WHEN MAX(cc.status) = 'ordered' THEN 'ordered'
                    WHEN MAX(cc.status) = 'active' THEN 'active'
                    ELSE 'active'
                END as status,
                GROUP_CONCAT(
                    CONCAT(p.name, ' (', cc.quantity, ')') 
                    ORDER BY cc.created_at 
                    SEPARATOR ', '
                ) as item_summary
              FROM customer_cart cc 
              LEFT JOIN products p ON cc.product_id = p.id 
              $where_clause
              AND cc.order_number IS NOT NULL
              GROUP BY cc.order_number, cc.table_id
              ORDER BY MAX(cc.created_at) DESC";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        throw new Exception("Database query failed: " . mysqli_error($conn));
    }
    
    $orders = [];
    $summary = [
        'active' => 0, 
        'ordered' => 0, 
        'preparing' => 0, 
        'ready' => 0, 
        'served' => 0, 
        'paid' => 0, 
        'cancelled' => 0, 
        'abandoned' => 0
    ];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = [
            'order_number' => $row['order_number'], // This is now the order_number
            'table_id' => $row['table_id'],
            'total_items' => (int)$row['total_items'],
            'subtotal' => (float)$row['subtotal'],
            'tax_amount' => (float)$row['tax_amount'],
            'total_amount' => (float)$row['total_amount'],
            'status' => $row['status'],
            'statuses' => $row['statuses'],
            'created_at' => $row['created_at'],
            'last_updated_at' => $row['last_updated_at'],
            'item_summary' => $row['item_summary'],
            'customer_name' => null // Will be populated if customer_orders table exists
        ];
        
        // Count by status
        $summary[$row['status']]++;
    }
    
    // Try to get customer information if customer_orders table exists
    $customer_orders_query = "SHOW TABLES LIKE 'customer_orders'";
    $customer_orders_result = mysqli_query($conn, $customer_orders_query);
    
    if ($customer_orders_result && mysqli_num_rows($customer_orders_result) > 0) {
        // Customer orders table exists, get customer info
        foreach ($orders as &$order) {
            $customer_query = "SELECT customer_name, customer_phone, customer_email 
                              FROM customer_orders 
                              WHERE order_number = '" . mysqli_real_escape_string($conn, $order['order_number']) . "'";
            $customer_result = mysqli_query($conn, $customer_query);
            
            if ($customer_result && mysqli_num_rows($customer_result) > 0) {
                $customer = mysqli_fetch_assoc($customer_result);
                $order['customer_name'] = $customer['customer_name'];
                $order['customer_phone'] = $customer['customer_phone'];
                $order['customer_email'] = $customer['customer_email'];
            }
        }
    }
    
    echo json_encode([
        'success' => true,
        'orders' => $orders,
        'summary' => $summary,
        'total_orders' => count($orders)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

mysqli_close($conn);
?>
