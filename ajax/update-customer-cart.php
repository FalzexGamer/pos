<?php
include '../include/conn.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get POST data
$table_id = isset($_POST['table_id']) ? (int)$_POST['table_id'] : 0;
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

if ($table_id <= 0 || $product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid table ID or product ID']);
    exit;
}

if ($quantity < 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
    exit;
}

try {
    if ($quantity === 0) {
        // Remove item from cart
        $delete_query = "DELETE FROM customer_cart WHERE table_id = $table_id AND product_id = $product_id AND status = 'active'";
        
        if (!mysqli_query($conn, $delete_query)) {
            throw new Exception("Failed to remove item from cart: " . mysqli_error($conn));
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Item removed from cart',
            'action' => 'removed'
        ]);
        
    } else {
        // Check if item exists in cart
        $check_query = "SELECT * FROM customer_cart WHERE table_id = $table_id AND product_id = $product_id AND status = 'active'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (!$check_result) {
            throw new Exception("Database query failed: " . mysqli_error($conn));
        }
        
        if (mysqli_num_rows($check_result) > 0) {
            // Update existing item
            $existing_item = mysqli_fetch_assoc($check_result);
            $new_subtotal = $quantity * $existing_item['price'];
            
            $update_query = "UPDATE customer_cart 
                           SET quantity = $quantity, subtotal = $new_subtotal 
                           WHERE table_id = $table_id AND product_id = $product_id AND status = 'active'";
            
            if (!mysqli_query($conn, $update_query)) {
                throw new Exception("Failed to update cart item: " . mysqli_error($conn));
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Cart item updated',
                'action' => 'updated',
                'subtotal' => $new_subtotal
            ]);
            
        } else {
            // Add new item to cart
            $product_query = mysqli_query($conn, "SELECT selling_price, sku FROM products WHERE id = $product_id AND is_active = 1");
            
            if (!$product_query || mysqli_num_rows($product_query) === 0) {
                throw new Exception("Product not found or inactive");
            }
            
            $product = mysqli_fetch_assoc($product_query);
            $subtotal = $quantity * $product['selling_price'];
            
            $insert_query = "INSERT INTO customer_cart (table_id, product_id, sku, quantity, price, subtotal, status) 
                           VALUES ($table_id, $product_id, '" . mysqli_real_escape_string($conn, $product['sku']) . "', $quantity, {$product['selling_price']}, $subtotal, 'active')";
            
            if (!mysqli_query($conn, $insert_query)) {
                throw new Exception("Failed to add item to cart: " . mysqli_error($conn));
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Item added to cart',
                'action' => 'added',
                'subtotal' => $subtotal
            ]);
        }
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

mysqli_close($conn);
?>
