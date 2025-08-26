<?php
include '../include/conn.php';

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
if (empty($sku) || empty($name) || empty($category_id) || empty($supplier_id) || empty($uom_id)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
    exit;
}

// Check if SKU already exists
$check_sku = mysqli_query($conn, "SELECT id FROM products WHERE sku = '$sku'");
if (mysqli_num_rows($check_sku) > 0) {
    echo json_encode(['success' => false, 'message' => 'SKU already exists']);
    exit;
}

// Check if barcode already exists (if provided)
if (!empty($barcode)) {
    $check_barcode = mysqli_query($conn, "SELECT id FROM products WHERE barcode = '$barcode'");
    if (mysqli_num_rows($check_barcode) > 0) {
        echo json_encode(['success' => false, 'message' => 'Barcode already exists']);
        exit;
    }
}

// Insert product
$insert = mysqli_query($conn, "
    INSERT INTO products (sku, barcode, name, description, category_id, supplier_id, uom_id, cost_price, selling_price, stock_quantity, min_stock_level) 
    VALUES ('$sku', " . ($barcode ? "'$barcode'" : "NULL") . ", '$name', '$description', $category_id, $supplier_id, $uom_id, $cost_price, $selling_price, $stock_quantity, $min_stock_level)
");

if ($insert) {
    $product_id = mysqli_insert_id($conn);
    
    // Record initial stock movement if stock quantity > 0
    if ($stock_quantity > 0) {
        mysqli_query($conn, "
            INSERT INTO stock_movements (product_id, movement_type, quantity, reference_type, reference_id, created_by, notes) 
            VALUES ($product_id, 'in', $stock_quantity, 'adjustment', $product_id, 1, 'Initial stock')
        ");
    }
    
    echo json_encode(['success' => true, 'message' => 'Product saved successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save product']);
}
?>
