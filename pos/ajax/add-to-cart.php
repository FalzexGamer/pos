<?php
include '../include/conn.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$product_id = $_POST['product_id'] ?? 0;
$user_id = $_SESSION['user_id'] ?? 0;

if (!$product_id || !$user_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID or user not authenticated']);
    exit;
}

// Get product details with prepared statement
$stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ? AND is_active = 1");
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_array($result);

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}

// Check stock availability
if ($product['stock_quantity'] <= 0) {
    echo json_encode(['success' => false, 'message' => 'Product "' . $product['name'] . '" is out of stock']);
    exit;
}

// Check if product already in cart with prepared statement
$stmt_existing = mysqli_prepare($conn, "
    SELECT id, quantity, price, subtotal 
    FROM cart 
    WHERE user_id = ? AND product_id = ? AND status = 'active'
");
mysqli_stmt_bind_param($stmt_existing, "ii", $user_id, $product_id);
mysqli_stmt_execute($stmt_existing);
$result_existing = mysqli_stmt_get_result($stmt_existing);
$existing_item = mysqli_fetch_array($result_existing);

if ($existing_item) {
    // Update existing item quantity
    $new_quantity = $existing_item['quantity'] + 1;
    $new_subtotal = $new_quantity * $existing_item['price'];
    
    $stmt_update = mysqli_prepare($conn, "
        UPDATE cart 
        SET quantity = ?, subtotal = ?, updated_at = CURRENT_TIMESTAMP 
        WHERE id = ?
    ");
    mysqli_stmt_bind_param($stmt_update, "ddi", $new_quantity, $new_subtotal, $existing_item['id']);
    $update_result = mysqli_stmt_execute($stmt_update);
    
    if ($update_result) {
        echo json_encode(['success' => true, 'message' => 'Product quantity updated in cart']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update cart: ' . mysqli_error($conn)]);
    }
} else {
    // Add new item to cart
    $price = $product['selling_price'];
    $subtotal = $price;
    
    $stmt_insert = mysqli_prepare($conn, "
        INSERT INTO cart (user_id, product_id, sku, quantity, price, subtotal) 
        VALUES (?, ?, ?, 1, ?, ?) 
    ");
    mysqli_stmt_bind_param($stmt_insert, "iisdd", $user_id, $product_id, $product['sku'], $price, $subtotal);
    $insert_result = mysqli_stmt_execute($stmt_insert);
    
    if ($insert_result) {
        echo json_encode(['success' => true, 'message' => 'Product added to cart']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add product to cart: ' . mysqli_error($conn)]);
    }
}
?>
