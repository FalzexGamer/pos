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
$escaped_product_id = mysqli_real_escape_string($conn, $product_id);
$check_sql = "SELECT id, sku, barcode, stock_quantity FROM products WHERE id = '$escaped_product_id'";
$result_check = mysqli_query($conn, $check_sql);

if (!$result_check || mysqli_num_rows($result_check) == 0) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}

$current_product = mysqli_fetch_assoc($result_check);
$old_stock_quantity = $current_product['stock_quantity'];

// Check if SKU already exists for other products
$escaped_sku = mysqli_real_escape_string($conn, $sku);
$sku_check_sql = "SELECT id FROM products WHERE sku = '$escaped_sku' AND id != '$escaped_product_id'";
$result_sku = mysqli_query($conn, $sku_check_sql);

if (!$result_sku || mysqli_num_rows($result_sku) > 0) {
    echo json_encode(['success' => false, 'message' => 'SKU already exists']);
    exit;
}

// Check if barcode already exists for other products (if provided)
if (!empty($barcode)) {
    $escaped_barcode = mysqli_real_escape_string($conn, $barcode);
    $barcode_check_sql = "SELECT id FROM products WHERE barcode = '$escaped_barcode' AND id != '$escaped_product_id'";
    $result_barcode = mysqli_query($conn, $barcode_check_sql);
    
    if (!$result_barcode || mysqli_num_rows($result_barcode) > 0) {
        echo json_encode(['success' => false, 'message' => 'Barcode already exists']);
        exit;
    }
}

// Update product with proper escaping
$escaped_name = mysqli_real_escape_string($conn, $name);
$escaped_description = mysqli_real_escape_string($conn, $description);
$escaped_barcode_value = !empty($barcode) ? "'$escaped_barcode'" : "NULL";

$update_sql = "
    UPDATE products 
    SET sku = '$escaped_sku', 
        barcode = $escaped_barcode_value, 
        name = '$escaped_name', 
        description = '$escaped_description', 
        category_id = $category_id, 
        supplier_id = $supplier_id, 
        uom_id = $uom_id, 
        cost_price = $cost_price, 
        selling_price = $selling_price, 
        stock_quantity = $stock_quantity, 
        min_stock_level = $min_stock_level, 
        updated_at = CURRENT_TIMESTAMP
    WHERE id = '$escaped_product_id'
";

if (mysqli_query($conn, $update_sql)) {
    // Record stock movement if stock quantity changed
    $stock_difference = $stock_quantity - $old_stock_quantity;
    
    if ($stock_difference != 0) {
        $movement_type = $stock_difference > 0 ? 'in' : 'out';
        $quantity = abs($stock_difference);
        $notes = $stock_difference > 0 ? 'Stock adjustment (increase)' : 'Stock adjustment (decrease)';
        $escaped_notes = mysqli_real_escape_string($conn, $notes);
        
        $movement_sql = "
            INSERT INTO stock_movements (product_id, movement_type, quantity, reference_type, reference_id, created_by, notes) 
            VALUES ('$escaped_product_id', '$movement_type', $quantity, 'adjustment', '$escaped_product_id', 1, '$escaped_notes')
        ";
        mysqli_query($conn, $movement_sql);
    }
    
    echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update product: ' . mysqli_error($conn)]);
}

// No need to close prepared statements since we're using regular queries
?>
