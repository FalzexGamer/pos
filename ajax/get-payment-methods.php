<?php
include '../include/conn.php';
include '../include/session.php';

header('Content-Type: application/json');

try {
    // Get parameters
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $status = isset($_GET['status']) ? $_GET['status'] : '';
    $itemsPerPage = 10;
    $offset = ($page - 1) * $itemsPerPage;
    
    // Build WHERE clause
    $whereConditions = [];
    $params = [];
    $paramTypes = '';
    
    if (!empty($search)) {
        $whereConditions[] = "(name LIKE ? OR description LIKE ?)";
        $searchParam = "%$search%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $paramTypes .= 'ss';
    }
    
    if ($status !== '') {
        $whereConditions[] = "is_active = ?";
        $params[] = $status;
        $paramTypes .= 'i';
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM payment_methods $whereClause";
    $countStmt = $conn->prepare($countQuery);
    
    if (!empty($params)) {
        $countStmt->bind_param($paramTypes, ...$params);
    }
    
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $totalRecords = $countResult->fetch_assoc()['total'];
    $totalPages = ceil($totalRecords / $itemsPerPage);
    
    // Get payment methods
    $query = "SELECT id, name, description, is_active, created_at, updated_at 
              FROM payment_methods 
              $whereClause 
              ORDER BY id ASC 
              LIMIT ? OFFSET ?";
    
    $stmt = $conn->prepare($query);
    
    // Add pagination parameters
    $params[] = $itemsPerPage;
    $params[] = $offset;
    $paramTypes .= 'ii';
    
    $stmt->bind_param($paramTypes, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $paymentMethods = [];
    while ($row = $result->fetch_assoc()) {
        $paymentMethods[] = [
            'id' => $row['id'],
            'name' => htmlspecialchars($row['name']),
            'description' => htmlspecialchars($row['description']),
            'is_active' => $row['is_active'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'payment_methods' => $paymentMethods,
        'total_records' => $totalRecords,
        'total_pages' => $totalPages,
        'current_page' => $page,
        'items_per_page' => $itemsPerPage
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
