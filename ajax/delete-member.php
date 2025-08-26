<?php
include '../include/conn.php';

$id = $_GET['id'] ?? 0;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Member ID required']);
    exit;
}

// Use prepared statement for security
$stmt = mysqli_prepare($conn, "DELETE FROM members WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
$result = mysqli_stmt_execute($stmt);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Member deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete member: ' . mysqli_error($conn)]);
}

mysqli_stmt_close($stmt);
?>
