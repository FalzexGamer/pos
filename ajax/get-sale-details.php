<?php
// Prevent any output before JSON response
ob_start();
error_reporting(0);
ini_set('display_errors', 0);

include '../include/conn.php';
include '../include/session.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

$sale_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($sale_id <= 0) {
    header('Content-Type: application/json');
    ob_end_clean();
    echo json_encode(['error' => 'Invalid sale ID']);
    exit;
}

// Get sale details
$sale_query = "SELECT s.*, 
                      m.name as member_name,
                      m.email as member_email,
                      m.phone as member_phone,
                      u.full_name as user_name
               FROM sales s
               LEFT JOIN members m ON s.member_id = m.id
               LEFT JOIN users u ON s.user_id = u.id
               WHERE s.id = ?";

$stmt = mysqli_prepare($conn, $sale_query);
if (!$stmt) {
    header('Content-Type: application/json');
    ob_end_clean();
    echo json_encode(['error' => 'Database error: ' . mysqli_error($conn)]);
    exit;
}

mysqli_stmt_bind_param($stmt, 'i', $sale_id);
mysqli_stmt_execute($stmt);
$sale_result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($sale_result) === 0) {
    header('Content-Type: application/json');
    ob_end_clean();
    echo json_encode(['error' => 'Sale not found']);
    exit;
}

$sale = mysqli_fetch_assoc($sale_result);

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
$items_result = mysqli_stmt_get_result($stmt);

$items = [];
while ($row = mysqli_fetch_assoc($items_result)) {
    $items[] = $row;
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
?>


