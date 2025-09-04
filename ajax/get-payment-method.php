<?php
include '../include/conn.php';
include '../include/session.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception('Payment method ID is required');
    }
    
    $id = (int)$_GET['id'];
    
    // Get payment method details
    $query = "SELECT id, name, description, is_active, created_at, updated_at 
              FROM payment_methods 
              WHERE id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Payment method not found'
        ]);
        exit;
    }
    
    $paymentMethod = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'payment_method' => [
            'id' => $paymentMethod['id'],
            'name' => htmlspecialchars($paymentMethod['name']),
            'description' => htmlspecialchars($paymentMethod['description']),
            'is_active' => $paymentMethod['is_active'],
            'created_at' => $paymentMethod['created_at'],
            'updated_at' => $paymentMethod['updated_at']
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
