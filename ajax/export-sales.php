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

// Set headers for CSV download
$filename = 'sales_export_' . date('Y-m-d_H-i-s') . '.csv';
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// Create output stream
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for proper encoding
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// CSV headers
$headers = [
    'Invoice Number',
    'Customer Name',
    'Customer Email',
    'Cashier',
    'Items Count',
    'Subtotal',
    'Discount',
    'Tax',
    'Total Amount',
    'Payment Method',
    'Payment Status',
    'Date',
    'Time',
    'Notes'
];

fputcsv($output, $headers);

// Add data rows
while ($sale = mysqli_fetch_assoc($result)) {
    $row = [
        $sale['invoice_number'],
        $sale['member_name'] ?: 'Walk-in Customer',
        $sale['member_email'] ?: '',
        $sale['cashier_name'] ?: 'Unknown',
        $sale['item_count'],
        number_format($sale['subtotal'], 2),
        number_format($sale['discount_amount'], 2),
        number_format($sale['tax_amount'], 2),
        number_format($sale['total_amount'], 2),
        ucfirst($sale['payment_method']),
        ucfirst($sale['payment_status']),
        date('Y-m-d', strtotime($sale['created_at'])),
        date('H:i:s', strtotime($sale['created_at'])),
        $sale['notes'] ?: ''
    ];
    
    fputcsv($output, $row);
}

fclose($output);
exit;
?>
