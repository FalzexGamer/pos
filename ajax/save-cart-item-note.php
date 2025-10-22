<?php
include '../include/conn.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? 0;
$cart_id = $_POST['cart_id'] ?? 0;
$note = $_POST['note'] ?? '';

if (!$user_id) {
    echo json_encode([
        'success' => false,
        'message' => 'User not authenticated'
    ]);
    exit;
}

if (!$cart_id) {
    echo json_encode([
        'success' => false,
        'message' => 'Cart item ID required'
    ]);
    exit;
}

// Sanitize the note
$note = mysqli_real_escape_string($conn, $note);

// Update cart item note
$query = mysqli_query($conn, "
    UPDATE cart 
    SET notes = '$note' 
    WHERE id = '$cart_id' AND user_id = '$user_id' AND status = 'active'
");

if ($query) {
    echo json_encode([
        'success' => true,
        'message' => 'Note saved successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error saving note: ' . mysqli_error($conn)
    ]);
}
?>
