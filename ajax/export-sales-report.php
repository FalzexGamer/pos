<?php
include '../include/conn.php';
include '../include/session.php';

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$report_type = isset($_GET['report_type']) ? $_GET['report_type'] : 'daily';

if (empty($start_date) || empty($end_date)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Start date and end date are required']);
    exit;
}

// Set headers for CSV download
$filename = 'sales_report_' . $start_date . '_to_' . $end_date . '.csv';
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// Create file pointer connected to the output stream
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for proper encoding
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Write CSV headers
fputcsv($output, [
    'Sales Report',
    'Period: ' . $start_date . ' to ' . $end_date,
    'Generated: ' . date('Y-m-d H:i:s')
]);
fputcsv($output, []); // Empty row

// Get summary metrics
$metrics_query = "SELECT 
                    COUNT(*) as total_sales,
                    SUM(total_amount) as total_revenue,
                    AVG(total_amount) as avg_sale,
                    COUNT(DISTINCT member_id) as unique_customers
                 FROM sales 
                 WHERE DATE(created_at) BETWEEN ? AND ?";

$stmt = mysqli_prepare($conn, $metrics_query);
mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
mysqli_stmt_execute($stmt);
$metrics_result = mysqli_stmt_get_result($stmt);
$metrics = mysqli_fetch_assoc($metrics_result);

// Write summary metrics
fputcsv($output, ['SUMMARY METRICS']);
fputcsv($output, ['Metric', 'Value']);
fputcsv($output, ['Total Sales', $metrics['total_sales']]);
fputcsv($output, ['Total Revenue (RM)', number_format($metrics['total_revenue'], 2)]);
fputcsv($output, ['Average Sale (RM)', number_format($metrics['avg_sale'], 2)]);
fputcsv($output, ['Unique Customers', $metrics['unique_customers']]);
fputcsv($output, []); // Empty row

// Get detailed breakdown based on report type
$group_by = '';
$date_format = '';
switch ($report_type) {
    case 'daily':
        $group_by = 'DATE(created_at)';
        $date_format = '%Y-%m-%d';
        break;
    case 'weekly':
        $group_by = 'YEARWEEK(created_at)';
        $date_format = '%Y-%u';
        break;
    case 'monthly':
        $group_by = 'DATE_FORMAT(created_at, "%Y-%m")';
        $date_format = '%Y-%m';
        break;
    case 'yearly':
        $group_by = 'YEAR(created_at)';
        $date_format = '%Y';
        break;
}

$breakdown_query = "SELECT 
                        DATE_FORMAT(created_at, ?) as period,
                        COUNT(*) as sales_count,
                        SUM(total_amount) as revenue,
                        AVG(total_amount) as avg_sale
                    FROM sales 
                    WHERE DATE(created_at) BETWEEN ? AND ?
                    GROUP BY $group_by
                    ORDER BY period";

$stmt = mysqli_prepare($conn, $breakdown_query);
mysqli_stmt_bind_param($stmt, 'sss', $date_format, $start_date, $end_date);
mysqli_stmt_execute($stmt);
$breakdown_result = mysqli_stmt_get_result($stmt);

// Write detailed breakdown
fputcsv($output, ['DETAILED BREAKDOWN']);
fputcsv($output, ['Period', 'Sales Count', 'Revenue (RM)', 'Average Sale (RM)']);

while ($row = mysqli_fetch_assoc($breakdown_result)) {
    fputcsv($output, [
        $row['period'],
        $row['sales_count'],
        number_format($row['revenue'], 2),
        number_format($row['avg_sale'], 2)
    ]);
}
fputcsv($output, []); // Empty row

// Get top products
$top_products_query = "SELECT 
                        p.name as product_name,
                        c.name as category_name,
                        SUM(si.quantity) as total_quantity,
                        SUM(si.total_price) as total_revenue
                     FROM sale_items si
                     JOIN products p ON si.product_id = p.id
                     LEFT JOIN categories c ON p.category_id = c.id
                     JOIN sales s ON si.sale_id = s.id
                     WHERE DATE(s.created_at) BETWEEN ? AND ?
                     GROUP BY p.id
                     ORDER BY total_quantity DESC
                     LIMIT 10";

$stmt = mysqli_prepare($conn, $top_products_query);
mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
mysqli_stmt_execute($stmt);
$top_products_result = mysqli_stmt_get_result($stmt);

// Write top products
fputcsv($output, ['TOP PRODUCTS']);
fputcsv($output, ['Product Name', 'Category', 'Quantity Sold', 'Revenue (RM)']);

while ($row = mysqli_fetch_assoc($top_products_result)) {
    fputcsv($output, [
        $row['product_name'],
        $row['category_name'] ?: 'Uncategorized',
        $row['total_quantity'],
        number_format($row['total_revenue'], 2)
    ]);
}
fputcsv($output, []); // Empty row

// Get top categories
$top_categories_query = "SELECT 
                            c.name as category_name,
                            COUNT(DISTINCT p.id) as product_count,
                            SUM(si.quantity) as total_sales,
                            SUM(si.total_price) as total_revenue
                         FROM sale_items si
                         JOIN products p ON si.product_id = p.id
                         LEFT JOIN categories c ON p.category_id = c.id
                         JOIN sales s ON si.sale_id = s.id
                         WHERE DATE(s.created_at) BETWEEN ? AND ?
                         GROUP BY c.id
                         ORDER BY total_sales DESC
                         LIMIT 10";

$stmt = mysqli_prepare($conn, $top_categories_query);
mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
mysqli_stmt_execute($stmt);
$top_categories_result = mysqli_stmt_get_result($stmt);

// Write top categories
fputcsv($output, ['TOP CATEGORIES']);
fputcsv($output, ['Category Name', 'Product Count', 'Units Sold', 'Revenue (RM)']);

while ($row = mysqli_fetch_assoc($top_categories_result)) {
    fputcsv($output, [
        $row['category_name'] ?: 'Uncategorized',
        $row['product_count'],
        $row['total_sales'],
        number_format($row['total_revenue'], 2)
    ]);
}
fputcsv($output, []); // Empty row

// Get payment method breakdown
$payment_query = "SELECT 
                    payment_method,
                    COUNT(*) as count,
                    SUM(total_amount) as total
                 FROM sales 
                 WHERE DATE(created_at) BETWEEN ? AND ?
                 GROUP BY payment_method
                 ORDER BY total DESC";

$stmt = mysqli_prepare($conn, $payment_query);
mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
mysqli_stmt_execute($stmt);
$payment_result = mysqli_stmt_get_result($stmt);

// Write payment method breakdown
fputcsv($output, ['PAYMENT METHOD BREAKDOWN']);
fputcsv($output, ['Payment Method', 'Transaction Count', 'Total Amount (RM)']);

while ($row = mysqli_fetch_assoc($payment_result)) {
    fputcsv($output, [
        ucfirst($row['payment_method']),
        $row['count'],
        number_format($row['total'], 2)
    ]);
}
fputcsv($output, []); // Empty row

// Get daily sales for detailed view
$daily_sales_query = "SELECT 
                        DATE(created_at) as sale_date,
                        COUNT(*) as sales_count,
                        SUM(total_amount) as daily_revenue,
                        AVG(total_amount) as avg_sale,
                        COUNT(DISTINCT member_id) as unique_customers
                     FROM sales 
                     WHERE DATE(created_at) BETWEEN ? AND ?
                     GROUP BY DATE(created_at)
                     ORDER BY sale_date";

$stmt = mysqli_prepare($conn, $daily_sales_query);
mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
mysqli_stmt_execute($stmt);
$daily_sales_result = mysqli_stmt_get_result($stmt);

// Write daily sales
fputcsv($output, ['DAILY SALES DETAILS']);
fputcsv($output, ['Date', 'Sales Count', 'Revenue (RM)', 'Average Sale (RM)', 'Unique Customers']);

while ($row = mysqli_fetch_assoc($daily_sales_result)) {
    fputcsv($output, [
        $row['sale_date'],
        $row['sales_count'],
        number_format($row['daily_revenue'], 2),
        number_format($row['avg_sale'], 2),
        $row['unique_customers']
    ]);
}

// Close the file pointer
fclose($output);
?>
