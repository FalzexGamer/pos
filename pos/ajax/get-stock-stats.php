<?php
include __DIR__ . '/../include/conn.php';

// Get stock statistics
$stats = [];

// Total products
$query_total = mysqli_query($conn, "SELECT COUNT(*) as count FROM products WHERE is_active = 1");
$total = mysqli_fetch_array($query_total);
$stats['total'] = $total['count'];

// In stock (stock > min_stock_level)
$query_in_stock = mysqli_query($conn, "
    SELECT COUNT(*) as count 
    FROM products 
    WHERE stock_quantity > min_stock_level 
    AND stock_quantity > 0 
    AND is_active = 1
");
$in_stock = mysqli_fetch_array($query_in_stock);
$stats['in_stock'] = $in_stock['count'];

// Low stock (stock <= min_stock_level but > 0)
$query_low_stock = mysqli_query($conn, "
    SELECT COUNT(*) as count 
    FROM products 
    WHERE stock_quantity <= min_stock_level 
    AND stock_quantity > 0 
    AND is_active = 1
");
$low_stock = mysqli_fetch_array($query_low_stock);
$stats['low_stock'] = $low_stock['count'];

// Out of stock (stock = 0)
$query_out_stock = mysqli_query($conn, "
    SELECT COUNT(*) as count 
    FROM products 
    WHERE stock_quantity = 0 
    AND is_active = 1
");
$out_stock = mysqli_fetch_array($query_out_stock);
$stats['out_stock'] = $out_stock['count'];

// Return JSON response
header('Content-Type: application/json');
echo json_encode($stats);
?>
