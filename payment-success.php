<?php
include 'include/conn.php';
include 'include/head.php';

// Get ToyyibPay response parameters - check multiple possible parameter names
$refno = '';
$status = '';
$reason = '';
$billcode = '';
$transaction_id = '';

// Check for refno/order_id in various possible parameter names - check both GET and POST
if (isset($_GET['refno']) || isset($_POST['refno'])) {
    $refno = mysqli_real_escape_string($conn, $_GET['refno'] ?? $_POST['refno']);
} elseif (isset($_GET['order_id']) || isset($_POST['order_id'])) {
    $refno = mysqli_real_escape_string($conn, $_GET['order_id'] ?? $_POST['order_id']);
} elseif (isset($_GET['billExternalReferenceNo']) || isset($_POST['billExternalReferenceNo'])) {
    $refno = mysqli_real_escape_string($conn, $_GET['billExternalReferenceNo'] ?? $_POST['billExternalReferenceNo']);
} elseif (isset($_GET['referenceNo']) || isset($_POST['referenceNo'])) {
    $refno = mysqli_real_escape_string($conn, $_GET['referenceNo'] ?? $_POST['referenceNo']);
}

// Check for status in various possible parameter names - check both GET and POST
if (isset($_GET['status']) || isset($_POST['status'])) {
    $status = mysqli_real_escape_string($conn, $_GET['status'] ?? $_POST['status']);
} elseif (isset($_GET['status_id']) || isset($_POST['status_id'])) {
    $status = mysqli_real_escape_string($conn, $_GET['status_id'] ?? $_POST['status_id']);
} elseif (isset($_GET['billpaymentStatus']) || isset($_POST['billpaymentStatus'])) {
    $status = mysqli_real_escape_string($conn, $_GET['billpaymentStatus'] ?? $_POST['billpaymentStatus']);
} elseif (isset($_GET['paymentStatus']) || isset($_POST['paymentStatus'])) {
    $status = mysqli_real_escape_string($conn, $_GET['paymentStatus'] ?? $_POST['paymentStatus']);
}

// Get other parameters - check both GET and POST
if (isset($_GET['reason']) || isset($_POST['reason'])) {
    $reason = mysqli_real_escape_string($conn, $_GET['reason'] ?? $_POST['reason']);
}
if (isset($_GET['billcode']) || isset($_POST['billcode'])) {
    $billcode = mysqli_real_escape_string($conn, $_GET['billcode'] ?? $_POST['billcode']);
}
if (isset($_GET['transaction_id']) || isset($_POST['transaction_id'])) {
    $transaction_id = mysqli_real_escape_string($conn, $_GET['transaction_id'] ?? $_POST['transaction_id']);
}

// Also check for billCode (capital B) variant
if (isset($_GET['billCode']) || isset($_POST['billCode'])) {
    $billcode = mysqli_real_escape_string($conn, $_GET['billCode'] ?? $_POST['billCode']);
}

// Debug information - remove this in production
error_log('ToyyibPay Success Page - All GET parameters: ' . json_encode($_GET));
error_log('ToyyibPay Success Page - All POST parameters: ' . json_encode($_POST));
error_log('ToyyibPay Success Page - Parsed values: refno=' . $refno . ', status=' . $status);

// Temporary debug display - remove this in production
$debug_info = '';
if ((isset($_GET['debug']) && $_GET['debug'] == '1') || (isset($_POST['debug']) && $_POST['debug'] == '1')) {
    $debug_info = '<div class="bg-yellow-100 p-4 m-4 rounded">
        <h4 class="font-bold">Debug Info (Remove in production):</h4>
        <h5>GET Parameters:</h5>
        <pre>' . htmlspecialchars(json_encode($_GET, JSON_PRETTY_PRINT)) . '</pre>
        <h5>POST Parameters:</h5>
        <pre>' . htmlspecialchars(json_encode($_POST, JSON_PRETTY_PRINT)) . '</pre>
        <p>Parsed: refno=' . htmlspecialchars($refno) . ', status=' . htmlspecialchars($status) . '</p>
    </div>';
}

// Initialize variables
$order_info = null;
$is_success = false;
$message = '';

if (!empty($refno)) {
    // Get order information using the reference number (order number)
    $order_query = mysqli_query($conn, "SELECT * FROM customer_order_summary WHERE order_number = '$refno'");
    
    if ($order_query && mysqli_num_rows($order_query) > 0) {
        $order_info = mysqli_fetch_assoc($order_query);
        
        // Check payment status from ToyyibPay - prioritize status_id over everything else
        $is_payment_success = false;
        
        // CRITICAL: Check for status_id=3 FIRST (ToyyibPay failure indicator)
        // This must take precedence over msg=ok
        if ((isset($_GET['status_id']) && $_GET['status_id'] == '3') || 
            (isset($_POST['status_id']) && $_POST['status_id'] == '3') ||
            $status == '3') {
            // DEFINITELY FAILED - redirect to failed page
            $is_payment_success = false;
        } 
        // Check for explicit success indicators
        elseif (isset($_GET['status_id']) && $_GET['status_id'] == '1') {
            $is_payment_success = true;
        } elseif (isset($_POST['status_id']) && $_POST['status_id'] == '1') {
            $is_payment_success = true;
        } elseif ($status == '1' || $status == '2' || strtoupper($status) == 'SUCCESS' || strtoupper($status) == 'PAID') {
            $is_payment_success = true;
        }
        // Check for other failure indicators
        elseif ($status == '0' || strtoupper($status) == 'FAILED' || strtoupper($status) == 'CANCELLED' || 
                isset($_GET['reason']) || isset($_GET['error'])) {
            $is_payment_success = false;
        }
        // Only if no explicit status_id, check msg=ok as fallback success indicator
        elseif (!isset($_GET['status_id']) && !isset($_POST['status_id']) && 
                isset($_GET['msg']) && strtoupper($_GET['msg']) == 'OK') {
            $is_payment_success = true;
        } elseif (!isset($_GET['status_id']) && !isset($_POST['status_id']) &&
                  isset($_POST['msg']) && strtoupper($_POST['msg']) == 'OK') {
            $is_payment_success = true;
        }
        // Default fallback - assume failure if uncertain
        else {
            $is_payment_success = false;
        }
        
        // Debug logging for payment status determination
        error_log('ToyyibPay Status Check - status_id: ' . ($_GET['status_id'] ?? 'not_set') . 
                  ', msg: ' . ($_GET['msg'] ?? 'not_set') . 
                  ', is_payment_success: ' . ($is_payment_success ? 'true' : 'false'));
        
        if ($is_payment_success) {
            $is_success = true;
            
            // Check if we already processed this payment to prevent double stock decrease
            $cart_status_check = mysqli_query($conn, "SELECT status FROM customer_cart WHERE order_number = '$refno' LIMIT 1");
            $already_processed = false;
            
            if ($cart_status_check && mysqli_num_rows($cart_status_check) > 0) {
                $status_row = mysqli_fetch_assoc($cart_status_check);
                if ($status_row['status'] == 'paid') {
                    $already_processed = true;
                    error_log("Order $refno already processed (status: paid) - skipping stock decrease");
                }
            }
            
            // IMPORTANT: Decrease stock FIRST when payment is successful (only if not already processed)
            $stock_updated = false;
            if (!$already_processed) {
                // Get all items in this order from customer_cart table
                $order_items_query = mysqli_query($conn, "SELECT product_id, quantity FROM customer_cart WHERE order_number = '$refno'");
                
                if ($order_items_query && mysqli_num_rows($order_items_query) > 0) {
                    while ($item = mysqli_fetch_assoc($order_items_query)) {
                        $product_id = (int)$item['product_id'];
                        $quantity = (int)$item['quantity'];
                        
                        // Check current stock before decreasing
                        $stock_check = mysqli_query($conn, "SELECT stock_quantity FROM products WHERE id = $product_id");
                        if ($stock_check && mysqli_num_rows($stock_check) > 0) {
                            $stock_info = mysqli_fetch_assoc($stock_check);
                            $current_stock = (int)$stock_info['stock_quantity'];
                            
                            // Only decrease if sufficient stock
                            if ($current_stock >= $quantity) {
                                $update_stock = mysqli_query($conn, "UPDATE products SET stock_quantity = stock_quantity - $quantity WHERE id = $product_id");
                                
                                if (!$update_stock) {
                                    error_log("Failed to decrease stock for product ID $product_id in order $refno: " . mysqli_error($conn));
                                } else {
                                    error_log("Successfully decreased stock for product ID $product_id by $quantity for order $refno");
                                    $stock_updated = true;
                                }
                            } else {
                                error_log("Warning: Insufficient stock to decrease for product ID $product_id in order $refno. Current: $current_stock, Required: $quantity");
                            }
                        }
                    }
                }
            }
            
            // Try to update customer_orders table if it exists
            $update_query = "UPDATE customer_orders SET 
                            payment_status = 'paid', 
                            payment_method = 'ToyyibPay',
                            updated_at = NOW() 
                            WHERE order_number = '$refno'";
            
            $order_updated = mysqli_query($conn, $update_query);
            
            if (!$order_updated) {
                error_log("Note: Could not update customer_orders table for order $refno - " . mysqli_error($conn));
            }
            
            // Update customer_cart status to paid
            $update_cart_query = "UPDATE customer_cart SET status = 'paid' WHERE order_number = '$refno'";
            $cart_updated = mysqli_query($conn, $update_cart_query);
            
            if (!$cart_updated) {
                error_log("Failed to update customer_cart status for order $refno: " . mysqli_error($conn));
            }
            
            if ($stock_updated) {
                $message = 'Payment completed successfully! Thank you for your order.';
            } else if ($already_processed) {
                $message = 'Payment completed successfully! Order already processed.';
            } else {
                $message = 'Payment successful, but stock update failed. Please contact support.';
                error_log("Stock update failed for order $refno - check error logs above");
            }
            
            // Add debug info to help troubleshoot
            error_log("Payment Success Debug - Order: $refno, Stock Updated: " . ($stock_updated ? 'Yes' : 'No') . 
                     ", Already Processed: " . ($already_processed ? 'Yes' : 'No'));
        } else {
            // Payment failed - redirect to failed page
            $failed_url = 'payment-failed.php?';
            $params = [];
            
            // Use the same parameter names that ToyyibPay sends
            if (!empty($refno)) {
                // If we got refno from order_id, pass it as order_id
                if (isset($_GET['order_id']) || isset($_POST['order_id'])) {
                    $params[] = 'order_id=' . urlencode($refno);
                } else {
                    $params[] = 'refno=' . urlencode($refno);
                }
            }
            
            // Pass status parameters using the same names ToyyibPay uses
            if (isset($_GET['status_id']) || isset($_POST['status_id'])) {
                $status_param = $_GET['status_id'] ?? $_POST['status_id'];
                $params[] = 'status_id=' . urlencode($status_param);
            } elseif (!empty($status)) {
                $params[] = 'status=' . urlencode($status);
            }
            
            if (!empty($reason)) $params[] = 'reason=' . urlencode($reason);
            if (!empty($billcode)) $params[] = 'billcode=' . urlencode($billcode);
            if (isset($_GET['msg'])) $params[] = 'msg=' . urlencode($_GET['msg']);
            if (isset($_GET['transaction_id'])) $params[] = 'transaction_id=' . urlencode($_GET['transaction_id']);
            
            header('Location: ' . $failed_url . implode('&', $params));
            exit;
        }
    } else {
        $is_success = false;
        $message = 'Order not found. Please contact support with reference: ' . $refno;
    }
} else {
    // Try to get refno from order_id or other sources if not in URL
    if ((isset($_GET['order_id']) && !empty($_GET['order_id'])) || (isset($_POST['order_id']) && !empty($_POST['order_id']))) {
        $order_id_param = $_GET['order_id'] ?? $_POST['order_id'];
        $refno = mysqli_real_escape_string($conn, $order_id_param);
        
        // Retry the process with found refno
        header('Location: payment-success.php?refno=' . urlencode($refno) . '&' . http_build_query($_GET));
        exit;
    } elseif (isset($_GET['billcode']) && !empty($_GET['billcode'])) {
        // Try to find order by billcode if refno not available
        $billcode_escaped = mysqli_real_escape_string($conn, $_GET['billcode']);
        $bill_query = mysqli_query($conn, "SELECT order_number FROM customer_orders WHERE notes LIKE '%$billcode_escaped%' LIMIT 1");
        
        if ($bill_query && mysqli_num_rows($bill_query) > 0) {
            $bill_result = mysqli_fetch_assoc($bill_query);
            $refno = $bill_result['order_number'];
            
            // Retry the process with found refno
            header('Location: payment-success.php?refno=' . urlencode($refno) . '&' . http_build_query($_GET));
            exit;
        }
    }
    
    $is_success = false;
    $message = 'Invalid payment response. Please contact support with your order details.';
}
?>

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center p-4">
    <?php echo $debug_info; ?>
    <div class="max-w-md w-full">
        <!-- Payment Result Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-8 text-center <?php echo $is_success ? 'bg-gradient-to-r from-green-500 to-emerald-500' : 'bg-gradient-to-r from-red-500 to-red-600'; ?>">
                <?php if ($is_success): ?>
                    <div class="mx-auto w-16 h-16 bg-white rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-check text-3xl text-green-500"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-white mb-2">Payment Successful!</h1>
                <?php else: ?>
                    <div class="mx-auto w-16 h-16 bg-white rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-times text-3xl text-red-500"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-white mb-2">Payment Failed</h1>
                <?php endif; ?>
                <p class="text-white/90"><?php echo htmlspecialchars($message); ?></p>
            </div>

            <!-- Order Details -->
            <?php if ($order_info): ?>
            <div class="px-6 py-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Details</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-600">Order Number:</span>
                        <span class="font-medium text-gray-800"><?php echo htmlspecialchars($order_info['order_number']); ?></span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-600">Table:</span>
                        <span class="font-medium text-gray-800">Table <?php echo htmlspecialchars($order_info['table_id']); ?></span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-600">Total Items:</span>
                        <span class="font-medium text-gray-800"><?php echo htmlspecialchars($order_info['total_items']); ?></span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-medium text-gray-800">RM <?php echo number_format($order_info['subtotal'], 2); ?></span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-600">Tax:</span>
                        <span class="font-medium text-gray-800">RM <?php echo number_format($order_info['tax_amount'], 2); ?></span>
                    </div>
                    <div class="flex justify-between items-center py-3 bg-gray-50 rounded-lg px-3">
                        <span class="text-lg font-semibold text-gray-800">Total Amount:</span>
                        <span class="text-lg font-bold text-blue-600">RM <?php echo number_format($order_info['total_amount'], 2); ?></span>
                    </div>
                </div>
                
                <?php if (!empty($transaction_id)): ?>
                <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 text-sm">Transaction ID:</span>
                        <span class="font-mono text-sm text-gray-800"><?php echo htmlspecialchars($transaction_id); ?></span>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Debug Information (temporary for troubleshooting) -->
                <?php if ($is_success): ?>
                <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="text-sm text-blue-800">
                        <div><strong>Stock Processing Status:</strong></div>
                        <div>Order: <?php echo htmlspecialchars($refno ?? 'N/A'); ?></div>
                        <div>Stock Updated: <?php echo isset($stock_updated) ? ($stock_updated ? 'Yes' : 'No') : 'Unknown'; ?></div>
                        <div>Already Processed: <?php echo isset($already_processed) ? ($already_processed ? 'Yes' : 'No') : 'Unknown'; ?></div>
                        <div class="mt-2 text-xs text-blue-600">Check server error logs for detailed information.</div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="px-6 pb-6 space-y-3">
                <?php if ($is_success): ?>
                    <button onclick="printReceipt()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-print mr-2"></i>
                        Print Receipt
                    </button>
                <?php else: ?>
                    <a href="paymentgateway.php?order_number=<?php echo urlencode($refno); ?>" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-redo mr-2"></i>
                        Retry Payment
                    </a>
                <?php endif; ?>
                
                <a href="customer-order.php<?php echo $order_info ? '?table=' . $order_info['table_id'] : ''; ?>" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Ordering
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6">
            <p class="text-gray-500 text-sm">
                For support, please contact us or visit our counter.
            </p>
        </div>
    </div>
</div>

<script>
function printReceipt() {
    // Open print receipt in new window
    window.open('print-order.php?order_number=<?php echo urlencode($refno); ?>', '_blank');
}

// Auto redirect after 10 seconds if successful
<?php if ($is_success): ?>
setTimeout(function() {
    if (confirm('Would you like to go back to the ordering page?')) {
        window.location.href = 'customer-order.php<?php echo $order_info ? '?table=' . $order_info['table_id'] : ''; ?>';
    }
}, 10000);
<?php endif; ?>
</script>

</body>
</html>
