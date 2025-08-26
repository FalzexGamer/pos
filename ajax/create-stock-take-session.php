<?php
include '../include/conn.php';
include '../include/session.php';

$session_name = trim($_POST['session_name']);
$notes = trim($_POST['notes'] ?? '');
$user_id = $_SESSION['user_id'];

// Validate required fields
if (empty($session_name)) {
    echo json_encode(['success' => false, 'message' => 'Session name is required']);
    exit;
}

// Check if session name already exists
$check_sql = "SELECT id FROM stock_take_sessions WHERE session_name = ? AND status = 'in_progress'";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "s", $session_name);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);

if (mysqli_fetch_array($check_result)) {
    echo json_encode(['success' => false, 'message' => 'A session with this name already exists']);
    exit;
}

// Create new session
$sql = "INSERT INTO stock_take_sessions (session_name, notes, created_by) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssi", $session_name, $notes, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'message' => 'Stock take session created successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}

mysqli_stmt_close($check_stmt);
mysqli_stmt_close($stmt);
?>
