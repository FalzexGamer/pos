<?php
include '../include/conn.php';
include '../include/session.php';

$session_id = $_POST['id'];
$user_id = $_SESSION['user_id'];

// Validate session exists and is in progress
$check_sql = "SELECT status FROM stock_take_sessions WHERE id = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "i", $session_id);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);
$session = mysqli_fetch_array($check_result);

if (!$session) {
    echo json_encode(['success' => false, 'message' => 'Session not found']);
    exit;
}

if ($session['status'] !== 'in_progress') {
    echo json_encode(['success' => false, 'message' => 'Session is not in progress']);
    exit;
}

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Get all stock take items for this session
    $items_sql = "SELECT sti.*, p.name as product_name, p.stock_quantity as current_stock 
                  FROM stock_take_items sti 
                  LEFT JOIN products p ON sti.product_id = p.id 
                  WHERE sti.session_id = ?";
    $items_stmt = mysqli_prepare($conn, $items_sql);
    mysqli_stmt_bind_param($items_stmt, "i", $session_id);
    mysqli_stmt_execute($items_stmt);
    $items_result = mysqli_stmt_get_result($items_stmt);
    
    $updated_count = 0;
    $differences_found = 0;
    
    while ($item = mysqli_fetch_assoc($items_result)) {
        $difference = $item['counted_quantity'] - $item['current_stock'];
        
        if ($difference != 0) {
            $differences_found++;
            
            // Update product stock quantity
            $update_product_sql = "UPDATE products SET stock_quantity = ? WHERE id = ?";
            $update_product_stmt = mysqli_prepare($conn, $update_product_sql);
            mysqli_stmt_bind_param($update_product_stmt, "ii", $item['counted_quantity'], $item['product_id']);
            mysqli_stmt_execute($update_product_stmt);
            
            // Record stock movement
            $movement_sql = "INSERT INTO stock_movements (product_id, movement_type, quantity, reference_type, reference_id, notes, created_by) 
                            VALUES (?, 'adjustment', ?, 'stock_take', ?, ?, ?)";
            $movement_stmt = mysqli_prepare($conn, $movement_sql);
            $notes = "Stock take adjustment: System: {$item['current_stock']}, Counted: {$item['counted_quantity']}, Difference: " . ($difference > 0 ? '+' : '') . $difference;
            mysqli_stmt_bind_param($movement_stmt, "iiiss", $item['product_id'], $difference, $session_id, $notes, $user_id);
            mysqli_stmt_execute($movement_stmt);
            
            $updated_count++;
        }
    }
    
    // Update session status to completed
    $update_sql = "UPDATE stock_take_sessions SET status = 'completed', end_date = CURRENT_TIMESTAMP WHERE id = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "i", $session_id);
    mysqli_stmt_execute($update_stmt);
    
    mysqli_commit($conn);
    
    $message = "Stock take session completed successfully";
    if ($updated_count > 0) {
        $message .= ". Updated stock levels for {$updated_count} products";
        if ($differences_found > 0) {
            $message .= " with {$differences_found} differences found";
        }
    } else {
        $message .= ". No stock adjustments were needed";
    }
    
    echo json_encode(['success' => true, 'message' => $message]);
    
} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => 'Database error occurred while completing session']);
}

mysqli_stmt_close($check_stmt);
if (isset($items_stmt)) mysqli_stmt_close($items_stmt);
if (isset($update_stmt)) mysqli_stmt_close($update_stmt);
?>
