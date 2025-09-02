<?php
session_start();
require_once 'include/conn.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'open_session':
                $opening_amount = floatval($_POST['opening_amount']);
                $notes = trim($_POST['notes']);
                
                // Check if user already has an open session
                $check_query = "SELECT id FROM sales_sessions WHERE user_id = ? AND status = 'open'";
                $check_stmt = $conn->prepare($check_query);
                $check_stmt->bind_param("i", $user_id);
                $check_stmt->execute();
                $result = $check_stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $error = "You already have an open session. Please close it first.";
                } else {
                    // Create new session
                    $insert_query = "INSERT INTO sales_sessions (user_id, opening_amount, notes) VALUES (?, ?, ?)";
                    $insert_stmt = $conn->prepare($insert_query);
                    $insert_stmt->bind_param("ids", $user_id, $opening_amount, $notes);
                    
                    if ($insert_stmt->execute()) {
                        $message = "Session opened successfully with opening amount: RM " . number_format($opening_amount, 2);
                    } else {
                        $error = "Error opening session: " . $conn->error;
                    }
                }
                break;
                
            case 'close_session':
                $closing_amount = floatval($_POST['closing_amount']);
                $notes = trim($_POST['notes']);
                
                // Get current open session
                $get_session_query = "SELECT id, opening_amount, total_sales FROM sales_sessions WHERE user_id = ? AND status = 'open'";
                $get_session_stmt = $conn->prepare($get_session_query);
                $get_session_stmt->bind_param("i", $user_id);
                $get_session_stmt->execute();
                $session_result = $get_session_stmt->get_result();
                
                if ($session_result->num_rows > 0) {
                    $session = $session_result->fetch_assoc();
                    $session_id = $session['id'];
                    
                    // Close the session
                    $close_query = "UPDATE sales_sessions SET 
                                   session_end = NOW(), 
                                   closing_amount = ?, 
                                   status = 'closed',
                                   notes = CONCAT(IFNULL(notes, ''), ' | ', ?)
                                   WHERE id = ?";
                    $close_stmt = $conn->prepare($close_query);
                    $close_stmt->bind_param("dsi", $closing_amount, $notes, $session_id);
                    
                    if ($close_stmt->execute()) {
                        $message = "Session closed successfully. Closing amount: RM " . number_format($closing_amount, 2);
                    } else {
                        $error = "Error closing session: " . $conn->error;
                    }
                } else {
                    $error = "No open session found.";
                }
                break;
                
            case 'topup_opening':
                $topup_amount = floatval($_POST['topup_amount']);
                $notes = trim($_POST['notes']);
                
                if ($topup_amount <= 0) {
                    $error = "Top-up amount must be greater than zero.";
                    break;
                }
                
                // Get current open session
                $get_session_query = "SELECT id, opening_amount FROM sales_sessions WHERE user_id = ? AND status = 'open'";
                $get_session_stmt = $conn->prepare($get_session_query);
                $get_session_stmt->bind_param("i", $user_id);
                $get_session_stmt->execute();
                $session_result = $get_session_stmt->get_result();
                
                if ($session_result->num_rows > 0) {
                    $session = $session_result->fetch_assoc();
                    $session_id = $session['id'];
                    $new_opening_amount = $session['opening_amount'] + $topup_amount;
                    
                    // Update the opening amount
                    $update_query = "UPDATE sales_sessions SET 
                                   opening_amount = ?,
                                   notes = CONCAT(IFNULL(notes, ''), ' | Top-up: RM ', ?, ' at ', NOW())
                                   WHERE id = ?";
                    $update_stmt = $conn->prepare($update_query);
                    $update_stmt->bind_param("dsi", $new_opening_amount, $topup_amount, $session_id);
                    
                    if ($update_stmt->execute()) {
                        $message = "Opening amount topped up successfully! Added RM " . number_format($topup_amount, 2) . ". New total: RM " . number_format($new_opening_amount, 2);
                        // Redirect to refresh the page and show updated amounts
                        header("Location: " . $_SERVER['PHP_SELF'] . "?success=topup");
                        exit();
                    } else {
                        $error = "Error updating opening amount: " . $conn->error;
                    }
                } else {
                    $error = "No open session found.";
                }
                break;
        }
    }
}

// Get current session status
$current_session_query = "SELECT * FROM sales_sessions WHERE user_id = ? AND status = 'open' ORDER BY session_start DESC LIMIT 1";
$current_session_stmt = $conn->prepare($current_session_query);
$current_session_stmt->bind_param("i", $user_id);
$current_session_stmt->execute();
$current_session_result = $current_session_stmt->get_result();
$current_session = $current_session_result->fetch_assoc();

// Get recent sessions with payment method breakdowns
$recent_sessions_query = "SELECT 
    ss.*,
    COALESCE(cash_sales.cash_total, 0) as cash_sales,
    COALESCE(card_sales.card_total, 0) as card_sales,
    COALESCE(ewallet_sales.ewallet_total, 0) as ewallet_sales
FROM sales_sessions ss
LEFT JOIN (
    SELECT session_id, SUM(total_amount) as cash_total 
    FROM sales 
    WHERE payment_method = 'cash' 
    GROUP BY session_id
) cash_sales ON ss.id = cash_sales.session_id
LEFT JOIN (
    SELECT session_id, SUM(total_amount) as card_total 
    FROM sales 
    WHERE payment_method = 'card' 
    GROUP BY session_id
) card_sales ON ss.id = card_sales.session_id
LEFT JOIN (
    SELECT session_id, SUM(total_amount) as ewallet_total 
    FROM sales 
    WHERE payment_method = 'ewallet' 
    GROUP BY session_id
) ewallet_sales ON ss.id = ewallet_sales.session_id
WHERE ss.user_id = ? 
ORDER BY ss.session_start DESC 
LIMIT 10";
$recent_sessions_stmt = $conn->prepare($recent_sessions_query);
$recent_sessions_stmt->bind_param("i", $user_id);
$recent_sessions_stmt->execute();
$recent_sessions_result = $recent_sessions_stmt->get_result();

// Get today's sales breakdown by payment method if session is open
$today_sales = 0;
$today_cash_sales = 0;
$today_card_sales = 0;
$today_ewallet_sales = 0;

if ($current_session) {
    $today_sales_query = "SELECT 
        SUM(total_amount) as total,
        SUM(CASE WHEN payment_method = 'cash' THEN total_amount ELSE 0 END) as cash_total,
        SUM(CASE WHEN payment_method = 'card' THEN total_amount ELSE 0 END) as card_total,
        SUM(CASE WHEN payment_method = 'ewallet' THEN total_amount ELSE 0 END) as ewallet_total
    FROM sales 
    WHERE user_id = ? AND DATE(created_at) = CURDATE()";
    $today_sales_stmt = $conn->prepare($today_sales_query);
    $today_sales_stmt->bind_param("i", $user_id);
    $today_sales_stmt->execute();
    $today_sales_result = $today_sales_stmt->get_result();
    $today_sales_row = $today_sales_result->fetch_assoc();
    $today_sales = $today_sales_row['total'] ?? 0;
    $today_cash_sales = $today_sales_row['cash_total'] ?? 0;
    $today_card_sales = $today_sales_row['card_total'] ?? 0;
    $today_ewallet_sales = $today_sales_row['ewallet_total'] ?? 0;
}

// Get session statistics with payment method breakdowns
$session_stats_query = "SELECT 
    COUNT(*) as total_sessions,
    SUM(total_sales) as total_revenue,
    AVG(total_sales) as avg_session_revenue,
    SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) as closed_sessions,
    SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open_sessions
FROM sales_sessions     
WHERE user_id = ?";
$session_stats_stmt = $conn->prepare($session_stats_query);
$session_stats_stmt->bind_param("i", $user_id);
$session_stats_stmt->execute();
$session_stats_result = $session_stats_stmt->get_result();
$session_stats = $session_stats_result->fetch_assoc();

// Get payment method breakdowns for all sessions
$payment_breakdown_query = "SELECT 
    SUM(CASE WHEN s.payment_method = 'cash' THEN s.total_amount ELSE 0 END) as total_cash,
    SUM(CASE WHEN s.payment_method = 'card' THEN s.total_amount ELSE 0 END) as total_card,
    SUM(CASE WHEN s.payment_method = 'ewallet' THEN s.total_amount ELSE 0 END) as total_ewallet
FROM sales s
JOIN sales_sessions ss ON s.session_id = ss.id
WHERE ss.user_id = ?";
$payment_breakdown_stmt = $conn->prepare($payment_breakdown_query);
$payment_breakdown_stmt->bind_param("i", $user_id);
$payment_breakdown_stmt->execute();
$payment_breakdown_result = $payment_breakdown_stmt->get_result();
$payment_breakdown = $payment_breakdown_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opening/Closing Management - POS System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
</head>
<body class="bg-gray-100">
    <?php include 'include/header.php'; ?>
    <?php include 'include/sidebar.php'; ?>

    <div class="main-content lg:ml-64 pt-16">
        <div class="p-6">
            <!-- Page Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Opening/Closing Management</h1>
                    <p class="text-gray-600">Manage your daily cash register sessions</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="printReport()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-print mr-2"></i>Print Report
                    </button>
                    <a href="dashboard.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>

            <!-- Alert Messages -->
            <?php if ($message): ?>
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                        <span class="text-green-800 font-medium"><?php echo $message; ?></span>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['success']) && $_GET['success'] == 'topup'): ?>
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                        <span class="text-green-800 font-medium">Opening amount topped up successfully! The page has been refreshed to show the updated amounts.</span>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl mr-3"></i>
                        <span class="text-red-800 font-medium"><?php echo $error; ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Session Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-cash-register text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Sessions</p>
                            <p class="text-2xl font-bold text-gray-900"><?= $session_stats['total_sessions'] ?></p>
                            <p class="text-xs text-gray-500">All time</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-dollar-sign text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                            <p class="text-2xl font-bold text-gray-900">RM <?= number_format($session_stats['total_revenue'], 2) ?></p>
                            <p class="text-xs text-gray-500">All sessions</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <i class="fas fa-chart-line text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Avg Session</p>
                            <p class="text-2xl font-bold text-gray-900">RM <?= number_format($session_stats['avg_session_revenue'], 2) ?></p>
                            <p class="text-xs text-gray-500">Per session</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <i class="fas fa-clock text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Active Sessions</p>
                            <p class="text-2xl font-bold text-gray-900"><?= $session_stats['open_sessions'] ?></p>
                            <p class="text-xs text-gray-500">Currently open</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Method Breakdown -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-money-bill-wave text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Cash Sales</p>
                            <p class="text-2xl font-bold text-gray-900">RM <?= number_format($payment_breakdown['total_cash'], 2) ?></p>
                            <p class="text-xs text-gray-500">Total cash payments</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-credit-card text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Card Sales</p>
                            <p class="text-2xl font-bold text-gray-900">RM <?= number_format($payment_breakdown['total_card'], 2) ?></p>
                            <p class="text-xs text-gray-500">Total card payments</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <i class="fas fa-mobile-alt text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">E-Wallet Sales</p>
                            <p class="text-2xl font-bold text-gray-900">RM <?= number_format($payment_breakdown['total_ewallet'], 2) ?></p>
                            <p class="text-xs text-gray-500">Total e-wallet payments</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Session Status and Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Current Session Status -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Session Status</h3>
                        
                        <?php if ($current_session): ?>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                                        <div class="flex items-center">
                                            <i class="fas fa-circle text-green-500 mr-3"></i>
                                            <span class="text-gray-700 font-medium">Status</span>
                                        </div>
                                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">
                                            Active Session
                                        </span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                                        <div class="flex items-center">
                                            <i class="fas fa-clock text-blue-500 mr-3"></i>
                                            <span class="text-gray-700 font-medium">Started</span>
                                        </div>
                                        <span class="font-semibold text-gray-800">
                                            <?php echo date('d/m/Y H:i', strtotime($current_session['session_start'])); ?>
                                        </span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                                        <div class="flex items-center">
                                            <i class="fas fa-money-bill-wave text-green-500 mr-3"></i>
                                            <span class="text-gray-700 font-medium">Opening Amount</span>
                                        </div>
                                        <span class="font-bold text-green-600 text-lg">
                                            RM <?php echo number_format($current_session['opening_amount'], 2); ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Total Expected -->
                                    <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg border-2 border-yellow-200">
                                        <div class="flex items-center">
                                            <i class="fas fa-calculator text-yellow-500 mr-3"></i>
                                            <span class="text-gray-700 font-medium">Total Expected</span>
                                        </div>
                                        <span class="font-bold text-yellow-600 text-lg">
                                            RM <?php echo number_format($current_session['opening_amount'] + $today_sales, 2); ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg">
                                        <div class="flex items-center">
                                            <i class="fas fa-chart-line text-purple-500 mr-3"></i>
                                            <span class="text-gray-700 font-medium">Today's Sales</span>
                                        </div>
                                        <span class="font-bold text-purple-600 text-lg">
                                            RM <?php echo number_format($today_sales, 2); ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Today's Sales Breakdown -->
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                            <div class="flex items-center">
                                                <i class="fas fa-money-bill-wave text-green-500 mr-2"></i>
                                                <span class="text-gray-600 text-sm">Cash</span>
                                            </div>
                                            <span class="font-semibold text-green-600">
                                                RM <?php echo number_format($today_cash_sales, 2); ?>
                                            </span>
                                        </div>
                                        
                                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                            <div class="flex items-center">
                                                <i class="fas fa-credit-card text-blue-500 mr-2"></i>
                                                <span class="text-gray-600 text-sm">Card</span>
                                            </div>
                                            <span class="font-semibold text-blue-600">
                                                RM <?php echo number_format($today_card_sales, 2); ?>
                                            </span>
                                        </div>
                                        
                                        <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                                            <div class="flex items-center">
                                                <i class="fas fa-mobile-alt text-purple-500 mr-2"></i>
                                                <span class="text-gray-600 text-sm">E-Wallet</span>
                                            </div>
                                            <span class="font-semibold text-purple-600">
                                                RM <?php echo number_format($today_ewallet_sales, 2); ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                                                         <!-- Expected Closing Breakdown -->
                                     <div class="space-y-2">
                                         <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                                             <div class="flex items-center">
                                                 <i class="fas fa-money-bill-wave text-yellow-500 mr-2"></i>
                                                 <span class="text-gray-600 text-sm">Expected Cash</span>
                                             </div>
                                             <span class="font-semibold text-yellow-600">
                                                 RM <?php echo number_format($current_session['opening_amount'] + $today_cash_sales, 2); ?>
                                             </span>
                                         </div>
                                         
                                         <div class="flex items-center justify-between p-3 bg-indigo-50 rounded-lg">
                                             <div class="flex items-center">
                                                 <i class="fas fa-globe text-indigo-500 mr-2"></i>
                                                 <span class="text-gray-600 text-sm">Expected Online</span>
                                             </div>
                                             <span class="font-semibold text-indigo-600">
                                                 RM <?php echo number_format($today_card_sales + $today_ewallet_sales, 2); ?>
                                             </span>
                                         </div>
                                     </div>
                                    
                                    <?php if ($current_session['notes']): ?>
                                        <div class="p-4 bg-gray-50 rounded-lg">
                                            <div class="flex items-start">
                                                <i class="fas fa-sticky-note text-gray-500 mr-3 mt-1"></i>
                                                <div>
                                                    <span class="text-gray-700 font-medium block mb-1">Notes</span>
                                                    <span class="text-gray-600 text-sm"><?php echo htmlspecialchars($current_session['notes']); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-12">
                                <div class="mb-6">
                                    <i class="fas fa-times-circle text-6xl text-gray-300"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-600 mb-2">No Active Session</h3>
                                <p class="text-gray-500">Start a new session to begin tracking your cash register</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                        
                        <?php if (!$current_session): ?>
                            <!-- Open Session Form -->
                            <form method="POST" class="space-y-4">
                                <input type="hidden" name="action" value="open_session">
                                
                                <div>
                                    <label for="opening_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-money-bill mr-2 text-green-500"></i>
                                        Opening Amount (RM)
                                    </label>
                                    <input type="number" 
                                           id="opening_amount" 
                                           name="opening_amount" 
                                           step="0.01" 
                                           min="0" 
                                           required
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                           placeholder="0.00">
                                </div>
                                
                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-sticky-note mr-2 text-blue-500"></i>
                                        Notes (Optional)
                                    </label>
                                    <textarea id="notes" 
                                              name="notes" 
                                              rows="3"
                                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                              placeholder="Enter any notes for this session"></textarea>
                                </div>
                                
                                <button type="submit" 
                                        class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200">
                                    <i class="fas fa-play mr-2"></i>Open Session
                                </button>
                            </form>
                        <?php else: ?>
                            <!-- Top-up Opening Button -->
                            <div class="mb-6">
                                <button onclick="openTopupModal()" 
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200 mb-4">
                                    <i class="fas fa-plus mr-2"></i>Top-up Opening Amount
                                </button>
                                
                                <div class="text-center text-sm text-gray-600">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Current: RM <?php echo number_format($current_session['opening_amount'], 2); ?>
                                </div>
                            </div>
                            
                            <!-- Close Session Form -->
                            <form method="POST" class="space-y-4">
                                <input type="hidden" name="action" value="close_session">
                                
                                <div>
                                    <label for="closing_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-money-bill mr-2 text-red-500"></i>
                                        Closing Amount (RM)
                                    </label>
                                    <input type="number" 
                                           id="closing_amount" 
                                           name="closing_amount" 
                                           step="0.01" 
                                           min="0" 
                                           required
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                           placeholder="0.00">
                                </div>
                                
                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-sticky-note mr-2 text-blue-500"></i>
                                        Closing Notes (Optional)
                                    </label>
                                    <textarea id="notes" 
                                              name="notes" 
                                              rows="3"
                                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                              placeholder="Enter closing notes"></textarea>
                                </div>
                                
                                <button type="submit" 
                                        class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200">
                                    <i class="fas fa-stop mr-2"></i>Close Session
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Recent Sessions Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Sessions</h3>
                </div>
                <div class="overflow-x-auto">
                    <table id="sessionsTable" class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Session</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">End Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Opening</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Closing</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cash</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Card</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">E-Wallet</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Sales</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if ($recent_sessions_result && $recent_sessions_result->num_rows > 0): ?>
                                <?php while ($session = $recent_sessions_result->fetch_assoc()): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                                                #<?php echo $session['id']; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <?php echo date('d/m/Y H:i', strtotime($session['session_start'])); ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <?php echo $session['session_end'] ? date('d/m/Y H:i', strtotime($session['session_end'])) : '<span class="text-gray-400">-</span>'; ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            RM <?php echo number_format($session['opening_amount'], 2); ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <?php if ($session['closing_amount']): ?>
                                                RM <?php echo number_format($session['closing_amount'], 2); ?>
                                            <?php else: ?>
                                                <span class="text-gray-400">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            RM <?php echo number_format($session['cash_sales'], 2); ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            RM <?php echo number_format($session['card_sales'], 2); ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            RM <?php echo number_format($session['ewallet_sales'], 2); ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 font-semibold">
                                            RM <?php echo number_format($session['total_sales'], 2); ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php if ($session['status'] == 'open'): ?>
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                    <i class="fas fa-circle mr-1 text-xs"></i>Active
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                    <i class="fas fa-circle mr-1 text-xs"></i>Closed
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="px-6 py-4 text-center text-gray-500">
                                        <div class="text-center">
                                            <i class="fas fa-inbox text-4xl mb-4 text-gray-300"></i>
                                            <p class="text-lg font-medium">No sessions found</p>
                                            <p class="text-sm">Start your first session to see history here</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Top-up Opening Modal -->
    <div id="topupModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-plus-circle text-blue-500 mr-2"></i>
                            Top-up Opening Amount
                        </h3>
                        <button onclick="closeTopupModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <form method="POST" class="px-6 py-4">
                    <input type="hidden" name="action" value="topup_opening">
                    
                    <div class="mb-4">
                        <label for="topup_amount" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-money-bill mr-2 text-green-500"></i>
                            Top-up Amount (RM)
                        </label>
                        <input type="number" 
                               id="topup_amount" 
                               name="topup_amount" 
                               step="0.01" 
                               min="0.01" 
                               required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="0.00">
                    </div>
                    
                    <div class="mb-6">
                        <label for="topup_notes" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-sticky-note mr-2 text-blue-500"></i>
                            Notes (Optional)
                        </label>
                        <textarea id="topup_notes" 
                                  name="notes" 
                                  rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                  placeholder="Enter reason for top-up"></textarea>
                    </div>
                    
                    <div class="flex space-x-3">
                        <button type="button" 
                                onclick="closeTopupModal()" 
                                class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                            <i class="fas fa-plus mr-2"></i>Top-up
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#sessionsTable').DataTable({
                pageLength: 10,
                order: [[1, 'desc']],
                responsive: true
            });

            // Auto-focus on amount input when page loads
            const openingAmount = document.getElementById('opening_amount');
            const closingAmount = document.getElementById('closing_amount');
            
            if (openingAmount) {
                openingAmount.focus();
            }
            if (closingAmount) {
                closingAmount.focus();
            }
        });

        // Format currency inputs
        function formatCurrency(input) {
            let value = input.value.replace(/[^\d.]/g, '');
            if (value) {
                value = parseFloat(value).toFixed(2);
                input.value = value;
            }
        }

        // Add event listeners for currency formatting
        const amountInputs = document.querySelectorAll('input[type="number"]');
        amountInputs.forEach(input => {
            input.addEventListener('blur', function() {
                formatCurrency(this);
            });
        });
        
        // Add event listener for top-up amount input
        const topupAmountInput = document.getElementById('topup_amount');
        if (topupAmountInput) {
            topupAmountInput.addEventListener('blur', function() {
                formatCurrency(this);
            });
        }

        function printReport() {
            window.print();
        }

        function openTopupModal() {
            document.getElementById('topupModal').classList.remove('hidden');
            document.getElementById('topup_amount').focus();
        }
        
        function closeTopupModal() {
            document.getElementById('topupModal').classList.add('hidden');
        }
    </script>
</body>
</html>

