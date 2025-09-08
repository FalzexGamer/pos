<?php
include '../include/conn.php';
include '../include/session.php';

// Get filter parameters
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Build the query
$query = "SELECT s.*, 
                 m.name as member_name,
                 m.email as member_email,
                 u.full_name as cashier_name,
                 COUNT(si.id) as item_count
          FROM sales s
          LEFT JOIN members m ON s.member_id = m.id
          LEFT JOIN users u ON s.user_id = u.id
          LEFT JOIN sale_items si ON s.id = si.sale_id
          WHERE 1=1";

$params = [];
$types = '';

// Add search filter
if (!empty($search)) {
    $query .= " AND (s.invoice_number LIKE ? OR m.name LIKE ? OR m.email LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'sss';
}

// Add date filters
if (!empty($start_date)) {
    $query .= " AND DATE(s.created_at) >= ?";
    $params[] = $start_date;
    $types .= 's';
}

if (!empty($end_date)) {
    $query .= " AND DATE(s.created_at) <= ?";
    $params[] = $end_date;
    $types .= 's';
}

// Add status filter
if (!empty($status)) {
    $query .= " AND s.payment_status = ?";
    $params[] = $status;
    $types .= 's';
}

$query .= " GROUP BY s.id ORDER BY s.created_at DESC";

// Prepare and execute the query
$stmt = mysqli_prepare($conn, $query);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    echo '<tr class="no-data">
            <td colspan="8" class="px-6 py-8 text-center">
                <div class="flex flex-col items-center space-y-4">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-2xl text-gray-400"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">No sales found</h3>
                        <p class="text-gray-500">Try adjusting your search criteria or date range.</p>
                    </div>
                </div>
            </td>
          </tr>';
    exit;
}

while ($sale = mysqli_fetch_assoc($result)) {
    $status_class = '';
    $status_icon = '';
    
    switch ($sale['payment_status']) {
        case 'paid':
            $status_class = 'bg-green-100 text-green-800';
            $status_icon = 'fas fa-check-circle';
            break;
        case 'pending':
            $status_class = 'bg-yellow-100 text-yellow-800';
            $status_icon = 'fas fa-clock';
            break;
        case 'refunded':
            $status_class = 'bg-red-100 text-red-800';
            $status_icon = 'fas fa-undo';
            break;
    }
    
    
    echo '<tr class="hover:bg-gray-50/50 transition-colors duration-200">
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-receipt text-blue-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">' . htmlspecialchars($sale['invoice_number']) . '</div>
                        <div class="text-sm text-gray-500">ID: ' . $sale['id'] . '</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">' . htmlspecialchars($sale['member_name'] ?: 'Walk-in Customer') . '</div>
                <div class="text-sm text-gray-500">' . htmlspecialchars($sale['member_email'] ?: 'No email') . '</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-box text-purple-600 text-sm"></i>
                    </div>
                    <span class="ml-2 text-sm text-gray-900">' . $sale['item_count'] . ' items</span>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-semibold text-gray-900">RM ' . number_format($sale['total_amount'], 2) . '</div>
                <div class="text-sm text-gray-500">Sub: RM ' . number_format($sale['subtotal'], 2) . '</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm text-gray-900 capitalize">' . $sale['payment_method'] . '</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $status_class . '">
                    <i class="' . $status_icon . ' mr-1"></i>
                    ' . ucfirst($sale['payment_status']) . '
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">' . date('M j, Y', strtotime($sale['created_at'])) . '</div>
                <div class="text-sm text-gray-500">' . date('g:i A', strtotime($sale['created_at'])) . '</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex items-center space-x-2">
                    <button onclick="viewSaleDetails(' . $sale['id'] . ')" class="text-blue-600 hover:text-blue-900 p-2 rounded-lg hover:bg-blue-50 transition-colors" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button onclick="printReceipt(' . $sale['id'] . ')" class="text-green-600 hover:text-green-900 p-2 rounded-lg hover:bg-green-50 transition-colors" title="Print Receipt">
                        <i class="fas fa-print"></i>
                    </button>' . 
                    ($sale['payment_status'] == 'paid' ? '
                    <button onclick="refundSale(' . $sale['id'] . ')" class="text-red-600 hover:text-red-900 p-2 rounded-lg hover:bg-red-50 transition-colors" title="Refund Sale">
                        <i class="fas fa-undo"></i>
                    </button>' : '') . '
                </div>
            </td>
          </tr>';
    
    // Also create mobile card version
    echo '<div class="mobile-card p-4 border-b border-gray-200/50">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="font-semibold text-gray-900">' . htmlspecialchars($sale['invoice_number']) . '</h3>
                    <p class="text-sm text-gray-600">' . htmlspecialchars($sale['member_name'] ?: 'Walk-in Customer') . '</p>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $status_class . '">
                    <i class="' . $status_icon . ' mr-1"></i>
                    ' . ucfirst($sale['payment_status']) . '
                </span>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-3">
                <div>
                    <p class="text-xs text-gray-500">Items</p>
                    <p class="font-medium">' . $sale['item_count'] . '</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Total</p>
                    <p class="font-medium text-green-600">RM ' . number_format($sale['total_amount'], 2) . '</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Payment</p>
                    <p class="font-medium capitalize">' . $sale['payment_method'] . '</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Date</p>
                    <p class="font-medium">' . date('M j, Y', strtotime($sale['created_at'])) . '</p>
                </div>
            </div>
            <div class="flex space-x-2">
                <button onclick="viewSaleDetails(' . $sale['id'] . ')" class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                    <i class="fas fa-eye mr-2"></i>View Details
                </button>
                <button onclick="printReceipt(' . $sale['id'] . ')" class="flex-1 bg-green-600 text-white py-2 px-4 rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
                    <i class="fas fa-print mr-2"></i>Print
                </button>' . 
                ($sale['payment_status'] == 'paid' ? '
                <button onclick="refundSale(' . $sale['id'] . ')" class="flex-1 bg-red-600 text-white py-2 px-4 rounded-lg text-sm font-medium hover:bg-red-700 transition-colors">
                    <i class="fas fa-undo mr-2"></i>Refund
                </button>' : '') . '
            </div>
          </div>';
}
?>
