<?php
include '../include/conn.php';
include '../include/session.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$member_id = $_POST['member_id'] ?? null;
$payment_method = $_POST['payment_method'];
$payment_method_id = $_POST['payment_method_id'] ?? null;
$amount_received = $_POST['amount_received'];
$user_id = $_SESSION['user_id'] ?? 0;

// Get payment method name from database if payment_method_id is provided
$payment_method_name = $payment_method; // Default to the passed value
if ($payment_method_id) {
    $payment_method_query = mysqli_query($conn, "SELECT name FROM payment_methods WHERE id = $payment_method_id");
    if ($payment_method_data = mysqli_fetch_array($payment_method_query)) {
        $payment_method_name = $payment_method_data['name'];
    }
}

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

// Get cart items from database
$query_cart_items = mysqli_query($conn, "
    SELECT c.*, p.name, p.sku 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = $user_id AND c.status = 'active'
");

if (!$query_cart_items || mysqli_num_rows($query_cart_items) == 0) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

// Calculate totals
$subtotal = 0;
$cart_items = [];
while ($item = mysqli_fetch_array($query_cart_items)) {
    $subtotal += $item['subtotal'];
    $cart_items[] = $item;
}

// Calculate member discount if member is selected
$discount = 0;
if ($member_id) {
    $member_query = mysqli_query($conn, "
        SELECT mt.discount_percentage 
        FROM members m 
        LEFT JOIN membership_tiers mt ON m.membership_tier_id = mt.id 
        WHERE m.id = '$member_id' AND m.is_active = 1
    ");
    
    if ($member_data = mysqli_fetch_array($member_query)) {
        $discount_percentage = floatval($member_data['discount_percentage']);
        $discount = ($subtotal * $discount_percentage) / 100;
    }
}

$tax = ($subtotal - $discount) * 0.06;
$total = $subtotal - $discount + $tax;

// Get current session
$user_id = $_SESSION['user_id'];
$query_session = mysqli_query($conn, "SELECT * FROM sales_sessions WHERE user_id = $user_id AND status = 'open' ORDER BY id DESC LIMIT 1");
$current_session = mysqli_fetch_array($query_session);

if (!$current_session) {
    echo json_encode(['success' => false, 'message' => 'No active sales session']);
    exit;
}

$session_id = $current_session['id'];

// Generate invoice number
$invoice_number = date('Ymd') . str_pad($session_id, 4, '0', STR_PAD_LEFT) . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

// Begin transaction
mysqli_begin_transaction($conn);

try {
    // Insert sale record
    $member_id_sql = $member_id ? $member_id : 'NULL';
    $payment_method_id_sql = $payment_method_id ? $payment_method_id : 'NULL';
    $payment_method_name_escaped = mysqli_real_escape_string($conn, $payment_method_name);
    $insert_sale = mysqli_query($conn, "
        INSERT INTO sales (invoice_number, session_id, member_id, user_id, subtotal, discount_amount, tax_amount, total_amount, payment_method, payment_method_id, payment_status) 
        VALUES ('$invoice_number', $session_id, $member_id_sql, $user_id, $subtotal, $discount, $tax, $total, '$payment_method_name_escaped', $payment_method_id_sql, 'paid')
    ");
    
    if (!$insert_sale) {
        throw new Exception('Failed to create sale record');
    }
    
    $sale_id = mysqli_insert_id($conn);
    
    // Insert sale items and update stock
    foreach ($cart_items as $item) {
        $product_id = $item['product_id'];
        $quantity = $item['quantity'];
        $unit_price = $item['price'];
        $total_price = $item['subtotal'];
        
        // Insert sale item
        $insert_item = mysqli_query($conn, "
            INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, total_price) 
            VALUES ($sale_id, $product_id, $quantity, $unit_price, $total_price)
        ");
        
        if (!$insert_item) {
            throw new Exception('Failed to insert sale item');
        }
        
        // Update stock
        $update_stock = mysqli_query($conn, "
            UPDATE products 
            SET stock_quantity = stock_quantity - $quantity 
            WHERE id = $product_id
        ");
        
        if (!$update_stock) {
            throw new Exception('Failed to update stock');
        }
        
        // Record stock movement
        $insert_movement = mysqli_query($conn, "
            INSERT INTO stock_movements (product_id, movement_type, quantity, reference_type, reference_id, created_by) 
            VALUES ($product_id, 'out', $quantity, 'sale', $sale_id, $user_id)
        ");
        
        if (!$insert_movement) {
            throw new Exception('Failed to record stock movement');
        }
    }
    
    // Update member total spent if member exists
    if ($member_id) {
        $update_member = mysqli_query($conn, "
            UPDATE members 
            SET total_spent = total_spent + $total 
            WHERE id = $member_id
        ");
        
        if (!$update_member) {
            throw new Exception('Failed to update member');
        }
    }
    
    // Update sales session
    $update_session = mysqli_query($conn, "
        UPDATE sales_sessions 
        SET total_sales = total_sales + $total 
        WHERE id = $session_id
    ");
    
    if (!$update_session) {
        throw new Exception('Failed to update sales session');
    }
    
    // Commit transaction
    mysqli_commit($conn);
    
    // Clear cart by updating status to 'ordered'
    mysqli_query($conn, "UPDATE cart SET status = 'ordered' WHERE user_id = $user_id AND status = 'active'");
    
    echo json_encode([
        'success' => true, 
        'message' => 'Sale completed successfully',
        'invoice_number' => $invoice_number
    ]);
    
} catch (Exception $e) {
    // Rollback transaction
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
