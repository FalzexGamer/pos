<?php
include 'include/conn.php';
include 'include/head.php';

// Get ToyyibPay response parameters - check multiple possible parameter names
$refno = '';
$status = '';
$reason = '';
$billcode = '';
$error_code = '';

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

// Get other parameters
if (isset($_GET['reason'])) {
    $reason = mysqli_real_escape_string($conn, $_GET['reason']);
}
if (isset($_GET['billcode'])) {
    $billcode = mysqli_real_escape_string($conn, $_GET['billcode']);
}
if (isset($_GET['error_code'])) {
    $error_code = mysqli_real_escape_string($conn, $_GET['error_code']);
}

// Initialize variables
$order_info = null;
$message = '';
$suggestions = [];

if (!empty($refno)) {
    // Get order information using the reference number (order number)
    $order_query = mysqli_query($conn, "SELECT * FROM customer_order_summary WHERE order_number = '$refno'");
    
    if ($order_query && mysqli_num_rows($order_query) > 0) {
        $order_info = mysqli_fetch_assoc($order_query);
        
        // Determine failure reason and provide appropriate message
        if (empty($status) || $status == '0' || $status == '3' || strtoupper($status) == 'FAILED' || strtoupper($status) == 'CANCELLED') {
            switch ($error_code) {
                case 'REJECTED':
                    $message = 'Payment was rejected by your bank or card issuer.';
                    $suggestions = [
                        'Check with your bank if there are any restrictions on your card',
                        'Ensure your account has sufficient funds',
                        'Try using a different payment method'
                    ];
                    break;
                case 'EXPIRED':
                    $message = 'Payment session has expired.';
                    $suggestions = [
                        'Please retry your payment within the allowed time limit',
                        'Complete the payment process quickly after starting'
                    ];
                    break;
                case 'CANCELLED':
                    $message = 'Payment was cancelled by you.';
                    $suggestions = [
                        'You can retry the payment if you wish to complete your order',
                        'Your order is still valid and can be paid for later'
                    ];
                    break;
                case 'INVALID':
                    $message = 'Invalid payment information provided.';
                    $suggestions = [
                        'Please check your card details and try again',
                        'Ensure all required fields are filled correctly'
                    ];
                    break;
                default:
                    if (!empty($reason)) {
                        $message = 'Payment failed: ' . $reason;
                    } else {
                        $message = 'Payment was not successful. Please try again.';
                    }
                    $suggestions = [
                        'Check your internet connection',
                        'Verify your payment details',
                        'Try using a different payment method',
                        'Contact support if the problem persists'
                    ];
            }
        }
        
        // Update order status to reflect failed payment
        $update_query = "UPDATE customer_orders SET 
                        payment_status = 'pending',
                        updated_at = NOW() 
                        WHERE order_number = '$refno'";
        mysqli_query($conn, $update_query);
        
    } else {
        $message = 'Order not found. Please contact support with reference: ' . $refno;
        $suggestions = [
            'Contact our customer service team',
            'Provide the reference number for assistance'
        ];
    }
} else {
    $message = 'Invalid payment response received.';
    $suggestions = [
        'Please try the payment process again',
        'Contact support if the issue continues'
    ];
}
?>

<div class="min-h-screen bg-gradient-to-br from-red-50 to-pink-100 flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Payment Failed Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-8 text-center bg-gradient-to-r from-red-500 to-red-600">
                <div class="mx-auto w-16 h-16 bg-white rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-exclamation-triangle text-3xl text-red-500"></i>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">Payment Failed</h1>
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
                    <div class="flex justify-between items-center py-3 bg-gray-50 rounded-lg px-3">
                        <span class="text-lg font-semibold text-gray-800">Amount to Pay:</span>
                        <span class="text-lg font-bold text-red-600">RM <?php echo number_format($order_info['total_amount'], 2); ?></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Suggestions -->
            <?php if (!empty($suggestions)): ?>
            <div class="px-6 pb-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">What can you do?</h3>
                <div class="space-y-2">
                    <?php foreach ($suggestions as $suggestion): ?>
                    <div class="flex items-start space-x-3 p-3 bg-blue-50 rounded-lg">
                        <i class="fas fa-lightbulb text-blue-500 mt-1"></i>
                        <p class="text-gray-700 text-sm"><?php echo htmlspecialchars($suggestion); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="px-6 pb-6 space-y-3">
                <?php if ($order_info): ?>
                    <a href="paymentgateway.php?order_number=<?php echo urlencode($refno); ?>" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-redo mr-2"></i>
                        Retry Payment
                    </a>
                    
                    <a href="customer-order.php?table=<?php echo $order_info['table_id']; ?>" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-pencil-alt mr-2"></i>
                        Modify Order
                    </a>
                <?php endif; ?>
                
                <button onclick="contactSupport()" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                    <i class="fas fa-headset mr-2"></i>
                    Contact Support
                </button>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="mt-6 bg-white rounded-xl p-4 shadow-sm">
            <div class="text-center">
                <h4 class="font-semibold text-gray-800 mb-2">Need Help?</h4>
                <p class="text-gray-600 text-sm mb-3">
                    Our team is here to assist you with any payment issues.
                </p>
                <div class="flex flex-col sm:flex-row gap-2 justify-center">
                    <a href="tel:+60123456789" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        <i class="fas fa-phone mr-1"></i>
                        Call Us
                    </a>
                    <span class="text-gray-300 hidden sm:inline">|</span>
                    <a href="mailto:support@example.com" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        <i class="fas fa-envelope mr-1"></i>
                        Email Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function contactSupport() {
    // You can customize this based on your contact methods
    const phoneNumber = '+60123456789';
    const email = 'support@example.com';
    
    if (confirm('Contact support via phone or email?')) {
        const choice = confirm('Choose OK for phone call, Cancel for email');
        if (choice) {
            window.open('tel:' + phoneNumber);
        } else {
            window.open('mailto:' + email + '?subject=Payment Issue - Order <?php echo urlencode($refno); ?>');
        }
    }
}

// Show a notification about the failed payment
document.addEventListener('DOMContentLoaded', function() {
    // You can add additional JavaScript here if needed
    console.log('Payment failed page loaded for order: <?php echo $refno; ?>');
});
</script>

</body>
</html>
