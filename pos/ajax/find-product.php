<?php
include '../include/conn.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$entry = $_POST['entry'] ?? '';

if (empty($entry)) {
    echo json_encode([
        'success' => false,
        'message' => 'Entry is required'
    ]);
    exit;
}

// Search for product by barcode or SKU only (exact matches)
$query = "SELECT p.*, c.name as category_name, u.abbreviation as uom_abbr 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          LEFT JOIN uom u ON p.uom_id = u.id 
          WHERE (p.barcode = ? OR p.sku = ?) 
          AND p.is_active = 1 
          ORDER BY 
            CASE 
              WHEN p.barcode = ? THEN 1
              WHEN p.sku = ? THEN 2
            END
          LIMIT 1";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ssss", $entry, $entry, $entry, $entry);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    $product = mysqli_fetch_assoc($result);
    
    // Check if product is in stock
    if ($product['stock_quantity'] > 0) {
        echo json_encode([
            'success' => true,
            'product_id' => $product['id'],
            'product_name' => $product['name'],
            'sku' => $product['sku'],
            'barcode' => $product['barcode'],
            'price' => $product['selling_price'],
            'stock' => $product['stock_quantity'],
            'category' => $product['category_name'],
            'uom' => $product['uom_abbr']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Product "' . $product['name'] . '" is out of stock'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Product not found. Please enter a valid SKU or barcode: ' . $entry
    ]);
}

mysqli_stmt_close($stmt);
?>
