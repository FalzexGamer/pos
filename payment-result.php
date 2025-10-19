<?php
include 'include/conn.php';

// Get all ToyyibPay response parameters
$refno = '';
$status = '';
$reason = '';
$billcode = '';
$transaction_id = '';
$msg = '';

// Check for refno/order_id in various possible parameter names
if (isset($_GET['refno']) || isset($_POST['refno'])) {
    $refno = mysqli_real_escape_string($conn, $_GET['refno'] ?? $_POST['refno']);
} elseif (isset($_GET['order_id']) || isset($_POST['order_id'])) {
    $refno = mysqli_real_escape_string($conn, $_GET['order_id'] ?? $_POST['order_id']);
} elseif (isset($_GET['billExternalReferenceNo']) || isset($_POST['billExternalReferenceNo'])) {
    $refno = mysqli_real_escape_string($conn, $_GET['billExternalReferenceNo'] ?? $_POST['billExternalReferenceNo']);
}

// Check for status in various possible parameter names
if (isset($_GET['status']) || isset($_POST['status'])) {
    $status = mysqli_real_escape_string($conn, $_GET['status'] ?? $_POST['status']);
} elseif (isset($_GET['status_id']) || isset($_POST['status_id'])) {
    $status = mysqli_real_escape_string($conn, $_GET['status_id'] ?? $_POST['status_id']);
}

// Get other parameters
if (isset($_GET['reason']) || isset($_POST['reason'])) {
    $reason = mysqli_real_escape_string($conn, $_GET['reason'] ?? $_POST['reason']);
}
if (isset($_GET['billcode']) || isset($_POST['billcode'])) {
    $billcode = mysqli_real_escape_string($conn, $_GET['billcode'] ?? $_POST['billcode']);
}
if (isset($_GET['transaction_id']) || isset($_POST['transaction_id'])) {
    $transaction_id = mysqli_real_escape_string($conn, $_GET['transaction_id'] ?? $_POST['transaction_id']);
}
if (isset($_GET['msg']) || isset($_POST['msg'])) {
    $msg = mysqli_real_escape_string($conn, $_GET['msg'] ?? $_POST['msg']);
}

// Debug logging
error_log('ToyyibPay Result Handler - All GET parameters: ' . json_encode($_GET));
error_log('ToyyibPay Result Handler - Parsed values: refno=' . $refno . ', status=' . $status . ', msg=' . $msg);

// Determine if payment was successful
$is_success = false;

// Priority 1: Check for explicit failure indicators
if (isset($_GET['status_id']) && $_GET['status_id'] == '3') {
    $is_success = false;
} elseif (isset($_POST['status_id']) && $_POST['status_id'] == '3') {
    $is_success = false;
} elseif ($status == '3' || $status == '0') {
    $is_success = false;
} elseif (isset($_GET['reason']) || isset($_GET['error'])) {
    $is_success = false;
}
// Priority 2: Check for explicit success indicators
elseif (isset($_GET['status_id']) && $_GET['status_id'] == '1') {
    $is_success = true;
} elseif (isset($_POST['status_id']) && $_POST['status_id'] == '1') {
    $is_success = true;
} elseif ($status == '1' || $status == '2') {
    $is_success = true;
}
// Priority 3: Check msg=ok only if no explicit status_id
elseif (!isset($_GET['status_id']) && !isset($_POST['status_id']) && 
         isset($_GET['msg']) && strtoupper($_GET['msg']) == 'OK') {
    $is_success = true;
}
// Default: Assume failure for safety
else {
    $is_success = false;
}

// Redirect to appropriate page with all parameters
$params = $_GET;
if ($is_success) {
    $redirect_url = 'payment-success.php?' . http_build_query($params);
} else {
    $redirect_url = 'payment-failed.php?' . http_build_query($params);
}

error_log('ToyyibPay Result Handler - Redirecting to: ' . $redirect_url);

header('Location: ' . $redirect_url);
exit();
?>
