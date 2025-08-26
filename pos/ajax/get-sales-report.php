<?php
// Prevent any output before JSON response
ob_clean();
error_reporting(0);
ini_set('display_errors', 0);

include '../include/conn.php';
include '../include/session.php';

// Check database connection
if (!$conn) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$report_type = isset($_GET['report_type']) ? $_GET['report_type'] : 'daily';

if (empty($start_date) || empty($end_date)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Start date and end date are required']);
    exit;
}

$response = [
    'metrics' => [],
    'charts' => [
        'revenue' => ['labels' => [], 'data' => []],
        'sales' => ['labels' => [], 'data' => []]
    ],
    'topProducts' => [],
    'topCategories' => [],
    'breakdown' => []
];

// Get metrics
$metrics_query = "SELECT 
                    COUNT(*) as total_sales,
                    SUM(total_amount) as total_revenue,
                    AVG(total_amount) as avg_sale
                 FROM sales 
                 WHERE DATE(created_at) BETWEEN ? AND ?";

$stmt = mysqli_prepare($conn, $metrics_query);
if (!$stmt) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database query preparation failed: ' . mysqli_error($conn)]);
    exit;
}

mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
if (!mysqli_stmt_execute($stmt)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database query execution failed: ' . mysqli_stmt_error($stmt)]);
    exit;
}

$metrics_result = mysqli_stmt_get_result($stmt);
if (!$metrics_result) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Failed to get query result: ' . mysqli_error($conn)]);
    exit;
}

$metrics = mysqli_fetch_assoc($metrics_result);

// Get previous period for comparison
$date_diff = strtotime($end_date) - strtotime($start_date);
$prev_start = date('Y-m-d', strtotime($start_date) - $date_diff);
$prev_end = date('Y-m-d', strtotime($start_date) - 1);

$prev_metrics_query = "SELECT 
                        COUNT(*) as total_sales,
                        SUM(total_amount) as total_revenue,
                        AVG(total_amount) as avg_sale
                     FROM sales 
                     WHERE DATE(created_at) BETWEEN ? AND ?";

$stmt = mysqli_prepare($conn, $prev_metrics_query);
mysqli_stmt_bind_param($stmt, 'ss', $prev_start, $prev_end);
mysqli_stmt_execute($stmt);
$prev_metrics_result = mysqli_stmt_get_result($stmt);
$prev_metrics = mysqli_fetch_assoc($prev_metrics_result);

// Calculate growth percentages
$revenue_change = $prev_metrics['total_revenue'] > 0 ? 
    round((($metrics['total_revenue'] - $prev_metrics['total_revenue']) / $prev_metrics['total_revenue']) * 100, 1) : 0;

$sales_change = $prev_metrics['total_sales'] > 0 ? 
    round((($metrics['total_sales'] - $prev_metrics['total_sales']) / $prev_metrics['total_sales']) * 100, 1) : 0;

$avg_change = $prev_metrics['avg_sale'] > 0 ? 
    round((($metrics['avg_sale'] - $prev_metrics['avg_sale']) / $prev_metrics['avg_sale']) * 100, 1) : 0;

// Get top product
$top_product_query = "SELECT 
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
                     LIMIT 1";

$stmt = mysqli_prepare($conn, $top_product_query);
mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
mysqli_stmt_execute($stmt);
$top_product_result = mysqli_stmt_get_result($stmt);
$top_product = mysqli_fetch_assoc($top_product_result);

$response['metrics'] = [
    'totalRevenue' => $metrics['total_revenue'] ?: 0,
    'revenueChange' => $revenue_change,
    'totalSales' => $metrics['total_sales'] ?: 0,
    'salesChange' => $sales_change,
    'avgSale' => $metrics['avg_sale'] ?: 0,
    'avgChange' => $avg_change,
    'topProduct' => $top_product ? $top_product['product_name'] : 'No sales',
    'topProductSales' => $top_product ? $top_product['total_quantity'] : 0
];

// Get chart data based on report type
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

$chart_query = "SELECT 
                    DATE_FORMAT(created_at, ?) as period,
                    COUNT(*) as sales_count,
                    SUM(total_amount) as revenue
                FROM sales 
                WHERE DATE(created_at) BETWEEN ? AND ?
                GROUP BY $group_by
                ORDER BY period";

$stmt = mysqli_prepare($conn, $chart_query);
mysqli_stmt_bind_param($stmt, 'sss', $date_format, $start_date, $end_date);
mysqli_stmt_execute($stmt);
$chart_result = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($chart_result)) {
    $response['charts']['revenue']['labels'][] = $row['period'];
    $response['charts']['revenue']['data'][] = (float)$row['revenue'];
    $response['charts']['sales']['labels'][] = $row['period'];
    $response['charts']['sales']['data'][] = (int)$row['sales_count'];
    
    // Add to breakdown
    $response['breakdown'][] = [
        'period' => $row['period'],
        'salesCount' => (int)$row['sales_count'],
        'revenue' => (float)$row['revenue'],
        'avgSale' => $row['sales_count'] > 0 ? (float)$row['revenue'] / (int)$row['sales_count'] : 0,
        'growth' => 0 // You can calculate this if needed
    ];
}

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
                     LIMIT 5";

$stmt = mysqli_prepare($conn, $top_products_query);
mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
mysqli_stmt_execute($stmt);
$top_products_result = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($top_products_result)) {
    $response['topProducts'][] = [
        'name' => $row['product_name'],
        'category' => $row['category_name'] ?: 'Uncategorized',
        'quantity' => (int)$row['total_quantity'],
        'revenue' => (float)$row['total_revenue']
    ];
}

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
                         LIMIT 5";

$stmt = mysqli_prepare($conn, $top_categories_query);
mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
mysqli_stmt_execute($stmt);
$top_categories_result = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($top_categories_result)) {
    $response['topCategories'][] = [
        'name' => $row['category_name'] ?: 'Uncategorized',
        'productCount' => (int)$row['product_count'],
        'sales' => (int)$row['total_sales'],
        'revenue' => (float)$row['total_revenue']
    ];
}

// Return JSON response
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

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


