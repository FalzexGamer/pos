<?php
include '../include/conn.php';

// Get table ID from GET parameter
$table_id = isset($_GET['table_id']) ? (int)$_GET['table_id'] : 0;

if ($table_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid table ID']);
    exit;
}

try {
    // Get customer cart items for the table
    $query = "SELECT cc.*, p.name as product_name, p.img as product_img 
              FROM customer_cart cc 
              LEFT JOIN products p ON cc.product_id = p.id 
              WHERE cc.table_id = $table_id AND cc.status = 'active' 
              ORDER BY cc.created_at DESC";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        throw new Exception("Database query failed: " . mysqli_error($conn));
    }
    
    $cart_items = [];
    $total_amount = 0;
    
    while ($row = mysqli_fetch_assoc($result)) {
        $cart_items[] = [
            'id' => $row['id'],
            'product_id' => $row['product_id'],
            'product_name' => $row['product_name'],
            'product_img' => $row['product_img'],
            'sku' => $row['sku'],
            'quantity' => (int)$row['quantity'],
            'price' => (float)$row['price'],
            'subtotal' => (float)$row['subtotal'],
            'created_at' => $row['created_at']
        ];
        
        $total_amount += (float)$row['subtotal'];
    }
    
    $tax_amount = $total_amount * 0.06; // 6% tax
    $final_total = $total_amount + $tax_amount;
    
    echo json_encode([
        'success' => true,
        'cart_items' => $cart_items,
        'total_items' => count($cart_items),
        'subtotal' => $total_amount,
        'tax_amount' => $tax_amount,
        'total_amount' => $final_total
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

mysqli_close($conn);
?>
