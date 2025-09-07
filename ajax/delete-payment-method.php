<?php
include '../include/conn.php';
include '../include/session.php';

$id = $_POST['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Payment method ID is required']);
    exit;
}

// Check if payment method is being used in sales
$checkQuery = mysqli_query($conn, "SELECT COUNT(*) as count FROM sales WHERE payment_method_id = $id");
$result = mysqli_fetch_array($checkQuery);

if ($result['count'] > 0) {
    echo json_encode(['success' => false, 'message' => 'Cannot delete payment method that is being used in sales records']);
    exit;
}

// Delete the payment method
$query = mysqli_query($conn, "DELETE FROM payment_methods WHERE id = $id");

if ($query) {
    echo json_encode(['success' => true, 'message' => 'Payment method deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete payment method']);
}
?>