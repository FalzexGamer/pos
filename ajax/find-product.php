<?php
// Suppress any output that might interfere with our response
ob_start();

include '../include/conn.php';

if (!$conn || $conn->connect_error) {
    ob_clean();
    echo "ERROR: Database connection failed";
    exit;
}

// Clear any output buffer content
ob_clean();

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$entry = $_POST['entry'] ?? '';

if (empty($entry)) {
    echo "ERROR: Entry is required";
    exit;
}

// Search for product by barcode or SKU only (exact matches)
$entry = mysqli_real_escape_string($conn, $entry); // Sanitize input
$query = "SELECT p.*, c.name as category_name, u.abbreviation as uom_abbr 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          LEFT JOIN uom u ON p.uom_id = u.id 
          WHERE (p.barcode = '$entry' OR p.sku = '$entry') 
          AND p.is_active = 1 
          ORDER BY 
            CASE 
              WHEN p.barcode = '$entry' THEN 1
              WHEN p.sku = '$entry' THEN 2
            END
          LIMIT 1";

$result = mysqli_query($conn, $query);
if (!$result) {
    echo "ERROR: Database query failed: " . mysqli_error($conn);
    exit;
}

if ($result && mysqli_num_rows($result) > 0) {
    $product = mysqli_fetch_assoc($result);
    
    // Check if product is in stock
    if ($product['stock_quantity'] > 0) {
        echo "SUCCESS: " . $product['id'] . "|" . $product['name'] . "|" . $product['sku'] . "|" . $product['barcode'] . "|" . $product['selling_price'] . "|" . $product['stock_quantity'] . "|" . $product['category_name'] . "|" . $product['uom_abbr'];
    } else {
        echo "ERROR: Product \"" . $product['name'] . "\" is out of stock";
    }
} else {
    echo "ERROR: Product not found. Please enter a valid SKU or barcode: " . $entry;
}

// Ensure we always have a proper response
if (!headers_sent()) {
    header('Content-Type: text/plain');
    header('Cache-Control: no-cache, no-store, must-revalidate');
}

// End output buffering and send response
ob_end_flush();
?>
