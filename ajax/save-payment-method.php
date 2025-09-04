<?php
include '../include/conn.php';
include '../include/session.php';

header('Content-Type: application/json');

try {
    // Validate input
    if (!isset($_POST['name']) || empty(trim($_POST['name']))) {
        throw new Exception('Payment method name is required');
    }
    
    $name = trim($_POST['name']);
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $isActive = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;
    
    // Validate name length
    if (strlen($name) > 100) {
        throw new Exception('Payment method name must be 100 characters or less');
    }
    
    // Check if name already exists
    $checkQuery = "SELECT id FROM payment_methods WHERE name = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param('s', $name);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'A payment method with this name already exists'
        ]);
        exit;
    }
    
    // Insert new payment method
    $query = "INSERT INTO payment_methods (name, description, is_active, created_at, updated_at) 
              VALUES (?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssi', $name, $description, $isActive);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Payment method added successfully',
            'id' => $conn->insert_id
        ]);
    } else {
        throw new Exception('Failed to insert payment method');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
