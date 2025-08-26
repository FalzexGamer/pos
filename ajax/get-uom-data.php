<?php
include '../include/conn.php';
include '../include/session.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['error' => 'UOM ID is required']);
    exit;
}

$id = intval($_GET['id']);

// Fetch UOM data
$sql = "SELECT id, name, abbreviation, description, is_active FROM uom WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($uom = mysqli_fetch_assoc($result)) {
    echo json_encode($uom);
} else {
    echo json_encode(['error' => 'UOM not found']);
}

mysqli_stmt_close($stmt);
?>
