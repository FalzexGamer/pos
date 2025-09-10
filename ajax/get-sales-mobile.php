<?php
include '../include/conn.php';
include '../include/session.php';

// Get filter parameters
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Get pagination parameters
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

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

$query .= " GROUP BY s.id ORDER BY s.created_at DESC LIMIT $per_page OFFSET $offset";

// Get total count for pagination
$count_query = "SELECT COUNT(DISTINCT s.id) as total
                FROM sales s
                LEFT JOIN members m ON s.member_id = m.id
                LEFT JOIN users u ON s.user_id = u.id
                LEFT JOIN sale_items si ON s.id = si.sale_id
                WHERE 1=1";

$count_params = [];
$count_types = '';

// Add the same filters to count query
if (!empty($search)) {
    $count_query .= " AND (s.invoice_number LIKE ? OR m.name LIKE ? OR m.email LIKE ?)";
    $search_param = "%$search%";
    $count_params[] = $search_param;
    $count_params[] = $search_param;
    $count_params[] = $search_param;
    $count_types .= 'sss';
}

if (!empty($start_date)) {
    $count_query .= " AND DATE(s.created_at) >= ?";
    $count_params[] = $start_date;
    $count_types .= 's';
}

if (!empty($end_date)) {
    $count_query .= " AND DATE(s.created_at) <= ?";
    $count_params[] = $end_date;
    $count_types .= 's';
}

if (!empty($status)) {
    $count_query .= " AND s.payment_status = ?";
    $count_params[] = $status;
    $count_types .= 's';
}

// Execute count query
$total_records = 0;
if (!empty($count_params)) {
    $count_stmt = mysqli_prepare($conn, $count_query);
    mysqli_stmt_bind_param($count_stmt, $count_types, ...$count_params);
    mysqli_stmt_execute($count_stmt);
    
    $count_result = mysqli_stmt_result_metadata($count_stmt);
    if ($count_result) {
        $count_fields = mysqli_fetch_fields($count_result);
        $count_field_names = array();
        foreach ($count_fields as $field) {
            $count_field_names[] = $field->name;
        }
        mysqli_free_result($count_result);
        
        $count_bind_vars = array();
        $count_bind_vars[] = $count_stmt;
        foreach ($count_field_names as $field) {
            $count_bind_vars[] = &$$field;
        }
        call_user_func_array('mysqli_stmt_bind_result', $count_bind_vars);
        
        if (mysqli_stmt_fetch($count_stmt)) {
            $total_records = $total;
        }
        mysqli_stmt_close($count_stmt);
    }
} else {
    $count_result = mysqli_query($conn, $count_query);
    if ($count_result) {
        $count_row = mysqli_fetch_assoc($count_result);
        $total_records = $count_row['total'];
    }
}

$total_pages = ceil($total_records / $per_page);

// Prepare and execute the main query with compatibility fix
if (!empty($params)) {
    // Use prepared statements for security
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    
    // Get result using compatible method
    $result = mysqli_stmt_result_metadata($stmt);
    if ($result) {
        $fields = mysqli_fetch_fields($result);
        $field_names = array();
        foreach ($fields as $field) {
            $field_names[] = $field->name;
        }
        mysqli_free_result($result);
        
        // Bind result variables
        $bind_vars = array();
        $bind_vars[] = $stmt;
        foreach ($field_names as $field) {
            $bind_vars[] = &$$field;
        }
        call_user_func_array('mysqli_stmt_bind_result', $bind_vars);
        
        // Store results in array
        $sales_data = array();
        while (mysqli_stmt_fetch($stmt)) {
            $row = array();
            foreach ($field_names as $field) {
                $row[$field] = $$field;
            }
            $sales_data[] = $row;
        }
        mysqli_stmt_close($stmt);
    } else {
        $sales_data = array();
    }
} else {
    // No parameters, use direct query
    $result = mysqli_query($conn, $query);
    $sales_data = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $sales_data[] = $row;
    }
}

if (empty($sales_data)) {
    echo '<div class="p-8 text-center">
            <div class="flex flex-col items-center space-y-4">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-2xl text-gray-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">No sales found</h3>
                    <p class="text-gray-500">Try adjusting your search criteria or date range.</p>
                </div>
            </div>
          </div>';
    exit;
}

foreach ($sales_data as $sale) {
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
    
    echo '<div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-4 p-4">
            <div class="flex justify-between items-start mb-3">
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-900 text-lg">' . htmlspecialchars($sale['invoice_number']) . '</h3>
                    <p class="text-sm text-gray-600">' . htmlspecialchars($sale['member_name'] ?: 'Walk-in Customer') . '</p>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $status_class . '">
                    <i class="' . $status_icon . ' mr-1"></i>
                    ' . ucfirst($sale['payment_status']) . '
                </span>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500 mb-1">Items</p>
                    <p class="font-semibold text-gray-900">' . $sale['item_count'] . '</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500 mb-1">Total</p>
                    <p class="font-semibold text-green-600">RM ' . number_format($sale['total_amount'], 2) . '</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500 mb-1">Payment</p>
                    <p class="font-semibold text-gray-900 capitalize">' . $sale['payment_method'] . '</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-500 mb-1">Date</p>
                    <p class="font-semibold text-gray-900">' . date('M j, Y', strtotime($sale['created_at'])) . '</p>
                </div>
            </div>
            
            <div class="flex space-x-2">
                <button onclick="viewSaleDetails(\'' . $sale['invoice_number'] . '\')" class="flex-1 bg-blue-600 text-white py-3 px-4 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors flex items-center justify-center">
                    <i class="fas fa-eye mr-2"></i>View Details
                </button>
                <button onclick="printReceipt(\'' . $sale['invoice_number'] . '\')" class="flex-1 bg-green-600 text-white py-3 px-4 rounded-lg text-sm font-medium hover:bg-green-700 transition-colors flex items-center justify-center">
                    <i class="fas fa-print mr-2"></i>Print
                </button>' . 
                ($sale['payment_status'] == 'paid' ? '
                <button onclick="refundSale(' . $sale['id'] . ')" class="flex-1 bg-red-600 text-white py-3 px-4 rounded-lg text-sm font-medium hover:bg-red-700 transition-colors flex items-center justify-center">
                    <i class="fas fa-undo mr-2"></i>Refund
                </button>' : '') . '
            </div>
          </div>';
}

// Add pagination controls
if ($total_pages > 1) {
    echo '<div class="mobile-pagination mt-6 flex justify-center items-center space-x-2">';
    
    // Previous button
    if ($page > 1) {
        echo '<button onclick="loadMobilePage(' . ($page - 1) . ')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                <i class="fas fa-chevron-left mr-1"></i>
                Previous
              </button>';
    }
    
    // Page numbers
    $start_page = max(1, $page - 2);
    $end_page = min($total_pages, $page + 2);
    
    if ($start_page > 1) {
        echo '<button onclick="loadMobilePage(1)" class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">1</button>';
        if ($start_page > 2) {
            echo '<span class="px-2 text-gray-500">...</span>';
        }
    }
    
    for ($i = $start_page; $i <= $end_page; $i++) {
        if ($i == $page) {
            echo '<button class="px-3 py-2 bg-blue-600 text-white rounded-lg">' . $i . '</button>';
        } else {
            echo '<button onclick="loadMobilePage(' . $i . ')" class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">' . $i . '</button>';
        }
    }
    
    if ($end_page < $total_pages) {
        if ($end_page < $total_pages - 1) {
            echo '<span class="px-2 text-gray-500">...</span>';
        }
        echo '<button onclick="loadMobilePage(' . $total_pages . ')" class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">' . $total_pages . '</button>';
    }
    
    // Next button
    if ($page < $total_pages) {
        echo '<button onclick="loadMobilePage(' . ($page + 1) . ')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                Next
                <i class="fas fa-chevron-right ml-1"></i>
              </button>';
    }
    
    echo '</div>';
    
    // Page info
    echo '<div class="text-center mt-4 text-sm text-gray-600">
            Showing ' . (($page - 1) * $per_page + 1) . ' to ' . min($page * $per_page, $total_records) . ' of ' . $total_records . ' sales
          </div>';
}
?>
