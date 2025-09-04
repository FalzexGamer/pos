<?php
include '../include/conn.php';
include '../include/session.php';

header('Content-Type: application/json');

try {
    // Validate input
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        throw new Exception('Payment method ID is required');
    }
    
    $id = (int)$_POST['id'];
    
    // Check if payment method exists
    $checkQuery = "SELECT id, name FROM payment_methods WHERE id = ?";
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
    
    $paymentMethod = $checkResult->fetch_assoc();
    
    // Check if payment method is being used in sales (optional safety check)
    // You can uncomment this if you want to prevent deletion of payment methods that are in use
    /*
    $usageQuery = "SELECT COUNT(*) as usage_count FROM sales WHERE payment_method_id = ?";
    $usageStmt = $conn->prepare($usageQuery);
    $usageStmt->bind_param('i', $id);
    $usageStmt->execute();
    $usageResult = $usageStmt->get_result();
    $usageCount = $usageResult->fetch_assoc()['usage_count'];
    
    if ($usageCount > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Cannot delete payment method. It is being used in ' . $usageCount . ' sale(s).'
        ]);
        exit;
    }
    */
    
    // Delete payment method
    $query = "DELETE FROM payment_methods WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Payment method "' . $paymentMethod['name'] . '" deleted successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete payment method'
            ]);
        }
    } else {
        throw new Exception('Failed to delete payment method');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
