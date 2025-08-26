<?php
include '../include/conn.php';
include '../include/session.php';

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'Supplier ID is required']);
    exit;
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM suppliers WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$supplier = mysqli_fetch_assoc($result);

if ($supplier) {
    echo json_encode($supplier);
} else {
    echo json_encode(['error' => 'Supplier not found']);
}

mysqli_stmt_close($stmt);
?>
