<?php
include '../include/conn.php';
include '../include/session.php';

header('Content-Type: application/json');

try {
    // Validate input
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        throw new Exception('Payment method ID is required');
    }
    
    if (!isset($_POST['name']) || empty(trim($_POST['name']))) {
        throw new Exception('Payment method name is required');
    }
    
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $isActive = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;
    
    // Validate name length
    if (strlen($name) > 100) {
        throw new Exception('Payment method name must be 100 characters or less');
    }
    
    // Check if payment method exists
    $checkQuery = "SELECT id FROM payment_methods WHERE id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param('i', $id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Payment method not found'
        ]);
        exit;
    }
    
    // Check if name already exists for other payment methods
    $nameCheckQuery = "SELECT id FROM payment_methods WHERE name = ? AND id != ?";
    $nameCheckStmt = $conn->prepare($nameCheckQuery);
    $nameCheckStmt->bind_param('si', $name, $id);
    $nameCheckStmt->execute();
    $nameCheckResult = $nameCheckStmt->get_result();
    
    if ($nameCheckResult->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'A payment method with this name already exists'
        ]);
        exit;
    }
    
    // Update payment method
    $query = "UPDATE payment_methods 
              SET name = ?, description = ?, is_active = ?, updated_at = CURRENT_TIMESTAMP 
              WHERE id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssii', $name, $description, $isActive, $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Payment method updated successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No changes were made to the payment method'
            ]);
        }
    } else {
        throw new Exception('Failed to update payment method');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
