<?php
include '../include/conn.php';
include '../include/session.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Payment method ID is required']);
    exit;
}

$query = mysqli_query($conn, "SELECT * FROM payment_methods WHERE id = $id");

if (!$query || mysqli_num_rows($query) == 0) {
    echo json_encode(['success' => false, 'message' => 'Payment method not found']);
    exit;
}

$payment_method = mysqli_fetch_array($query);

echo json_encode([
    'success' => true,
    'payment_method' => [
        'id' => $payment_method['id'],
        'name' => $payment_method['name'],
        'description' => $payment_method['description'],
        'is_active' => $payment_method['is_active'],
        'created_at' => $payment_method['created_at'],
        'updated_at' => $payment_method['updated_at']
    ]
]);
?>