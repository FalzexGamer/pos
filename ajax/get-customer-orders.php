<?php
include '../include/conn.php';

// Get filter parameters
$status = isset($_GET['status']) ? $_GET['status'] : '';
$table_id = isset($_GET['table_id']) ? (int)$_GET['table_id'] : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    // Build the query
    $where_conditions = [];
    $params = [];
    
    if (!empty($status)) {
        $where_conditions[] = "cc.status = '" . mysqli_real_escape_string($conn, $status) . "'";
    }
    
    if ($table_id > 0) {
        $where_conditions[] = "cc.table_id = $table_id";
    }
    
    if (!empty($search)) {
        $search = mysqli_real_escape_string($conn, $search);
        $where_conditions[] = "(p.name LIKE '%$search%' OR cc.sku LIKE '%$search%')";
    }
    
    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
    
    // Get orders
    $query = "SELECT cc.*, p.name as product_name, p.img as product_img 
              FROM customer_cart cc 
              LEFT JOIN products p ON cc.product_id = p.id 
              $where_clause
              ORDER BY cc.created_at DESC";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        throw new Exception("Database query failed: " . mysqli_error($conn));
    }
    
    $orders = [];
    $summary = ['active' => 0, 'ordered' => 0, 'paid' => 0, 'abandoned' => 0];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = [
            'id' => $row['id'],
            'table_id' => $row['table_id'],
            'product_id' => $row['product_id'],
            'product_name' => $row['product_name'],
            'product_img' => $row['product_img'],
            'sku' => $row['sku'],
            'quantity' => (int)$row['quantity'],
            'price' => (float)$row['price'],
            'subtotal' => (float)$row['subtotal'],
            'status' => $row['status'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at']
        ];
        
        // Count by status
        $summary[$row['status']]++;
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
