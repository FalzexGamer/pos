<?php
include '../include/conn.php';
include '../include/session.php';

// Get pagination parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = 10;
$offset = ($page - 1) * $itemsPerPage;

// Get total count
$countQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
$totalRecords = mysqli_fetch_array($countQuery)['total'];
$totalPages = ceil($totalRecords / $itemsPerPage);

// Get users with pagination
$query = mysqli_query($conn, "
    SELECT id, username, full_name, email, phone, role, is_active, last_login, created_at, updated_at
    FROM users 
    ORDER BY created_at DESC 
    LIMIT $itemsPerPage OFFSET $offset
");

$users = [];
while ($user = mysqli_fetch_array($query)) {
    $users[] = [
        'id' => $user['id'],
        'username' => $user['username'],
        'full_name' => $user['full_name'],
        'email' => $user['email'],
        'phone' => $user['phone'],
        'role' => $user['role'],
        'is_active' => $user['is_active'],
        'last_login' => $user['last_login'],
        'created_at' => $user['created_at'],
        'updated_at' => $user['updated_at']
    ];
}

echo json_encode([
    'success' => true,
    'users' => $users,
    'total_pages' => $totalPages,
    'current_page' => $page,
    'total_records' => $totalRecords
]);
?>
