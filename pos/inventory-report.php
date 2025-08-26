<?php
session_start();
require_once 'include/conn.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get filter parameters
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$supplier_filter = isset($_GET['supplier']) ? (int)$_GET['supplier'] : 0;
$stock_status = isset($_GET['stock_status']) ? $_GET['stock_status'] : 'all';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Build WHERE clause for filters
$where_conditions = [];
$params = [];
$param_types = '';

if ($category_filter > 0) {
    $where_conditions[] = "p.category_id = ?";
    $params[] = $category_filter;
    $param_types .= 'i';
}

if ($supplier_filter > 0) {
    $where_conditions[] = "p.supplier_id = ?";
    $params[] = $supplier_filter;
    $param_types .= 'i';
}

if ($stock_status === 'low') {
    $where_conditions[] = "p.stock_quantity <= p.min_stock_level";
} elseif ($stock_status === 'out') {
    $where_conditions[] = "p.stock_quantity = 0";
} elseif ($stock_status === 'overstock') {
    $where_conditions[] = "p.stock_quantity > p.max_stock_level AND p.max_stock_level > 0";
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get inventory statistics
$stats_query = "SELECT 
    COUNT(*) as total_products,
    SUM(stock_quantity) as total_stock,
    SUM(stock_quantity * cost_price) as total_value,
    COUNT(CASE WHEN stock_quantity <= min_stock_level THEN 1 END) as low_stock_count,
    COUNT(CASE WHEN stock_quantity = 0 THEN 1 END) as out_of_stock_count,
    COUNT(CASE WHEN stock_quantity > max_stock_level AND max_stock_level > 0 THEN 1 END) as overstock_count
FROM products p 
$where_clause";

if (!empty($params)) {
    $stmt = mysqli_prepare($conn, $stats_query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, $param_types, ...$params);
        mysqli_stmt_execute($stmt);
        $stats_result = mysqli_stmt_get_result($stmt);
    } else {
        $stats_result = false;
    }
} else {
    $stats_result = mysqli_query($conn, $stats_query);
}

if ($stats_result) {
    $stats = mysqli_fetch_assoc($stats_result);
} else {
    // Default values if query fails
    $stats = [
        'total_products' => 0,
        'total_stock' => 0,
        'total_value' => 0,
        'low_stock_count' => 0,
        'out_of_stock_count' => 0,
        'overstock_count' => 0
    ];
}

// Get inventory list
$inventory_query = "SELECT 
    p.id,
    p.sku,
    p.barcode,
    p.name,
    p.description,
    p.cost_price,
    p.selling_price,
    p.stock_quantity,
    p.min_stock_level,
    p.max_stock_level,
    p.is_active,
    c.name as category_name,
    s.name as supplier_name,
    u.name as uom_name,
    u.abbreviation as uom_abbr,
    (p.stock_quantity * p.cost_price) as stock_value,
    (p.stock_quantity * p.selling_price) as retail_value
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
LEFT JOIN suppliers s ON p.supplier_id = s.id
LEFT JOIN uom u ON p.uom_id = u.id
$where_clause
ORDER BY p.name";

if (!empty($params)) {
    $stmt = mysqli_prepare($conn, $inventory_query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, $param_types, ...$params);
        mysqli_stmt_execute($stmt);
        $inventory_result = mysqli_stmt_get_result($stmt);
    } else {
        $inventory_result = false;
    }
} else {
    $inventory_result = mysqli_query($conn, $inventory_query);
}

// Get categories for filter
$categories_result = mysqli_query($conn, "SELECT id, name FROM categories WHERE is_active = 1 ORDER BY name");
if (!$categories_result) {
    $categories_result = false;
}

// Get suppliers for filter
$suppliers_result = mysqli_query($conn, "SELECT id, name FROM suppliers WHERE is_active = 1 ORDER BY name");
if (!$suppliers_result) {
    $suppliers_result = false;
}

// Get recent stock movements
$movements_result = false;
$stock_take_result = false;

// Check if stock_movements table exists
$check_movements = mysqli_query($conn, "SHOW TABLES LIKE 'stock_movements'");
if (mysqli_num_rows($check_movements) > 0) {
    $movements_query = "SELECT 
        sm.id,
        sm.movement_type,
        sm.quantity,
        sm.reference_type,
        sm.notes,
        sm.created_at,
        p.name as product_name,
        p.sku,
        u.full_name as user_name
    FROM stock_movements sm
    JOIN products p ON sm.product_id = p.id
    JOIN users u ON sm.created_by = u.id
    ORDER BY sm.created_at DESC
    LIMIT 10";
    
    $movements_result = mysqli_query($conn, $movements_query);
    if (!$movements_result) {
        // If the query fails, try a simpler version
        $movements_query = "SELECT 
            sm.id,
            sm.movement_type,
            sm.quantity,
            sm.reference_type,
            sm.notes,
            sm.created_at,
            p.name as product_name,
            p.sku
        FROM stock_movements sm
        JOIN products p ON sm.product_id = p.id
        ORDER BY sm.created_at DESC
        LIMIT 10";
        $movements_result = mysqli_query($conn, $movements_query);
    }
}

// Check if stock_take_sessions table exists
$check_stock_take = mysqli_query($conn, "SHOW TABLES LIKE 'stock_take_sessions'");
if (mysqli_num_rows($check_stock_take) > 0) {
    $stock_take_query = "SELECT 
        sts.id,
        sts.session_name,
        sts.start_date,
        sts.end_date,
        sts.status,
        sts.notes,
        u.full_name as created_by_name,
        COUNT(sti.id) as items_counted
    FROM stock_take_sessions sts
    JOIN users u ON sts.created_by = u.id
    LEFT JOIN stock_take_items sti ON sts.id = sti.session_id
    GROUP BY sts.id
    ORDER BY sts.start_date DESC
    LIMIT 5";
    
    $stock_take_result = mysqli_query($conn, $stock_take_query);
    if (!$stock_take_result) {
        // If the query fails, try a simpler version
        $stock_take_query = "SELECT 
            sts.id,
            sts.session_name,
            sts.start_date,
            sts.end_date,
            sts.status,
            sts.notes
        FROM stock_take_sessions sts
        ORDER BY sts.start_date DESC
        LIMIT 5";
        $stock_take_result = mysqli_query($conn, $stock_take_query);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Report - POS System</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Print.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/print-js/1.6.0/print.min.js"></script>
</head>
<body class="bg-gray-100">
    <?php include 'include/header.php'; ?>
    <?php include 'include/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content lg:ml-64 pt-16">
        <div class="p-6">
            <!-- Page Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Inventory Report</h1>
                    <p class="text-gray-600">Comprehensive inventory analysis and reporting</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="exportToExcel()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <i class="fas fa-file-excel"></i>
                        <span>Export Excel</span>
                    </button>
                    <button onclick="printReport()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <i class="fas fa-print"></i>
                        <span>Print</span>
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Filters</h2>
                <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="category" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Supplier</label>
                        <select name="supplier" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="0">All Suppliers</option>
                            <?php if ($suppliers_result): ?>
                                <?php while ($supplier = mysqli_fetch_assoc($suppliers_result)): ?>
                                    <option value="<?= $supplier['id'] ?>" <?= $supplier_filter == $supplier['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($supplier['name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stock Status</label>
                        <select name="stock_status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="all" <?= $stock_status === 'all' ? 'selected' : '' ?>>All Items</option>
                            <option value="low" <?= $stock_status === 'low' ? 'selected' : '' ?>>Low Stock</option>
                            <option value="out" <?= $stock_status === 'out' ? 'selected' : '' ?>>Out of Stock</option>
                            <option value="overstock" <?= $stock_status === 'overstock' ? 'selected' : '' ?>>Overstock</option>
                        </select>
                    </div>
                    
                    <div class="flex space-x-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-filter mr-2"></i>Filter
                        </button>
                        <a href="inventory-report.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-times mr-2"></i>Clear
                        </a>
                    </div>
                </form>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-boxes text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Products</p>
                            <p class="text-2xl font-bold text-gray-900"><?= number_format($stats['total_products']) ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-cubes text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Stock</p>
                            <p class="text-2xl font-bold text-gray-900"><?= number_format($stats['total_stock']) ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <i class="fas fa-dollar-sign text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Stock Value</p>
                            <p class="text-2xl font-bold text-gray-900">RM <?= number_format($stats['total_value'], 2) ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 text-red-600">
                            <i class="fas fa-exclamation-triangle text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Low Stock Items</p>
                            <p class="text-2xl font-bold text-gray-900"><?= number_format($stats['low_stock_count']) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Stock Status Chart -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Stock Status Distribution</h3>
                    <canvas id="stockStatusChart" width="400" height="200"></canvas>
                </div>
                
                <!-- Category Distribution Chart -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Products by Category</h3>
                    <canvas id="categoryChart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Inventory Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Inventory List</h3>
                </div>
                <div class="overflow-x-auto">
                    <table id="inventoryTable" class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU/Barcode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selling Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Value</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if ($inventory_result && mysqli_num_rows($inventory_result) > 0): ?>
                                <?php while ($product = mysqli_fetch_assoc($inventory_result)): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($product['name']) ?></div>
                                                <div class="text-sm text-gray-500"><?= htmlspecialchars($product['description']) ?></div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?= htmlspecialchars($product['sku']) ?></div>
                                            <div class="text-sm text-gray-500"><?= htmlspecialchars($product['barcode']) ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?= htmlspecialchars($product['category_name']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?= htmlspecialchars($product['supplier_name']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <?= number_format($product['stock_quantity']) ?> <?= htmlspecialchars($product['uom_abbr']) ?>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                Min: <?= $product['min_stock_level'] ?> | Max: <?= $product['max_stock_level'] ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            RM <?= number_format($product['cost_price'], 2) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            RM <?= number_format($product['selling_price'], 2) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            RM <?= number_format($product['stock_value'], 2) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php
                                            $stock_status_class = '';
                                            $stock_status_text = '';
                                            if ($product['stock_quantity'] == 0) {
                                                $stock_status_class = 'bg-red-100 text-red-800';
                                                $stock_status_text = 'Out of Stock';
                                            } elseif ($product['stock_quantity'] <= $product['min_stock_level']) {
                                                $stock_status_class = 'bg-yellow-100 text-yellow-800';
                                                $stock_status_text = 'Low Stock';
                                            } elseif ($product['stock_quantity'] > $product['max_stock_level'] && $product['max_stock_level'] > 0) {
                                                $stock_status_class = 'bg-orange-100 text-orange-800';
                                                $stock_status_text = 'Overstock';
                                            } else {
                                                $stock_status_class = 'bg-green-100 text-green-800';
                                                $stock_status_text = 'In Stock';
                                            }
                                            ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $stock_status_class ?>">
                                                <?= $stock_status_text ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                        No products found
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                <!-- Recent Stock Movements -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Stock Movements</h3>
                    </div>
                    <div class="p-6">
                        <?php if ($movements_result && mysqli_num_rows($movements_result) > 0): ?>
                            <div class="space-y-4">
                                <?php while ($movement = mysqli_fetch_assoc($movements_result)): ?>
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <?php
                                            $icon_class = '';
                                            $bg_class = '';
                                            if ($movement['movement_type'] === 'in') {
                                                $icon_class = 'fas fa-arrow-down text-green-600';
                                                $bg_class = 'bg-green-100';
                                            } elseif ($movement['movement_type'] === 'out') {
                                                $icon_class = 'fas fa-arrow-up text-red-600';
                                                $bg_class = 'bg-red-100';
                                            } else {
                                                $icon_class = 'fas fa-exchange-alt text-blue-600';
                                                $bg_class = 'bg-blue-100';
                                            }
                                            ?>
                                            <div class="w-8 h-8 rounded-full <?= $bg_class ?> flex items-center justify-center">
                                                <i class="<?= $icon_class ?>"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($movement['product_name']) ?>
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                <?= ucfirst($movement['movement_type']) ?>: <?= $movement['quantity'] ?> units
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-gray-500">
                                                <?= date('M j, Y H:i', strtotime($movement['created_at'])) ?>
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                <?= htmlspecialchars(isset($movement['user_name']) ? $movement['user_name'] : 'System') ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-500 text-center py-4">No recent stock movements</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Stock Take Sessions -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Stock Take Sessions</h3>
                    </div>
                    <div class="p-6">
                        <?php if ($stock_take_result && mysqli_num_rows($stock_take_result) > 0): ?>
                            <div class="space-y-4">
                                <?php while ($session = mysqli_fetch_assoc($stock_take_result)): ?>
                                    <div class="border-l-4 border-blue-500 pl-4">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-900">
                                                    <?= htmlspecialchars($session['session_name']) ?>
                                                </h4>
                                                <p class="text-sm text-gray-500">
                                                    <?= isset($session['items_counted']) ? $session['items_counted'] : '0' ?> items counted
                                                </p>
                                                <p class="text-xs text-gray-400">
                                                    Started: <?= date('M j, Y H:i', strtotime($session['start_date'])) ?>
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <?php
                                                $status_class = '';
                                                if ($session['status'] === 'completed') {
                                                    $status_class = 'bg-green-100 text-green-800';
                                                } elseif ($session['status'] === 'in_progress') {
                                                    $status_class = 'bg-yellow-100 text-yellow-800';
                                                } else {
                                                    $status_class = 'bg-red-100 text-red-800';
                                                }
                                                ?>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $status_class ?>">
                                                    <?= ucfirst(str_replace('_', ' ', $session['status'])) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-500 text-center py-4">No recent stock take sessions</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#inventoryTable').DataTable({
                pageLength: 25,
                order: [[0, 'asc']],
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });

            // Stock Status Chart
            const stockStatusCtx = document.getElementById('stockStatusChart').getContext('2d');
            new Chart(stockStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['In Stock', 'Low Stock', 'Out of Stock', 'Overstock'],
                    datasets: [{
                        data: [
                            <?= $stats['total_products'] - $stats['low_stock_count'] - $stats['out_of_stock_count'] - $stats['overstock_count'] ?>,
                            <?= $stats['low_stock_count'] ?>,
                            <?= $stats['out_of_stock_count'] ?>,
                            <?= $stats['overstock_count'] ?>
                        ],
                        backgroundColor: [
                            '#10B981',
                            '#F59E0B',
                            '#EF4444',
                            '#F97316'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Category Distribution Chart (you would need to fetch this data)
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            new Chart(categoryCtx, {
                type: 'bar',
                data: {
                    labels: ['Electronics', 'Clothing', 'Food & Beverages', 'Home & Garden'],
                    datasets: [{
                        label: 'Products',
                        data: [12, 19, 8, 15],
                        backgroundColor: '#3B82F6'
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
                            beginAtZero: true
                        }
                    }
                }
            });
        });

        function exportToExcel() {
            // Implementation for Excel export
            alert('Excel export functionality would be implemented here');
        }

        function printReport() {
            window.print();
        }
    </script>
</body>
</html>
