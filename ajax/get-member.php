<?php
include '../include/conn.php';

$id = $_GET['id'] ?? 0;

if (!$id) {
    echo json_encode(['error' => 'Member ID required']);
    exit;
}

// Use prepared statement for security
$stmt = mysqli_prepare($conn, "SELECT * FROM members WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$member = mysqli_fetch_array($result);

if (!$member) {
    echo json_encode(['error' => 'Member not found']);
    exit;
}

echo json_encode($member);
mysqli_stmt_close($stmt);
?>
