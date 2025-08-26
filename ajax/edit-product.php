<?php
include '../include/conn.php';

$product_id = $_POST['product_id'] ?? 0;
$sku = $_POST['sku'] ?? '';
$barcode = $_POST['barcode'] ?? '';
$name = $_POST['name'] ?? '';
$description = $_POST['description'] ?? '';
$category_id = $_POST['category_id'] ?? '';
$supplier_id = $_POST['supplier_id'] ?? '';
$uom_id = $_POST['uom_id'] ?? '';
$cost_price = $_POST['cost_price'] ?? 0;
$selling_price = $_POST['selling_price'] ?? 0;
$stock_quantity = $_POST['stock_quantity'] ?? 0;
$min_stock_level = $_POST['min_stock_level'] ?? 0;

// Validation
if (!$product_id || empty($sku) || empty($name) || empty($category_id) || empty($supplier_id) || empty($uom_id)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
    exit;
}

// Check if product exists
$stmt_check = mysqli_prepare($conn, "SELECT id, sku, barcode, stock_quantity FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt_check, "i", $product_id);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);

if (mysqli_num_rows($result_check) == 0) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}

$current_product = mysqli_fetch_assoc($result_check);
$old_stock_quantity = $current_product['stock_quantity'];

// Check if SKU already exists for other products
$stmt_sku = mysqli_prepare($conn, "SELECT id FROM products WHERE sku = ? AND id != ?");
mysqli_stmt_bind_param($stmt_sku, "si", $sku, $product_id);
mysqli_stmt_execute($stmt_sku);
$result_sku = mysqli_stmt_get_result($stmt_sku);

if (mysqli_num_rows($result_sku) > 0) {
    echo json_encode(['success' => false, 'message' => 'SKU already exists']);
    exit;
}

// Check if barcode already exists for other products (if provided)
if (!empty($barcode)) {
    $stmt_barcode = mysqli_prepare($conn, "SELECT id FROM products WHERE barcode = ? AND id != ?");
    mysqli_stmt_bind_param($stmt_barcode, "si", $barcode, $product_id);
    mysqli_stmt_execute($stmt_barcode);
    $result_barcode = mysqli_stmt_get_result($stmt_barcode);
    
    if (mysqli_num_rows($result_barcode) > 0) {
        echo json_encode(['success' => false, 'message' => 'Barcode already exists']);
        exit;
    }
}

// Update product with prepared statement
$stmt_update = mysqli_prepare($conn, "
    UPDATE products 
    SET sku = ?, barcode = ?, name = ?, description = ?, category_id = ?, supplier_id = ?, 
        uom_id = ?, cost_price = ?, selling_price = ?, stock_quantity = ?, min_stock_level = ?, 
        updated_at = CURRENT_TIMESTAMP
    WHERE id = ?
");

mysqli_stmt_bind_param($stmt_update, "ssssiiidddii", 
    $sku, 
    $barcode, 
    $name, 
    $description, 
    $category_id, 
    $supplier_id, 
    $uom_id, 
    $cost_price, 
    $selling_price, 
    $stock_quantity, 
    $min_stock_level, 
    $product_id
);

if (mysqli_stmt_execute($stmt_update)) {
    // Record stock movement if stock quantity changed
    $stock_difference = $stock_quantity - $old_stock_quantity;
    
    if ($stock_difference != 0) {
        $movement_type = $stock_difference > 0 ? 'in' : 'out';
        $quantity = abs($stock_difference);
        $notes = $stock_difference > 0 ? 'Stock adjustment (increase)' : 'Stock adjustment (decrease)';
        
        $stmt_movement = mysqli_prepare($conn, "
            INSERT INTO stock_movements (product_id, movement_type, quantity, reference_type, reference_id, created_by, notes) 
            VALUES (?, ?, ?, 'adjustment', ?, 1, ?)
        ");
        mysqli_stmt_bind_param($stmt_movement, "isiss", $product_id, $movement_type, $quantity, $product_id, $notes);
        mysqli_stmt_execute($stmt_movement);
    }
    
    echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update product: ' . mysqli_error($conn)]);
}

mysqli_stmt_close($stmt_check);
mysqli_stmt_close($stmt_sku);
if (!empty($barcode)) {
    mysqli_stmt_close($stmt_barcode);
}
mysqli_stmt_close($stmt_update);
?>
