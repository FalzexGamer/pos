<?php
include '../include/conn.php';
include '../include/session.php';

// Get pagination parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = 10;
$offset = ($page - 1) * $itemsPerPage;

// Get total count
$countQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM payment_methods");
$totalRecords = mysqli_fetch_array($countQuery)['total'];
$totalPages = ceil($totalRecords / $itemsPerPage);

// Get payment methods with pagination
$query = mysqli_query($conn, "
    SELECT id, name, description, is_active, created_at, updated_at 
    FROM payment_methods 
    ORDER BY id ASC
    LIMIT $itemsPerPage OFFSET $offset
");

$payment_methods = [];
while ($method = mysqli_fetch_array($query)) {
    $payment_methods[] = [
        'id' => $method['id'],
        'name' => $method['name'],
        'description' => $method['description'],
        'is_active' => $method['is_active'],
        'created_at' => $method['created_at'],
        'updated_at' => $method['updated_at']
    ];
}

echo json_encode([
    'success' => true,
    'payment_methods' => $payment_methods,
    'total_records' => $totalRecords,
    'total_pages' => $totalPages,
    'current_page' => $page
]);
?>
