<?php
session_start();
require_once 'include/conn.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get filter parameters
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-01');
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-t');
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$period = isset($_GET['period']) ? $_GET['period'] : 'month';

// Set date range based on period
if ($period === 'week') {
    $date_from = date('Y-m-d', strtotime('monday this week'));
    $date_to = date('Y-m-d', strtotime('sunday this week'));
} elseif ($period === 'month') {
    $date_from = date('Y-m-01');
    $date_to = date('Y-m-t');
} elseif ($period === 'quarter') {
    $current_quarter = ceil(date('n') / 3);
    $start_month = ($current_quarter - 1) * 3 + 1;
    $date_from = date('Y-' . str_pad($start_month, 2, '0', STR_PAD_LEFT) . '-01');
    $end_month = $start_month + 2;
    $date_to = date('Y-' . str_pad($end_month, 2, '0', STR_PAD_LEFT) . '-t');
} elseif ($period === 'year') {
    $date_from = date('Y-01-01');
    $date_to = date('Y-12-31');
}

// Build WHERE clause for filters
$where_conditions = ["s.created_at BETWEEN ? AND ?"];
$params = [$date_from, $date_to];
$param_types = 'ss';

if ($category_filter > 0) {
    $where_conditions[] = "p.category_id = ?";
    $params[] = $category_filter;
    $param_types .= 'i';
}

$where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

// Get revenue data
$revenue_query = "SELECT 
    SUM(s.total_amount) as total_revenue,
    SUM(s.subtotal) as total_subtotal,
    SUM(s.discount_amount) as total_discounts,
    SUM(s.tax_amount) as total_taxes,
    COUNT(s.id) as total_sales,
    AVG(s.total_amount) as avg_sale_value
FROM sales s
$where_clause";

$stmt = mysqli_prepare($conn, $revenue_query);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, $param_types, ...$params);
    mysqli_stmt_execute($stmt);
    $revenue_result = mysqli_stmt_get_result($stmt);
    $revenue_data = mysqli_fetch_assoc($revenue_result);
} else {
    $revenue_data = [
        'total_revenue' => 0,
        'total_subtotal' => 0,
        'total_discounts' => 0,
        'total_taxes' => 0,
        'total_sales' => 0,
        'avg_sale_value' => 0
    ];
}

// Get cost of goods sold (COGS)
$cogs_query = "SELECT 
    SUM(si.quantity * p.cost_price) as total_cogs,
    SUM(si.quantity * (p.selling_price - p.cost_price)) as total_profit_margin,
    SUM(si.quantity * p.selling_price) as total_retail_value
FROM sales s
JOIN sale_items si ON s.id = si.sale_id
JOIN products p ON si.product_id = p.id
$where_clause";

$stmt = mysqli_prepare($conn, $cogs_query);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, $param_types, ...$params);
    mysqli_stmt_execute($stmt);
    $cogs_result = mysqli_stmt_get_result($stmt);
    $cogs_data = mysqli_fetch_assoc($cogs_result);
} else {
    $cogs_data = [
        'total_cogs' => 0,
        'total_profit_margin' => 0,
        'total_retail_value' => 0
    ];
}

// Calculate profit metrics
$gross_profit = $cogs_data['total_profit_margin'];
$net_profit = $revenue_data['total_revenue'] - $cogs_data['total_cogs'];
$gross_profit_margin = $revenue_data['total_revenue'] > 0 ? ($gross_profit / $revenue_data['total_revenue']) * 100 : 0;
$net_profit_margin = $revenue_data['total_revenue'] > 0 ? ($net_profit / $revenue_data['total_revenue']) * 100 : 0;

// Get daily revenue trend
$daily_revenue_query = "SELECT 
    DATE(s.created_at) as sale_date,
    SUM(s.total_amount) as daily_revenue,
    COUNT(s.id) as daily_sales
FROM sales s
$where_clause
GROUP BY DATE(s.created_at)
ORDER BY sale_date";

$stmt = mysqli_prepare($conn, $daily_revenue_query);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, $param_types, ...$params);
    mysqli_stmt_execute($stmt);
    $daily_revenue_result = mysqli_stmt_get_result($stmt);
} else {
    $daily_revenue_result = false;
}

// Get top selling products
$top_products_query = "SELECT 
    p.name as product_name,
    p.sku,
    SUM(si.quantity) as total_quantity,
    SUM(si.quantity * si.unit_price) as total_revenue,
    SUM(si.quantity * p.cost_price) as total_cost,
    SUM(si.quantity * (si.unit_price - p.cost_price)) as total_profit,
    c.name as category_name
FROM sales s
JOIN sale_items si ON s.id = si.sale_id
JOIN products p ON si.product_id = p.id
LEFT JOIN categories c ON p.category_id = c.id
$where_clause
GROUP BY p.id
ORDER BY total_revenue DESC
LIMIT 10";

$stmt = mysqli_prepare($conn, $top_products_query);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, $param_types, ...$params);
    mysqli_stmt_execute($stmt);
    $top_products_result = mysqli_stmt_get_result($stmt);
} else {
    $top_products_result = false;
}

// Get categories for filter
$categories_result = mysqli_query($conn, "SELECT id, name FROM categories WHERE is_active = 1 ORDER BY name");
if (!$categories_result) {
    $categories_result = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profit & Loss Report - POS System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <?php include 'include/header.php'; ?>
    <?php include 'include/sidebar.php'; ?>

    <div class="main-content lg:ml-64 pt-16">
        <div class="p-6">
            <!-- Page Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Profit & Loss Report</h1>
                    <p class="text-gray-600">Financial performance analysis</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="exportToExcel()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-file-excel mr-2"></i>Export Excel
                    </button>
                    <button onclick="printReport()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-print mr-2"></i>Print
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Filters</h2>
                <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Period</label>
                        <select name="period" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="week" <?= $period === 'week' ? 'selected' : '' ?>>This Week</option>
                            <option value="month" <?= $period === 'month' ? 'selected' : '' ?>>This Month</option>
                            <option value="quarter" <?= $period === 'quarter' ? 'selected' : '' ?>>This Quarter</option>
                            <option value="year" <?= $period === 'year' ? 'selected' : '' ?>>This Year</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                        <input type="date" name="date_from" value="<?= $date_from ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                        <input type="date" name="date_to" value="<?= $date_to ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="category" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="0">All Categories</option>
                            <?php if ($categories_result): ?>
                                <?php while ($category = mysqli_fetch_assoc($categories_result)): ?>
                                    <option value="<?= $category['id'] ?>" <?= $category_filter == $category['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="flex space-x-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                            <i class="fas fa-filter mr-2"></i>Filter
                        </button>
                        <a href="profit-loss.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                            <i class="fas fa-times mr-2"></i>Clear
                        </a>
                    </div>
                </form>
            </div>

            <!-- Key Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-dollar-sign text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                            <p class="text-2xl font-bold text-gray-900">RM <?= number_format($revenue_data['total_revenue'], 2) ?></p>
                            <p class="text-xs text-gray-500"><?= $revenue_data['total_sales'] ?> transactions</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 text-red-600">
                            <i class="fas fa-shopping-cart text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Cost of Goods</p>
                            <p class="text-2xl font-bold text-gray-900">RM <?= number_format($cogs_data['total_cogs'], 2) ?></p>
                            <p class="text-xs text-gray-500">COGS</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-chart-line text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Gross Profit</p>
                            <p class="text-2xl font-bold text-gray-900">RM <?= number_format($gross_profit, 2) ?></p>
                            <p class="text-xs text-gray-500"><?= number_format($gross_profit_margin, 1) ?>% margin</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <i class="fas fa-percentage text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Net Profit</p>
                            <p class="text-2xl font-bold text-gray-900">RM <?= number_format($net_profit, 2) ?></p>
                            <p class="text-xs text-gray-500"><?= number_format($net_profit_margin, 1) ?>% margin</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profit & Loss Statement -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Profit & Loss Statement</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <tbody class="space-y-2">
                            <tr class="border-b border-gray-200">
                                <td class="py-2 font-medium text-gray-900">Revenue</td>
                                <td class="py-2 text-right font-medium text-gray-900">RM <?= number_format($revenue_data['total_revenue'], 2) ?></td>
                            </tr>
                            <tr class="border-b border-gray-200">
                                <td class="py-2 text-gray-600 pl-4">Subtotal</td>
                                <td class="py-2 text-right text-gray-600">RM <?= number_format($revenue_data['total_subtotal'], 2) ?></td>
                            </tr>
                            <tr class="border-b border-gray-200">
                                <td class="py-2 text-gray-600 pl-4">Taxes</td>
                                <td class="py-2 text-right text-gray-600">RM <?= number_format($revenue_data['total_taxes'], 2) ?></td>
                            </tr>
                            <tr class="border-b border-gray-200">
                                <td class="py-2 text-gray-600 pl-4">Discounts</td>
                                <td class="py-2 text-right text-red-600">-RM <?= number_format($revenue_data['total_discounts'], 2) ?></td>
                            </tr>
                            <tr class="border-b-2 border-gray-300">
                                <td class="py-2 font-medium text-gray-900">Cost of Goods Sold</td>
                                <td class="py-2 text-right font-medium text-red-600">-RM <?= number_format($cogs_data['total_cogs'], 2) ?></td>
                            </tr>
                            <tr class="border-b-2 border-gray-300 bg-green-50">
                                <td class="py-2 font-bold text-gray-900">Gross Profit</td>
                                <td class="py-2 text-right font-bold text-green-600">RM <?= number_format($gross_profit, 2) ?></td>
                            </tr>
                            <tr class="bg-blue-50">
                                <td class="py-2 font-bold text-gray-900">Net Profit</td>
                                <td class="py-2 text-right font-bold text-blue-600">RM <?= number_format($net_profit, 2) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Revenue Trend Chart -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Daily Revenue Trend</h3>
                <canvas id="revenueTrendChart" width="400" height="200"></canvas>
            </div>

            <!-- Top Products Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Top Selling Products</h3>
                </div>
                <div class="overflow-x-auto">
                    <table id="topProductsTable" class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cost</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Profit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Margin %</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if ($top_products_result && mysqli_num_rows($top_products_result) > 0): ?>
                                <?php while ($product = mysqli_fetch_assoc($top_products_result)): ?>
                                    <?php 
                                    $profit_margin = $product['total_revenue'] > 0 ? ($product['total_profit'] / $product['total_revenue']) * 100 : 0;
                                    ?>
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($product['product_name']) ?></div>
                                                <div class="text-sm text-gray-500"><?= htmlspecialchars($product['sku']) ?></div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <?= htmlspecialchars($product['category_name']) ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <?= number_format($product['total_quantity']) ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            RM <?= number_format($product['total_revenue'], 2) ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            RM <?= number_format($product['total_cost'], 2) ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-green-600 font-medium">
                                            RM <?= number_format($product['total_profit'], 2) ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $profit_margin >= 20 ? 'bg-green-100 text-green-800' : ($profit_margin >= 10 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') ?>">
                                                <?= number_format($profit_margin, 1) ?>%
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        No products found
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#topProductsTable').DataTable({
                pageLength: 10,
                order: [[3, 'desc']],
                responsive: true
            });

            // Revenue Trend Chart
            <?php if ($daily_revenue_result && mysqli_num_rows($daily_revenue_result) > 0): ?>
            const revenueCtx = document.getElementById('revenueTrendChart').getContext('2d');
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: [
                        <?php 
                        mysqli_data_seek($daily_revenue_result, 0);
                        while ($row = mysqli_fetch_assoc($daily_revenue_result)) {
                            echo "'" . date('M j', strtotime($row['sale_date'])) . "',";
                        }
                        ?>
                    ],
                    datasets: [{
                        label: 'Daily Revenue',
                        data: [
                            <?php 
                            mysqli_data_seek($daily_revenue_result, 0);
                            while ($row = mysqli_fetch_assoc($daily_revenue_result)) {
                                echo $row['daily_revenue'] . ",";
                            }
                            ?>
                        ],
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'RM ' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
            <?php endif; ?>
        });

        function exportToExcel() {
            alert('Excel export functionality would be implemented here');
        }

        function printReport() {
            window.print();
        }
    </script>
</body>
</html>
