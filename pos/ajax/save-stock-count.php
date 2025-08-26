<?php
include '../include/conn.php';
include '../include/session.php';

$product_id = $_POST['product_id'];
$session_id = $_POST['session_id'];
$counted_quantity = (int)$_POST['counted_quantity'];
$notes = trim($_POST['notes'] ?? '');
$user_id = $_SESSION['user_id'];

// Validate required fields
if (empty($product_id) || $counted_quantity < 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    exit;
}

// Get current stock quantity
$product_sql = "SELECT stock_quantity FROM products WHERE id = ?";
$product_stmt = mysqli_prepare($conn, $product_sql);
mysqli_stmt_bind_param($product_stmt, "i", $product_id);
mysqli_stmt_execute($product_stmt);
$product_result = mysqli_stmt_get_result($product_stmt);
$product = mysqli_fetch_array($product_result);

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}

$current_quantity = $product['stock_quantity'];
$difference = $counted_quantity - $current_quantity;

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Update product stock quantity
    $update_sql = "UPDATE products SET stock_quantity = ? WHERE id = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "ii", $counted_quantity, $product_id);
    mysqli_stmt_execute($update_stmt);
    
    // Record stock movement
    $movement_sql = "INSERT INTO stock_movements (product_id, movement_type, quantity, reference_type, reference_id, notes, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $movement_stmt = mysqli_prepare($conn, $movement_sql);
    
    if ($difference > 0) {
        // Stock increase
        $movement_type = 'in';
        $movement_quantity = $difference;
        mysqli_stmt_bind_param($movement_stmt, "isisiss", $product_id, $movement_type, $movement_quantity, $reference_type, $session_id, $notes, $user_id);
        $reference_type = 'stock_take';
        mysqli_stmt_execute($movement_stmt);
    } elseif ($difference < 0) {
        // Stock decrease
        $movement_type = 'out';
        $movement_quantity = abs($difference);
        $reference_type = 'stock_take';
        mysqli_stmt_bind_param($movement_stmt, "isisiss", $product_id, $movement_type, $movement_quantity, $reference_type, $session_id, $notes, $user_id);
        mysqli_stmt_execute($movement_stmt);
    }
    
    // If session_id is provided, record in stock take items
    if ($session_id > 0) {
        // Check if item already exists in session
        $check_sql = "SELECT id FROM stock_take_items WHERE session_id = ? AND product_id = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "ii", $session_id, $product_id);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_fetch_array($check_result)) {
            // Update existing record
            $update_item_sql = "UPDATE stock_take_items SET counted_quantity = ?, difference = ?, notes = ? WHERE session_id = ? AND product_id = ?";
            $update_item_stmt = mysqli_prepare($conn, $update_item_sql);
            mysqli_stmt_bind_param($update_item_stmt, "iissi", $counted_quantity, $difference, $notes, $session_id, $product_id);
            mysqli_stmt_execute($update_item_stmt);
        } else {
            // Insert new record
            $insert_item_sql = "INSERT INTO stock_take_items (session_id, product_id, system_quantity, counted_quantity, difference, notes) VALUES (?, ?, ?, ?, ?, ?)";
            $insert_item_stmt = mysqli_prepare($conn, $insert_item_sql);
            mysqli_stmt_bind_param($insert_item_stmt, "iiiiis", $session_id, $product_id, $current_quantity, $counted_quantity, $difference, $notes);
            mysqli_stmt_execute($insert_item_stmt);
        }
    }
    
    mysqli_commit($conn);
    
    $message = 'Stock count updated successfully';
    if ($difference != 0) {
        $message .= ' (Difference: ' . ($difference > 0 ? '+' : '') . $difference . ')';
    }
    
    echo json_encode(['success' => true, 'message' => $message]);
    
} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}

mysqli_stmt_close($product_stmt);
mysqli_stmt_close($update_stmt);
mysqli_stmt_close($movement_stmt);
if (isset($check_stmt)) mysqli_stmt_close($check_stmt);
if (isset($update_item_stmt)) mysqli_stmt_close($update_item_stmt);
if (isset($insert_item_stmt)) mysqli_stmt_close($insert_item_stmt);
?>
