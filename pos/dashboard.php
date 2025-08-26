<?php
include 'include/conn.php';
include 'include/session.php';
include 'include/head.php';
include 'include/header.php';
include 'include/sidebar.php';

// Get today's date
$today = date('Y-m-d');
$current_month = date('Y-m');

// Get statistics
$query_today_sales = mysqli_query($conn, "SELECT COUNT(*) as count, SUM(total_amount) as total FROM sales WHERE DATE(created_at) = '$today'");
$today_sales = mysqli_fetch_array($query_today_sales);

$query_month_sales = mysqli_query($conn, "SELECT COUNT(*) as count, SUM(total_amount) as total FROM sales WHERE DATE_FORMAT(created_at, '%Y-%m') = '$current_month'");
$month_sales = mysqli_fetch_array($query_month_sales);

$query_total_products = mysqli_query($conn, "SELECT COUNT(*) as count FROM products WHERE is_active = 1");
$total_products = mysqli_fetch_array($query_total_products);

$query_low_stock = mysqli_query($conn, "SELECT COUNT(*) as count FROM products WHERE stock_quantity <= min_stock_level AND is_active = 1");
$low_stock = mysqli_fetch_array($query_low_stock);

$query_total_members = mysqli_query($conn, "SELECT COUNT(*) as count FROM members WHERE is_active = 1");
$total_members = mysqli_fetch_array($query_total_members);

// Get recent sales
$query_recent_sales = mysqli_query($conn, "
    SELECT s.*, m.name as member_name, u.full_name as cashier_name 
    FROM sales s 
    LEFT JOIN members m ON s.member_id = m.id 
    LEFT JOIN users u ON s.user_id = u.id 
    ORDER BY s.created_at DESC 
    LIMIT 10
");

// Get top selling products
$query_top_products = mysqli_query($conn, "
    SELECT p.name, p.sku, SUM(si.quantity) as total_sold, SUM(si.total_price) as total_revenue
    FROM sale_items si
    JOIN products p ON si.product_id = p.id
    JOIN sales s ON si.sale_id = s.id
    WHERE DATE_FORMAT(s.created_at, '%Y-%m') = '$current_month'
    GROUP BY p.id
    ORDER BY total_sold DESC
    LIMIT 5
");
?>

<!-- Main Content -->
<div class="main-content ml-0 lg:ml-64 pt-16">
    <div class="p-6">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-600">Welcome back, <?= $_SESSION['full_name'] ?>!</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Today's Sales -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-chart-line text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Today's Sales</p>
                        <p class="text-2xl font-bold text-gray-900"><?= number_format($today_sales['total'] ?? 0, 2) ?></p>
                        <p class="text-sm text-gray-500"><?= $today_sales['count'] ?? 0 ?> transactions</p>
                    </div>
                </div>
            </div>

            <!-- Monthly Sales -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-calendar-alt text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Monthly Sales</p>
                        <p class="text-2xl font-bold text-gray-900"><?= number_format($month_sales['total'] ?? 0, 2) ?></p>
                        <p class="text-sm text-gray-500"><?= $month_sales['count'] ?? 0 ?> transactions</p>
                    </div>
                </div>
            </div>

            <!-- Total Products -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-boxes text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Products</p>
                        <p class="text-2xl font-bold text-gray-900"><?= $total_products['count'] ?? 0 ?></p>
                        <p class="text-sm text-gray-500">Active inventory</p>
                    </div>
                </div>
            </div>

            <!-- Low Stock Alert -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <i class="fas fa-exclamation-triangle text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Low Stock Items</p>
                        <p class="text-2xl font-bold text-gray-900"><?= $low_stock['count'] ?? 0 ?></p>
                        <p class="text-sm text-gray-500">Need attention</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Tables Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Recent Sales -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Recent Sales</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <?php while ($sale = mysqli_fetch_array($query_recent_sales)): ?>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900"><?= $sale['invoice_number'] ?></p>
                                <p class="text-sm text-gray-500">
                                    <?= $sale['member_name'] ? $sale['member_name'] : 'Walk-in Customer' ?> • 
                                    <?= $sale['cashier_name'] ?>
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900"><?= number_format($sale['total_amount'], 2) ?></p>
                                <p class="text-sm text-gray-500"><?= date('M d, H:i', strtotime($sale['created_at'])) ?></p>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>

            <!-- Top Selling Products -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Top Selling Products (This Month)</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <?php while ($product = mysqli_fetch_array($query_top_products)): ?>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900"><?= $product['name'] ?></p>
                                <p class="text-sm text-gray-500"><?= $product['sku'] ?></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900"><?= $product['total_sold'] ?> sold</p>
                                <p class="text-sm text-gray-500"><?= number_format($product['total_revenue'], 2) ?></p>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="pos.php" class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <i class="fas fa-cash-register text-2xl text-blue-600 mb-2"></i>
                    <span class="text-sm font-medium text-gray-900">New Sale</span>
                </a>
                <a href="products.php" class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                    <i class="fas fa-plus text-2xl text-green-600 mb-2"></i>
                    <span class="text-sm font-medium text-gray-900">Add Product</span>
                </a>
                <a href="members.php" class="flex flex-col items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                    <i class="fas fa-user-plus text-2xl text-purple-600 mb-2"></i>
                    <span class="text-sm font-medium text-gray-900">Add Member</span>
                </a>
                <a href="stock-take.php" class="flex flex-col items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                    <i class="fas fa-clipboard-check text-2xl text-orange-600 mb-2"></i>
                    <span class="text-sm font-medium text-gray-900">Stock Take</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>
