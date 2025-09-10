<?php
// Prevent any output before JSON response
ob_start();
// Disable error reporting to prevent output issues
error_reporting(0);
ini_set('display_errors', 0);

include '../include/conn.php';
include '../include/session.php';

// Helper functions
function getStatusClass($status) {
    switch ($status) {
        case 'paid':
            return 'bg-green-100 text-green-800';
        case 'pending':
            return 'bg-yellow-100 text-yellow-800';
        case 'refunded':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

$invoice_number = isset($_GET['invoice']) ? mysqli_real_escape_string($conn, $_GET['invoice']) : '';
$sale_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Debug: Log the received parameters (commented out to prevent output issues)
// error_log("Received invoice number: " . $invoice_number . ", sale ID: " . $sale_id);

if (empty($invoice_number) && $sale_id <= 0) {
    header('Content-Type: application/json');
    ob_end_clean();
    echo json_encode(['error' => 'Invoice number or sale ID required']);
    exit;
}

// Get sale details
if (!empty($invoice_number)) {
    $sale_query = "SELECT s.*, 
                          m.name as member_name,
                          m.email as member_email,
                          m.phone as member_phone,
                          u.full_name as user_name
                   FROM sales s
                   LEFT JOIN members m ON s.member_id = m.id
                   LEFT JOIN users u ON s.user_id = u.id
                   WHERE s.invoice_number = ?";
    $param_type = 's';
    $param_value = $invoice_number;
} else {
    $sale_query = "SELECT s.*, 
                          m.name as member_name,
                          m.email as member_email,
                          m.phone as member_phone,
                          u.full_name as user_name
                   FROM sales s
                   LEFT JOIN members m ON s.member_id = m.id
                   LEFT JOIN users u ON s.user_id = u.id
                   WHERE s.id = ?";
    $param_type = 'i';
    $param_value = $sale_id;
}

$stmt = mysqli_prepare($conn, $sale_query);
if (!$stmt) {
    header('Content-Type: application/json');
    ob_end_clean();
    echo json_encode(['error' => 'Database error: ' . mysqli_error($conn)]);
    exit;
}

mysqli_stmt_bind_param($stmt, $param_type, $param_value);
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
    
    // Fetch the sale data
    if (mysqli_stmt_fetch($stmt)) {
        $sale = array();
        foreach ($field_names as $field) {
            $sale[$field] = $$field;
        }
    } else {
        $sale = null;
    }
    mysqli_stmt_close($stmt);
} else {
    $sale = null;
}

// Debug: Log query results (commented out to prevent output issues)
// error_log("Query executed. Sale found: " . ($sale ? 'Yes' : 'No'));

if (!$sale) {
    header('Content-Type: application/json');
    ob_end_clean();
    $error_msg = !empty($invoice_number) ? 
        'Sale not found for invoice: ' . $invoice_number . '. Please check if the invoice number exists in the database.' :
        'Sale not found for ID: ' . $sale_id . '. Please check if the sale ID exists in the database.';
    echo json_encode(['error' => $error_msg]);
    exit;
}
$sale_id = $sale['id']; // Get the sale ID from the found sale record

// Get sale items
$items_query = "SELECT si.*, 
                       p.name as product_name,
                       p.sku as product_sku,
                       c.name as category_name
                FROM sale_items si
                JOIN products p ON si.product_id = p.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE si.sale_id = ?";

$stmt = mysqli_prepare($conn, $items_query);
if (!$stmt) {
    header('Content-Type: application/json');
    ob_end_clean();
    echo json_encode(['error' => 'Database error: ' . mysqli_error($conn)]);
    exit;
}

mysqli_stmt_bind_param($stmt, 'i', $sale_id);
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
    $items = array();
    while (mysqli_stmt_fetch($stmt)) {
        $row = array();
        foreach ($field_names as $field) {
            $row[$field] = $$field;
        }
        $items[] = $row;
    }
    mysqli_stmt_close($stmt);
} else {
    $items = array();
}

// Generate HTML for sale details
$html = '<div class="space-y-6">';

// Sale Information
$html .= '<div class="bg-gray-50/50 rounded-xl p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Sale Information</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-500">Invoice Number</p>
                    <p class="text-lg font-semibold text-gray-900">' . htmlspecialchars($sale['invoice_number']) . '</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Date & Time</p>
                    <p class="text-lg font-semibold text-gray-900">' . date('M d, Y H:i', strtotime($sale['created_at'])) . '</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Payment Method</p>
                    <span class="text-lg font-semibold text-gray-900 capitalize">' . $sale['payment_method'] . '</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Status</p>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ' . getStatusClass($sale['payment_status']) . '">
                        ' . ucfirst($sale['payment_status']) . '
                    </span>
                </div>
            </div>
          </div>';

// Customer Information
if ($sale['member_name']) {
    $html .= '<div class="bg-gray-50/50 rounded-xl p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Customer Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Name</p>
                        <p class="text-lg font-semibold text-gray-900">' . htmlspecialchars($sale['member_name']) . '</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Email</p>
                        <p class="text-lg font-semibold text-gray-900">' . htmlspecialchars($sale['member_email']) . '</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Phone</p>
                        <p class="text-lg font-semibold text-gray-900">' . htmlspecialchars($sale['member_phone']) . '</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Member ID</p>
                        <p class="text-lg font-semibold text-gray-900">' . $sale['member_id'] . '</p>
                    </div>
                </div>
              </div>';
} else {
    $html .= '<div class="bg-gray-50/50 rounded-xl p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Customer Information</h4>
                <p class="text-gray-500">Walk-in Customer</p>
              </div>';
}

// Items List
$html .= '<div class="bg-gray-50/50 rounded-xl p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Items (' . count($items) . ')</h4>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-white/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white/30 divide-y divide-gray-200/50">';

foreach ($items as $item) {
    $html .= '<tr>
                <td class="px-4 py-3 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">' . htmlspecialchars($item['product_name']) . '</div>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">' . htmlspecialchars($item['product_sku']) . '</td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">' . htmlspecialchars($item['category_name'] ?: 'Uncategorized') . '</td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-right">' . $item['quantity'] . '</td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-right">RM ' . number_format($item['unit_price'], 2) . '</td>
                <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">RM ' . number_format($item['total_price'], 2) . '</td>
              </tr>';
}

$html .= '</tbody>
        </table>
      </div>
    </div>';

// Totals
$html .= '<div class="bg-gray-50/50 rounded-xl p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Totals</h4>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Subtotal</span>
                    <span class="text-sm font-semibold text-gray-900">RM ' . number_format($sale['subtotal'], 2) . '</span>
                </div>';

if ($sale['discount_amount'] > 0) {
    $html .= '<div class="flex justify-between">
                <span class="text-sm text-gray-500">Discount</span>
                <span class="text-sm font-semibold text-red-600">-RM ' . number_format($sale['discount_amount'], 2) . '</span>
              </div>';
}

if ($sale['tax_amount'] > 0) {
    $html .= '<div class="flex justify-between">
                <span class="text-sm text-gray-500">Tax</span>
                <span class="text-sm font-semibold text-gray-900">RM ' . number_format($sale['tax_amount'], 2) . '</span>
              </div>';
}

$html .= '<div class="flex justify-between border-t border-gray-200 pt-3">
            <span class="text-lg font-semibold text-gray-900">Total</span>
            <span class="text-lg font-bold text-gray-900">RM ' . number_format($sale['total_amount'], 2) . '</span>
          </div>
        </div>
      </div>';

// Add item count to sale data
$sale['item_count'] = count($items);

// Return JSON response
$response = [
    'sale' => $sale,
    'html' => $html
];

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00');

// Ensure clean output
ob_end_clean();

// Check for any errors
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['error' => 'JSON encoding error: ' . json_last_error_msg()]);
} else {
    echo json_encode($response);
}
exit;
?>


