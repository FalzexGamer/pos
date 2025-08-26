<?php
// Test Cart System Script
// This script will test the cart functionality

include 'include/conn.php';

echo "<h2>POS System - Cart System Test</h2>";

// Check if cart table exists
$check_cart = mysqli_query($conn, "SHOW TABLES LIKE 'cart'");

if (mysqli_num_rows($check_cart) == 0) {
    echo "<p style='color: red;'>❌ Cart table does not exist. Please run setup_cart_table.php first.</p>";
    exit;
}

echo "<p style='color: green;'>✅ Cart table exists.</p>";

// Check if user is logged in
session_start();
$user_id = $_SESSION['user_id'] ?? 0;

if (!$user_id) {
    echo "<p style='color: red;'>❌ No user logged in. Please log in first.</p>";
    exit;
}

echo "<p style='color: green;'>✅ User logged in (ID: $user_id).</p>";

// Check if products exist
$check_products = mysqli_query($conn, "SELECT COUNT(*) as count FROM products WHERE status = 'active'");
$product_count = mysqli_fetch_assoc($check_products)['count'];

echo "<p style='color: blue;'>📦 Found $product_count active products in database.</p>";

// Show sample products
$sample_products = mysqli_query($conn, "SELECT id, name, sku, barcode, selling_price, stock_quantity FROM products WHERE status = 'active' LIMIT 5");

echo "<h3>Sample Products for Testing:</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #f3f4f6;'><th>ID</th><th>Name</th><th>SKU</th><th>Barcode</th><th>Price</th><th>Stock</th></tr>";

while ($product = mysqli_fetch_assoc($sample_products)) {
    echo "<tr>";
    echo "<td>{$product['id']}</td>";
    echo "<td>{$product['name']}</td>";
    echo "<td>{$product['sku']}</td>";
    echo "<td>{$product['barcode']}</td>";
    echo "<td>RM {$product['selling_price']}</td>";
    echo "<td>{$product['stock_quantity']}</td>";
    echo "</tr>";
}
echo "</table>";

// Check current cart items
$check_cart_items = mysqli_query($conn, "SELECT COUNT(*) as count FROM cart WHERE user_id = $user_id AND status = 'active'");
$cart_count = mysqli_fetch_assoc($check_cart_items)['count'];

echo "<p style='color: blue;'>🛒 Current cart has $cart_count items.</p>";

// Show current cart items
if ($cart_count > 0) {
    $cart_items = mysqli_query($conn, "
        SELECT c.*, p.name 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = $user_id AND c.status = 'active'
    ");
    
    echo "<h3>Current Cart Items:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f3f4f6;'><th>Product</th><th>SKU</th><th>Quantity</th><th>Price</th><th>Subtotal</th></tr>";
    
    while ($item = mysqli_fetch_assoc($cart_items)) {
        echo "<tr>";
        echo "<td>{$item['name']}</td>";
        echo "<td>{$item['sku']}</td>";
        echo "<td>{$item['quantity']}</td>";
        echo "<td>RM {$item['price']}</td>";
        echo "<td>RM {$item['subtotal']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<hr>";
echo "<h3>How to Test:</h3>";
echo "<ol>";
echo "<li>Go to <a href='pos.php' target='_blank'>POS System</a></li>";
echo "<li>Use the product entry field to scan barcodes or enter SKUs</li>";
echo "<li>Products will be automatically added to the cart table</li>";
echo "<li>Check this page again to see updated cart items</li>";
echo "</ol>";

echo "<h3>Test Commands:</h3>";
echo "<p>You can test the system by entering these in the product entry field:</p>";
echo "<ul>";
echo "<li><strong>Barcode:</strong> Enter any barcode from the sample products above</li>";
echo "<li><strong>SKU:</strong> Enter any SKU from the sample products above</li>";
echo "<li><strong>Product Name:</strong> Enter part of any product name</li>";
echo "</ul>";

echo "<p><strong>Expected Behavior:</strong></p>";
echo "<ul>";
echo "<li>✅ Product found → Added to cart → Success message + beep sound</li>";
echo "<li>❌ Product not found → Error message</li>";
echo "<li>❌ Product out of stock → Error message</li>";
echo "</ul>";

echo "<p><a href='pos.php' style='background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to POS System</a></p>";
?>
