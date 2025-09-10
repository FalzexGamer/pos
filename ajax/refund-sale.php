<?php
// Prevent any output before JSON response
ob_start();
error_reporting(0);
ini_set('display_errors', 0);

include '../include/conn.php';
include '../include/session.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get POST data
$sale_id = isset($_POST['sale_id']) ? intval($_POST['sale_id']) : 0;
$refund_reason = isset($_POST['refund_reason']) ? mysqli_real_escape_string($conn, $_POST['refund_reason']) : '';
$user_id = $_SESSION['user_id'];

// Validate input
if ($sale_id <= 0) {
    header('Content-Type: application/json');
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid sale ID']);
    exit;
}

if (empty($refund_reason)) {
    header('Content-Type: application/json');
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Refund reason is required']);
    exit;
}

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Get sale details
    $sale_query = mysqli_query($conn, "
        SELECT s.*, m.name as member_name, u.full_name as cashier_name
        FROM sales s
        LEFT JOIN members m ON s.member_id = m.id
        LEFT JOIN users u ON s.user_id = u.id
        WHERE s.id = $sale_id AND s.payment_status = 'paid'
    ");
    
    if (mysqli_num_rows($sale_query) == 0) {
        throw new Exception('Sale not found or already refunded');
    }
    
    $sale = mysqli_fetch_assoc($sale_query);
    
    // Get sale items
    $items_query = mysqli_query($conn, "
        SELECT si.*, p.name as product_name, p.sku
        FROM sale_items si
        LEFT JOIN products p ON si.product_id = p.id
        WHERE si.sale_id = $sale_id
    ");
    
    // Update stock quantities (add back to inventory)
    while ($item = mysqli_fetch_assoc($items_query)) {
        $product_id = $item['product_id'];
        $quantity = $item['quantity'];
        
        // Update product stock
        $update_stock = mysqli_query($conn, "
            UPDATE products 
            SET stock_quantity = stock_quantity + $quantity 
            WHERE id = $product_id
        ");
        
        if (!$update_stock) {
            throw new Exception('Failed to update stock for product: ' . $item['product_name']);
        }
        
        // Record stock movement
        $insert_movement = mysqli_query($conn, "
            INSERT INTO stock_movements (product_id, movement_type, quantity, reference_type, reference_id, notes, created_by) 
            VALUES ($product_id, 'in', $quantity, 'adjustment', $sale_id, 'Refund for sale #{$sale['invoice_number']}', $user_id)
        ");
        
        if (!$insert_movement) {
            throw new Exception('Failed to record stock movement for product: ' . $item['product_name']);
        }
    }
    
    // Update member total spent (subtract refunded amount)
    if ($sale['member_id']) {
        $update_member = mysqli_query($conn, "
            UPDATE members 
            SET total_spent = total_spent - {$sale['total_amount']} 
            WHERE id = {$sale['member_id']}
        ");
        
        if (!$update_member) {
            throw new Exception('Failed to update member total spent');
        }
    }
    
    // Update sales session totals
    if ($sale['session_id']) {
        $update_session = mysqli_query($conn, "
            UPDATE sales_sessions 
            SET total_sales = total_sales - {$sale['total_amount']},
                total_refunds = total_refunds + {$sale['total_amount']}
            WHERE id = {$sale['session_id']}
        ");
        
        if (!$update_session) {
            throw new Exception('Failed to update sales session');
        }
    }
    
    // Update sale status to refunded
    $update_sale = mysqli_query($conn, "
        UPDATE sales 
        SET payment_status = 'refunded', 
            notes = CONCAT(COALESCE(notes, ''), '\nRefunded on " . date('Y-m-d H:i:s') . " - Reason: $refund_reason - By: {$_SESSION['full_name']}')
        WHERE id = $sale_id
    ");
    
    if (!$update_sale) {
        throw new Exception('Failed to update sale status');
    }
    
    // Commit transaction
    mysqli_commit($conn);
    
    header('Content-Type: application/json');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00');
    
    // Ensure clean output
    ob_end_clean();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Sale refunded successfully',
        'invoice_number' => $sale['invoice_number']
    ]);
    
} catch (Exception $e) {
    // Rollback transaction
    mysqli_rollback($conn);
    
    header('Content-Type: application/json');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00');
    
    // Ensure clean output
    ob_end_clean();
    
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
exit;
?>
