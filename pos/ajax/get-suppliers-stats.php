<?php
include __DIR__ . '/../include/conn.php';

// Start session manually to avoid redirect issues
session_start();
date_default_timezone_set("Asia/Kuala_Lumpur");

// Check if user is logged in, but don't redirect for AJAX calls
if (empty($_SESSION['user_id'])) {
    // Return error response instead of redirecting
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

// Get supplier statistics
$stats = [];

// Total suppliers
$query_total = mysqli_query($conn, "SELECT COUNT(*) as count FROM suppliers");
$total = mysqli_fetch_array($query_total);
$stats['total_suppliers'] = $total['count'];

// Active suppliers
$query_active = mysqli_query($conn, "SELECT COUNT(*) as count FROM suppliers WHERE is_active = 1");
$active = mysqli_fetch_array($query_active);
$stats['active_suppliers'] = $active['count'];

// Inactive suppliers
$query_inactive = mysqli_query($conn, "SELECT COUNT(*) as count FROM suppliers WHERE is_active = 0");
$inactive = mysqli_fetch_array($query_inactive);
$stats['inactive_suppliers'] = $inactive['count'];

// Total products
$query_products = mysqli_query($conn, "SELECT COUNT(*) as count FROM products");
$products = mysqli_fetch_array($query_products);
$stats['total_products'] = $products['count'];

// Return JSON response
header('Content-Type: application/json');
echo json_encode($stats);
?>
