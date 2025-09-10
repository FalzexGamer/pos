<?php
include '../include/conn.php';
include '../include/session.php';

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$report_type = isset($_GET['report_type']) ? $_GET['report_type'] : 'daily';

if (empty($start_date) || empty($end_date)) {
    echo '<script>alert("Start date and end date are required"); window.close();</script>';
    exit;
}

// Get summary metrics
$metrics_query = "SELECT 
                    COUNT(*) as total_sales,
                    SUM(total_amount) as total_revenue,
                    AVG(total_amount) as avg_sale,
                    COUNT(DISTINCT member_id) as unique_customers
                 FROM sales 
                 WHERE DATE(created_at) BETWEEN ? AND ?";

$stmt = mysqli_prepare($conn, $metrics_query);
if (!$stmt) {
    die('Metrics query preparation failed: ' . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
if (!mysqli_stmt_execute($stmt)) {
    die('Metrics query execution failed: ' . mysqli_stmt_error($stmt));
}
mysqli_stmt_bind_result($stmt, $total_sales, $total_revenue, $avg_sale, $unique_customers);
if (!mysqli_stmt_fetch($stmt)) {
    $total_sales = 0;
    $total_revenue = 0;
    $avg_sale = 0;
    $unique_customers = 0;
}
mysqli_stmt_close($stmt);

$metrics = [
    'total_sales' => $total_sales,
    'total_revenue' => $total_revenue,
    'avg_sale' => $avg_sale,
    'unique_customers' => $unique_customers
];

// Get detailed breakdown based on report type
$breakdown_query = '';
switch ($report_type) {
    case 'daily':
        $breakdown_query = "SELECT 
                            DATE_FORMAT(created_at, '%Y-%m-%d') as period,
                            COUNT(*) as sales_count,
                            SUM(total_amount) as revenue,
                            AVG(total_amount) as avg_sale
                        FROM sales 
                        WHERE DATE(created_at) BETWEEN ? AND ?
                        GROUP BY DATE(created_at)
                        ORDER BY period";
        break;
    case 'weekly':
        $breakdown_query = "SELECT 
                            DATE_FORMAT(created_at, '%Y-%u') as period,
                            COUNT(*) as sales_count,
                            SUM(total_amount) as revenue,
                            AVG(total_amount) as avg_sale
                        FROM sales 
                        WHERE DATE(created_at) BETWEEN ? AND ?
                        GROUP BY YEARWEEK(created_at)
                        ORDER BY period";
        break;
    case 'monthly':
        $breakdown_query = "SELECT 
                            DATE_FORMAT(created_at, '%Y-%m') as period,
                            COUNT(*) as sales_count,
                            SUM(total_amount) as revenue,
                            AVG(total_amount) as avg_sale
                        FROM sales 
                        WHERE DATE(created_at) BETWEEN ? AND ?
                        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                        ORDER BY period";
        break;
    case 'yearly':
        $breakdown_query = "SELECT 
                            DATE_FORMAT(created_at, '%Y') as period,
                            COUNT(*) as sales_count,
                            SUM(total_amount) as revenue,
                            AVG(total_amount) as avg_sale
                        FROM sales 
                        WHERE DATE(created_at) BETWEEN ? AND ?
                        GROUP BY YEAR(created_at)
                        ORDER BY period";
        break;
    default:
        $breakdown_query = "SELECT 
                            DATE_FORMAT(created_at, '%Y-%m-%d') as period,
                            COUNT(*) as sales_count,
                            SUM(total_amount) as revenue,
                            AVG(total_amount) as avg_sale
                        FROM sales 
                        WHERE DATE(created_at) BETWEEN ? AND ?
                        GROUP BY DATE(created_at)
                        ORDER BY period";
        break;
}

$breakdown_stmt = mysqli_prepare($conn, $breakdown_query);
if (!$breakdown_stmt) {
    die('Breakdown query preparation failed: ' . mysqli_error($conn));
}
mysqli_stmt_bind_param($breakdown_stmt, 'ss', $start_date, $end_date);
if (!mysqli_stmt_execute($breakdown_stmt)) {
    die('Breakdown query execution failed: ' . mysqli_stmt_error($breakdown_stmt));
}
// Store breakdown data
$breakdown_data = [];
mysqli_stmt_bind_result($breakdown_stmt, $period, $sales_count, $revenue, $avg_sale);
while (mysqli_stmt_fetch($breakdown_stmt)) {
    $breakdown_data[] = [
        'period' => $period,
        'sales_count' => $sales_count,
        'revenue' => $revenue,
        'avg_sale' => $avg_sale
    ];
}
mysqli_stmt_close($breakdown_stmt);

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
    die('Top products query preparation failed: ' . mysqli_error($conn));
}
mysqli_stmt_bind_param($top_products_stmt, 'ss', $start_date, $end_date);
if (!mysqli_stmt_execute($top_products_stmt)) {
    die('Top products query execution failed: ' . mysqli_stmt_error($top_products_stmt));
}

// Store top products data
$top_products_data = [];
mysqli_stmt_bind_result($top_products_stmt, $product_name, $category_name, $total_quantity, $total_revenue);
while (mysqli_stmt_fetch($top_products_stmt)) {
    $top_products_data[] = [
        'product_name' => $product_name,
        'category_name' => $category_name,
        'total_quantity' => $total_quantity,
        'total_revenue' => $total_revenue
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
    die('Top categories query preparation failed: ' . mysqli_error($conn));
}
mysqli_stmt_bind_param($top_categories_stmt, 'ss', $start_date, $end_date);
if (!mysqli_stmt_execute($top_categories_stmt)) {
    die('Top categories query execution failed: ' . mysqli_stmt_error($top_categories_stmt));
}

// Store top categories data
$top_categories_data = [];
mysqli_stmt_bind_result($top_categories_stmt, $category_name, $product_count, $total_sales, $total_revenue);
while (mysqli_stmt_fetch($top_categories_stmt)) {
    $top_categories_data[] = [
        'category_name' => $category_name,
        'product_count' => $product_count,
        'total_sales' => $total_sales,
        'total_revenue' => $total_revenue
    ];
}
mysqli_stmt_close($top_categories_stmt);

// Get payment method breakdown
$payment_query = "SELECT 
                    payment_method,
                    COUNT(*) as count,
                    SUM(total_amount) as total
                 FROM sales 
                 WHERE DATE(created_at) BETWEEN ? AND ?
                 GROUP BY payment_method
                 ORDER BY total DESC";

$payment_stmt = mysqli_prepare($conn, $payment_query);
if (!$payment_stmt) {
    die('Payment query preparation failed: ' . mysqli_error($conn));
}
mysqli_stmt_bind_param($payment_stmt, 'ss', $start_date, $end_date);
if (!mysqli_stmt_execute($payment_stmt)) {
    die('Payment query execution failed: ' . mysqli_stmt_error($payment_stmt));
}

// Store payment data
$payment_data = [];
mysqli_stmt_bind_result($payment_stmt, $payment_method, $count, $total);
while (mysqli_stmt_fetch($payment_stmt)) {
    $payment_data[] = [
        'payment_method' => $payment_method,
        'count' => $count,
        'total' => $total
    ];
}
mysqli_stmt_close($payment_stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report - <?php echo $start_date; ?> to <?php echo $end_date; ?></title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            background: white;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #1e40af;
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        
        .header p {
            margin: 5px 0;
            color: #6b7280;
        }
        
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .metric-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }
        
        .metric-card h3 {
            margin: 0 0 10px 0;
            color: #374151;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .metric-card .value {
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
        }
        
        .section {
            margin-bottom: 30px;
        }
        
        .section h2 {
            color: #1e40af;
            border-bottom: 2px solid #dbeafe;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        th {
            background-color: #f3f4f6;
            font-weight: 600;
            color: #374151;
        }
        
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #2563eb;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .print-button:hover {
            background: #1d4ed8;
        }
        
        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        @media (max-width: 768px) {
            .two-column {
                grid-template-columns: 1fr;
            }
            
            .metrics-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">Print Report</button>
    
    <div class="header">
        <h1>Sales Report</h1>
        <p><strong>Period:</strong> <?php echo date('F j, Y', strtotime($start_date)); ?> to <?php echo date('F j, Y', strtotime($end_date)); ?></p>
        <p><strong>Report Type:</strong> <?php echo ucfirst($report_type); ?> | <strong>Generated:</strong> <?php echo date('F j, Y \a\t g:i A'); ?></p>
    </div>
    
    <!-- Summary Metrics -->
    <div class="section">
        <h2>Summary Metrics</h2>
        <div class="metrics-grid">
            <div class="metric-card">
                <h3>Total Sales</h3>
                <div class="value"><?php echo number_format($metrics['total_sales']); ?></div>
            </div>
            <div class="metric-card">
                <h3>Total Revenue</h3>
                <div class="value">RM <?php echo number_format($metrics['total_revenue'], 2); ?></div>
            </div>
            <div class="metric-card">
                <h3>Average Sale</h3>
                <div class="value">RM <?php echo number_format($metrics['avg_sale'], 2); ?></div>
            </div>
            <div class="metric-card">
                <h3>Unique Customers</h3>
                <div class="value"><?php echo number_format($metrics['unique_customers']); ?></div>
            </div>
        </div>
    </div>
    
    <!-- Detailed Breakdown -->
    <div class="section">
        <h2>Detailed Breakdown</h2>
        <table>
            <thead>
                <tr>
                    <th>Period</th>
                    <th class="text-center">Sales Count</th>
                    <th class="text-right">Revenue (RM)</th>
                    <th class="text-right">Average Sale (RM)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($breakdown_data as $row): ?>
                <tr>
                    <td><?php echo $row['period']; ?></td>
                    <td class="text-center"><?php echo number_format($row['sales_count']); ?></td>
                    <td class="text-right"><?php echo number_format($row['revenue'], 2); ?></td>
                    <td class="text-right"><?php echo number_format($row['avg_sale'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Top Products and Categories -->
    <div class="section">
        <h2>Performance Analysis</h2>
        <div class="two-column">
            <!-- Top Products -->
            <div>
                <h3>Top Products</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th class="text-center">Qty</th>
                            <th class="text-right">Revenue (RM)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($top_products_data as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['category_name'] ?: 'Uncategorized'); ?></td>
                            <td class="text-center"><?php echo number_format($row['total_quantity']); ?></td>
                            <td class="text-right"><?php echo number_format($row['total_revenue'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Top Categories -->
            <div>
                <h3>Top Categories</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th class="text-center">Products</th>
                            <th class="text-center">Units</th>
                            <th class="text-right">Revenue (RM)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($top_categories_data as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['category_name'] ?: 'Uncategorized'); ?></td>
                            <td class="text-center"><?php echo number_format($row['product_count']); ?></td>
                            <td class="text-center"><?php echo number_format($row['total_sales']); ?></td>
                            <td class="text-right"><?php echo number_format($row['total_revenue'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Payment Method Breakdown -->
    <div class="section">
        <h2>Payment Method Analysis</h2>
        <table>
            <thead>
                <tr>
                    <th>Payment Method</th>
                    <th class="text-center">Transaction Count</th>
                    <th class="text-right">Total Amount (RM)</th>
                    <th class="text-right">Percentage</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total_payment_amount = 0;
                foreach ($payment_data as $row) {
                    $total_payment_amount += $row['total'];
                }
                
                foreach ($payment_data as $row): 
                    $percentage = $total_payment_amount > 0 ? ($row['total'] / $total_payment_amount) * 100 : 0;
                ?>
                <tr>
                    <td><?php echo ucfirst($row['payment_method']); ?></td>
                    <td class="text-center"><?php echo number_format($row['count']); ?></td>
                    <td class="text-right"><?php echo number_format($row['total'], 2); ?></td>
                    <td class="text-right"><?php echo number_format($percentage, 1); ?>%</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="footer">
        <p>This report was generated automatically by the POS System</p>
        <p>For questions or support, please contact your system administrator</p>
    </div>
    
    <script>
        // Auto-print when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
