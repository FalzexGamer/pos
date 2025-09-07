<?php
include '../include/conn.php';
include '../include/session.php';

// Get active payment methods (excluding cash)
$query = mysqli_query($conn, "
    SELECT id, name, description 
    FROM payment_methods 
    WHERE is_active = 1 AND id != 1 
    ORDER BY name
");

$payment_methods = [];
while ($method = mysqli_fetch_array($query)) {
    $payment_methods[] = [
        'id' => $method['id'],
        'name' => $method['name'],
        'description' => $method['description']
    ];
}

echo json_encode([
    'success' => true,
    'payment_methods' => $payment_methods
]);
?>