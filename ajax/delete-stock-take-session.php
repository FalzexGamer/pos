<?php
include '../include/conn.php';
include '../include/session.php';

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Session ID is required']);
    exit;
}

$session_id = $_GET['id'];

// Check if session exists and is in progress
$check_sql = "SELECT status FROM stock_take_sessions WHERE id = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "i", $session_id);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);
$session = mysqli_fetch_array($check_result);

if (!$session) {
    echo json_encode(['success' => false, 'message' => 'Session not found']);
    exit;
}

if ($session['status'] !== 'in_progress') {
    echo json_encode(['success' => false, 'message' => 'Only in-progress sessions can be deleted']);
    exit;
}

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Delete stock take items
    $delete_items_sql = "DELETE FROM stock_take_items WHERE session_id = ?";
    $delete_items_stmt = mysqli_prepare($conn, $delete_items_sql);
    mysqli_stmt_bind_param($delete_items_stmt, "i", $session_id);
    mysqli_stmt_execute($delete_items_stmt);
    
    // Delete session
    $delete_session_sql = "DELETE FROM stock_take_sessions WHERE id = ?";
    $delete_session_stmt = mysqli_prepare($conn, $delete_session_sql);
    mysqli_stmt_bind_param($delete_session_stmt, "i", $session_id);
    mysqli_stmt_execute($delete_session_stmt);
    
    mysqli_commit($conn);
    
    echo json_encode(['success' => true, 'message' => 'Stock take session deleted successfully']);
    
} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}

mysqli_stmt_close($check_stmt);
mysqli_stmt_close($delete_items_stmt);
mysqli_stmt_close($delete_session_stmt);
?>
