<?php
// Start output buffering to prevent any output before JSON response
ob_start();
error_reporting(0);
ini_set('display_errors', 0);

include '../include/conn.php';
include '../include/session.php';

// Check database connection
if (!$conn) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit;
}

// Check if user is logged in
if (empty($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'User not authenticated']);
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

mysqli_stmt_bind_result($stmt, $total_sales, $total_revenue, $avg_sale);
if (!mysqli_stmt_fetch($stmt)) {
    $total_sales = 0;
    $total_revenue = 0;
    $avg_sale = 0;
}
mysqli_stmt_close($stmt);

$metrics = [
    'total_sales' => $total_sales,
    'total_revenue' => $total_revenue,
    'avg_sale' => $avg_sale
];

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

$prev_stmt = mysqli_prepare($conn, $prev_metrics_query);
if (!$prev_stmt) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Previous metrics query preparation failed: ' . mysqli_error($conn)]);
    exit;
}
mysqli_stmt_bind_param($prev_stmt, 'ss', $prev_start, $prev_end);
if (!mysqli_stmt_execute($prev_stmt)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Previous metrics query execution failed: ' . mysqli_stmt_error($prev_stmt)]);
    exit;
}
mysqli_stmt_bind_result($prev_stmt, $prev_total_sales, $prev_total_revenue, $prev_avg_sale);
if (!mysqli_stmt_fetch($prev_stmt)) {
    $prev_total_sales = 0;
    $prev_total_revenue = 0;
    $prev_avg_sale = 0;
}
mysqli_stmt_close($prev_stmt);

$prev_metrics = [
    'total_sales' => $prev_total_sales,
    'total_revenue' => $prev_total_revenue,
    'avg_sale' => $prev_avg_sale
];

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

$top_product_stmt = mysqli_prepare($conn, $top_product_query);
if (!$top_product_stmt) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Top product query preparation failed: ' . mysqli_error($conn)]);
    exit;
}
mysqli_stmt_bind_param($top_product_stmt, 'ss', $start_date, $end_date);
if (!mysqli_stmt_execute($top_product_stmt)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Top product query execution failed: ' . mysqli_stmt_error($top_product_stmt)]);
    exit;
}
mysqli_stmt_bind_result($top_product_stmt, $product_name, $category_name, $total_quantity, $total_revenue);
$top_product = null;
if (mysqli_stmt_fetch($top_product_stmt)) {
    $top_product = [
        'product_name' => $product_name,
        'category_name' => $category_name,
        'total_quantity' => $total_quantity,
        'total_revenue' => $total_revenue
    ];
}
mysqli_stmt_close($top_product_stmt);

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

$chart_stmt = mysqli_prepare($conn, $chart_query);
if (!$chart_stmt) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Chart query preparation failed: ' . mysqli_error($conn)]);
    exit;
}
mysqli_stmt_bind_param($chart_stmt, 'sss', $date_format, $start_date, $end_date);
if (!mysqli_stmt_execute($chart_stmt)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Chart query execution failed: ' . mysqli_stmt_error($chart_stmt)]);
    exit;
}
mysqli_stmt_bind_result($chart_stmt, $period, $sales_count, $revenue);

while (mysqli_stmt_fetch($chart_stmt)) {
    $response['charts']['revenue']['labels'][] = $period;
    $response['charts']['revenue']['data'][] = (float)$revenue;
    $response['charts']['sales']['labels'][] = $period;
    $response['charts']['sales']['data'][] = (int)$sales_count;
    
    // Add to breakdown
    $response['breakdown'][] = [
        'period' => $period,
        'salesCount' => (int)$sales_count,
        'revenue' => (float)$revenue,
        'avgSale' => $sales_count > 0 ? (float)$revenue / (int)$sales_count : 0,
        'growth' => 0 // You can calculate this if needed
    ];
}
mysqli_stmt_close($chart_stmt);

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

$top_products_stmt = mysqli_prepare($conn, $top_products_query);
if (!$top_products_stmt) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Top products query preparation failed: ' . mysqli_error($conn)]);
    exit;
}
mysqli_stmt_bind_param($top_products_stmt, 'ss', $start_date, $end_date);
if (!mysqli_stmt_execute($top_products_stmt)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Top products query execution failed: ' . mysqli_stmt_error($top_products_stmt)]);
    exit;
}
mysqli_stmt_bind_result($top_products_stmt, $product_name, $category_name, $total_quantity, $total_revenue);

while (mysqli_stmt_fetch($top_products_stmt)) {
    $response['topProducts'][] = [
        'name' => $product_name,
        'category' => $category_name ?: 'Uncategorized',
        'quantity' => (int)$total_quantity,
        'revenue' => (float)$total_revenue
    ];
}
mysqli_stmt_close($top_products_stmt);

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

$top_categories_stmt = mysqli_prepare($conn, $top_categories_query);
if (!$top_categories_stmt) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Top categories query preparation failed: ' . mysqli_error($conn)]);
    exit;
}
mysqli_stmt_bind_param($top_categories_stmt, 'ss', $start_date, $end_date);
if (!mysqli_stmt_execute($top_categories_stmt)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Top categories query execution failed: ' . mysqli_stmt_error($top_categories_stmt)]);
    exit;
}
mysqli_stmt_bind_result($top_categories_stmt, $category_name, $product_count, $total_sales, $total_revenue);

while (mysqli_stmt_fetch($top_categories_stmt)) {
    $response['topCategories'][] = [
        'name' => $category_name ?: 'Uncategorized',
        'productCount' => (int)$product_count,
        'sales' => (int)$total_sales,
        'revenue' => (float)$total_revenue
    ];
}
mysqli_stmt_close($top_categories_stmt);

// Return JSON response
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

// Clean any previous output and send JSON response
ob_end_clean();

// Check for any errors
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['error' => 'JSON encoding error: ' . json_last_error_msg()]);
} else {
    echo json_encode($response);
}
exit;
?>


