<?php
include 'include/conn.php';
include 'include/session.php';
include 'include/head.php';
include 'include/header.php';
include 'include/sidebar.php';

// Get default date range (current month)
$today = date('Y-m-d');
$current_month = date('Y-m');
$month_start = $current_month . '-01';
$month_end = $today;

// Get quick statistics for the current month
$query_month_stats = mysqli_query($conn, "SELECT 
    COUNT(*) as total_sales,
    SUM(total_amount) as total_revenue,
    AVG(total_amount) as avg_sale,
    COUNT(DISTINCT member_id) as unique_customers
FROM sales 
WHERE DATE(created_at) BETWEEN '$month_start' AND '$month_end'");
$month_stats = mysqli_fetch_array($query_month_stats);

// Get previous month for comparison
$prev_month_start = date('Y-m-d', strtotime($month_start . ' -1 month'));
$prev_month_end = date('Y-m-d', strtotime($month_start . ' -1 day'));

$query_prev_stats = mysqli_query($conn, "SELECT 
    COUNT(*) as total_sales,
    SUM(total_amount) as total_revenue,
    AVG(total_amount) as avg_sale
FROM sales 
WHERE DATE(created_at) BETWEEN '$prev_month_start' AND '$prev_month_end'");
$prev_stats = mysqli_fetch_array($query_prev_stats);

// Calculate growth percentages
$revenue_growth = $prev_stats['total_revenue'] > 0 ? 
    round((($month_stats['total_revenue'] - $prev_stats['total_revenue']) / $prev_stats['total_revenue']) * 100, 1) : 0;
$sales_growth = $prev_stats['total_sales'] > 0 ? 
    round((($month_stats['total_sales'] - $prev_stats['total_sales']) / $prev_stats['total_sales']) * 100, 1) : 0;
$avg_growth = $prev_stats['avg_sale'] > 0 ? 
    round((($month_stats['avg_sale'] - $prev_stats['avg_sale']) / $prev_stats['avg_sale']) * 100, 1) : 0;
?>

<!-- Main Content -->
<div class="main-content ml-0 lg:ml-64 pt-16">
    <!-- Background Gradient -->
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-indigo-50 -z-10"></div>
    
    <div class="p-4 sm:p-6 lg:p-8">
        <!-- Page Header with Glassmorphism -->
        <div class="backdrop-blur-sm bg-white/70 rounded-2xl shadow-xl border border-white/20 p-6 mb-8">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0">
                <div>
                    <div class="flex items-center space-x-3 mb-2">
                        <div class="p-2 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl">
                            <i class="fas fa-chart-line text-white text-xl"></i>
                        </div>
                        <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                            Sales Report
                        </h1>
                    </div>
                    <p class="text-gray-600 text-lg">Comprehensive sales analytics and insights</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="exportReport()" class="group relative inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <i class="fas fa-download mr-2"></i>
                        <span class="font-medium">Export Report</span>
                    </button>
                    <button onclick="printReport()" class="group relative inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <i class="fas fa-print mr-2"></i>
                        <span class="font-medium">Print Report</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Report Filters -->
        <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-xl border border-white/20 p-4 lg:p-6 mb-6 lg:mb-8">
            <div class="flex items-center space-x-3 mb-4 lg:mb-6">
                <div class="p-2 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg">
                    <i class="fas fa-filter text-white"></i>
                </div>
                <h3 class="text-base lg:text-lg font-semibold text-gray-900">Report Parameters</h3>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 lg:gap-6">
                <div class="space-y-2">
                    <label class="block text-xs lg:text-sm font-semibold text-gray-700">Start Date</label>
                    <input type="date" id="start-date" value="<?php echo $month_start; ?>"
                           class="w-full px-3 lg:px-4 py-2.5 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-lg lg:rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm lg:text-base">
                </div>
                
                <div class="space-y-2">
                    <label class="block text-xs lg:text-sm font-semibold text-gray-700">End Date</label>
                    <input type="date" id="end-date" value="<?php echo $month_end; ?>"
                           class="w-full px-3 lg:px-4 py-2.5 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-lg lg:rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm lg:text-base">
                </div>
                
                <div class="space-y-2">
                    <label class="block text-xs lg:text-sm font-semibold text-gray-700">Report Type</label>
                    <select id="report-type" class="w-full px-3 lg:px-4 py-2.5 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-lg lg:rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm lg:text-base">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-xs lg:text-sm font-semibold text-gray-700">Generate Report</label>
                    <button onclick="generateReport()" class="w-full px-4 py-2.5 lg:py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg lg:rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 font-medium">
                        <i class="fas fa-chart-bar mr-2"></i>Generate
                    </button>
                </div>
            </div>
            
            <!-- Quick Filter Buttons -->
            <div class="flex flex-wrap items-center space-x-2 mt-4 lg:mt-6">
                <button onclick="setDateRange('today')" class="px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    Today
                </button>
                <button onclick="setDateRange('yesterday')" class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    Yesterday
                </button>
                <button onclick="setDateRange('week')" class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    This Week
                </button>
                <button onclick="setDateRange('month')" class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    This Month
                </button>
                <button onclick="setDateRange('quarter')" class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    This Quarter
                </button>
                <button onclick="setDateRange('year')" class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    This Year
                </button>
            </div>
        </div>

        <!-- Key Metrics Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6 lg:mb-8">
            <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-lg border border-white/20 p-4 lg:p-6 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between mb-2">
                    <div class="p-2 lg:p-3 bg-green-100 rounded-lg lg:rounded-xl">
                        <i class="fas fa-dollar-sign text-green-600 text-sm lg:text-xl"></i>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-medium text-gray-500">vs Last Period</span>
                        <div class="flex items-center space-x-1">
                            <i class="fas fa-arrow-<?php echo $revenue_growth >= 0 ? 'up' : 'down'; ?> text-<?php echo $revenue_growth >= 0 ? 'green' : 'red'; ?>-500 text-xs"></i>
                            <span class="text-xs font-medium text-<?php echo $revenue_growth >= 0 ? 'green' : 'red'; ?>-500"><?php echo abs($revenue_growth); ?>%</span>
                        </div>
                    </div>
                </div>
                <p class="text-xs lg:text-sm font-medium text-gray-600">Total Revenue</p>
                <p class="text-lg lg:text-2xl font-bold text-gray-900" id="total-revenue">RM <?php echo number_format($month_stats['total_revenue'] ?? 0, 2); ?></p>
            </div>
            
            <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-lg border border-white/20 p-4 lg:p-6 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between mb-2">
                    <div class="p-2 lg:p-3 bg-blue-100 rounded-lg lg:rounded-xl">
                        <i class="fas fa-shopping-cart text-blue-600 text-sm lg:text-xl"></i>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-medium text-gray-500">vs Last Period</span>
                        <div class="flex items-center space-x-1">
                            <i class="fas fa-arrow-<?php echo $sales_growth >= 0 ? 'up' : 'down'; ?> text-<?php echo $sales_growth >= 0 ? 'green' : 'red'; ?>-500 text-xs"></i>
                            <span class="text-xs font-medium text-<?php echo $sales_growth >= 0 ? 'green' : 'red'; ?>-500"><?php echo abs($sales_growth); ?>%</span>
                        </div>
                    </div>
                </div>
                <p class="text-xs lg:text-sm font-medium text-gray-600">Total Sales</p>
                <p class="text-lg lg:text-2xl font-bold text-gray-900" id="total-sales"><?php echo number_format($month_stats['total_sales'] ?? 0); ?></p>
            </div>
            
            <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-lg border border-white/20 p-4 lg:p-6 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between mb-2">
                    <div class="p-2 lg:p-3 bg-purple-100 rounded-lg lg:rounded-xl">
                        <i class="fas fa-chart-line text-purple-600 text-sm lg:text-xl"></i>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-medium text-gray-500">vs Last Period</span>
                        <div class="flex items-center space-x-1">
                            <i class="fas fa-arrow-<?php echo $avg_growth >= 0 ? 'up' : 'down'; ?> text-<?php echo $avg_growth >= 0 ? 'green' : 'red'; ?>-500 text-xs"></i>
                            <span class="text-xs font-medium text-<?php echo $avg_growth >= 0 ? 'green' : 'red'; ?>-500"><?php echo abs($avg_growth); ?>%</span>
                        </div>
                    </div>
                </div>
                <p class="text-xs lg:text-sm font-medium text-gray-600">Average Sale</p>
                <p class="text-lg lg:text-2xl font-bold text-gray-900" id="avg-sale">RM <?php echo number_format($month_stats['avg_sale'] ?? 0, 2); ?></p>
            </div>
            
            <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-lg border border-white/20 p-4 lg:p-6 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between mb-2">
                    <div class="p-2 lg:p-3 bg-orange-100 rounded-lg lg:rounded-xl">
                        <i class="fas fa-users text-orange-600 text-sm lg:text-xl"></i>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-medium text-gray-500">Unique</span>
                    </div>
                </div>
                <p class="text-xs lg:text-sm font-medium text-gray-600">Customers</p>
                <p class="text-lg lg:text-2xl font-bold text-gray-900" id="unique-customers"><?php echo number_format($month_stats['unique_customers'] ?? 0); ?></p>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8 mb-6 lg:mb-8">
            <!-- Revenue Chart -->
            <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-xl border border-white/20 p-4 lg:p-6">
                <div class="flex items-center justify-between mb-4 lg:mb-6">
                    <div class="flex items-center space-x-2 lg:space-x-3">
                        <div class="p-1.5 lg:p-2 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg">
                            <i class="fas fa-chart-area text-white text-sm lg:text-base"></i>
                        </div>
                        <h3 class="text-lg lg:text-xl font-semibold text-gray-900">Revenue Trend</h3>
                    </div>
                </div>
                <div class="h-64 lg:h-80">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
            
            <!-- Sales Count Chart -->
            <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-xl border border-white/20 p-4 lg:p-6">
                <div class="flex items-center justify-between mb-4 lg:mb-6">
                    <div class="flex items-center space-x-2 lg:space-x-3">
                        <div class="p-1.5 lg:p-2 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-lg">
                            <i class="fas fa-chart-bar text-white text-sm lg:text-base"></i>
                        </div>
                        <h3 class="text-lg lg:text-xl font-semibold text-gray-900">Sales Count</h3>
                    </div>
                </div>
                <div class="h-64 lg:h-80">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Products and Categories -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8 mb-6 lg:mb-8">
            <!-- Top Products -->
            <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-xl border border-white/20 p-4 lg:p-6">
                <div class="flex items-center justify-between mb-4 lg:mb-6">
                    <div class="flex items-center space-x-2 lg:space-x-3">
                        <div class="p-1.5 lg:p-2 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg">
                            <i class="fas fa-box text-white text-sm lg:text-base"></i>
                        </div>
                        <h3 class="text-lg lg:text-xl font-semibold text-gray-900">Top Products</h3>
                    </div>
                </div>
                <div id="top-products-list" class="space-y-3">
                    <!-- Top products will be loaded here -->
                </div>
            </div>
            
            <!-- Top Categories -->
            <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-xl border border-white/20 p-4 lg:p-6">
                <div class="flex items-center justify-between mb-4 lg:mb-6">
                    <div class="flex items-center space-x-2 lg:space-x-3">
                        <div class="p-1.5 lg:p-2 bg-gradient-to-r from-orange-500 to-red-500 rounded-lg">
                            <i class="fas fa-tags text-white text-sm lg:text-base"></i>
                        </div>
                        <h3 class="text-lg lg:text-xl font-semibold text-gray-900">Top Categories</h3>
                    </div>
                </div>
                <div id="top-categories-list" class="space-y-3">
                    <!-- Top categories will be loaded here -->
                </div>
            </div>
        </div>

        <!-- Detailed Breakdown Table -->
        <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-xl border border-white/20 overflow-hidden">
            <div class="px-4 lg:px-6 py-4 lg:py-6 border-b border-gray-200/50 bg-gradient-to-r from-gray-50/50 to-gray-100/50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2 lg:space-x-3">
                        <div class="p-1.5 lg:p-2 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-lg">
                            <i class="fas fa-table text-white text-sm lg:text-base"></i>
                        </div>
                        <h3 class="text-lg lg:text-xl font-semibold text-gray-900">Detailed Breakdown</h3>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50/80 to-gray-100/80">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Period</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Sales Count</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Revenue</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Avg Sale</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Growth</th>
                        </tr>
                    </thead>
                    <tbody id="breakdown-tbody" class="bg-white/50 divide-y divide-gray-200/50">
                        <!-- Breakdown data will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-50">
    <div class="flex items-center justify-center min-h-screen">
        <div class="backdrop-blur-md bg-white/95 rounded-2xl shadow-2xl p-8 border border-white/20">
            <div class="flex items-center space-x-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Generating Report</h3>
                    <p class="text-sm text-gray-600">Please wait while we process your data...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let revenueChart, salesChart;

$(document).ready(function() {
    console.log('Document ready - initializing sales report');
    
    // Check if required elements exist
    if ($('#start-date').length === 0) {
        console.error('Start date element not found');
        showAlert('Start date element not found', 'error');
        return;
    }
    
    if ($('#end-date').length === 0) {
        console.error('End date element not found');
        showAlert('End date element not found', 'error');
        return;
    }
    
    if ($('#report-type').length === 0) {
        console.error('Report type element not found');
        showAlert('Report type element not found', 'error');
        return;
    }
    
    console.log('Form elements found, initializing charts');
    
    // Initialize charts
    initializeCharts();
    
    // Load initial report data
    console.log('Generating initial report');
    generateReport();
});

function initializeCharts() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Revenue (RM)',
                data: [],
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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
    
    // Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    salesChart = new Chart(salesCtx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Sales Count',
                data: [],
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

function generateReport() {
    const startDate = $('#start-date').val();
    const endDate = $('#end-date').val();
    const reportType = $('#report-type').val();
    
    console.log('Generate Report called with:', { startDate, endDate, reportType });
    
    if (!startDate || !endDate) {
        showAlert('Please select start and end dates', 'error');
        return;
    }
    
    if (new Date(startDate) > new Date(endDate)) {
        showAlert('Start date cannot be after end date', 'error');
        return;
    }
    
    $('#loading-overlay').removeClass('hidden');
    
    $.ajax({
        url: 'ajax/get-sales-report.php',
        type: 'GET',
        dataType: 'json',
        data: {
            start_date: startDate,
            end_date: endDate,
            report_type: reportType
        },
        success: function(response) {
            console.log('AJAX Response:', response);
            $('#loading-overlay').addClass('hidden');
            
            // Check if response is already an object (jQuery might auto-parse JSON)
            let data;
            if (typeof response === 'object') {
                data = response;
            } else {
                try {
                    data = JSON.parse(response);
                } catch (e) {
                    console.error('Error parsing response:', e);
                    console.error('Raw response:', response);
                    showAlert('Error processing report data: Invalid JSON response', 'error');
                    return;
                }
            }
            
            console.log('Parsed data:', data);
            
            if (data.error) {
                showAlert(data.error, 'error');
                return;
            }
            
            // Update metrics
            updateMetrics(data.metrics);
            
            // Update charts
            updateCharts(data.charts);
            
            // Update top products
            updateTopProducts(data.topProducts);
            
            // Update top categories
            updateTopCategories(data.topCategories);
            
            // Update breakdown table
            updateBreakdownTable(data.breakdown);
            
            showAlert('Report generated successfully!', 'success');
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', { xhr, status, error });
            $('#loading-overlay').addClass('hidden');
            showAlert('Error generating report: ' + error, 'error');
        }
    });
}

function updateMetrics(metrics) {
    $('#total-revenue').text('RM ' + Number(metrics.totalRevenue).toLocaleString('en-MY', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    $('#total-sales').text(Number(metrics.totalSales).toLocaleString());
    $('#avg-sale').text('RM ' + Number(metrics.avgSale).toLocaleString('en-MY', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
}

function updateCharts(charts) {
    // Update Revenue Chart
    revenueChart.data.labels = charts.revenue.labels;
    revenueChart.data.datasets[0].data = charts.revenue.data;
    revenueChart.update();
    
    // Update Sales Chart
    salesChart.data.labels = charts.sales.labels;
    salesChart.data.datasets[0].data = charts.sales.data;
    salesChart.update();
}

function updateTopProducts(products) {
    const container = $('#top-products-list');
    container.empty();
    
    if (products.length === 0) {
        container.html('<p class="text-gray-500 text-center py-4">No product data available</p>');
        return;
    }
    
    products.forEach((product, index) => {
        const percentage = (product.quantity / products[0].quantity * 100).toFixed(1);
        const html = `
            <div class="flex items-center justify-between p-3 bg-gray-50/50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center text-white text-sm font-bold">
                        ${index + 1}
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900">${product.name}</h4>
                        <p class="text-sm text-gray-600">${product.category}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-medium text-gray-900">${product.quantity} units</p>
                    <p class="text-sm text-gray-600">RM ${Number(product.revenue).toLocaleString('en-MY', {minimumFractionDigits: 2})}</p>
                </div>
            </div>
        `;
        container.append(html);
    });
}

function updateTopCategories(categories) {
    const container = $('#top-categories-list');
    container.empty();
    
    if (categories.length === 0) {
        container.html('<p class="text-gray-500 text-center py-4">No category data available</p>');
        return;
    }
    
    categories.forEach((category, index) => {
        const html = `
            <div class="flex items-center justify-between p-3 bg-gray-50/50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gradient-to-r from-orange-500 to-red-500 rounded-lg flex items-center justify-center text-white text-sm font-bold">
                        ${index + 1}
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900">${category.name}</h4>
                        <p class="text-sm text-gray-600">${category.productCount} products</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-medium text-gray-900">${category.sales} units</p>
                    <p class="text-sm text-gray-600">RM ${Number(category.revenue).toLocaleString('en-MY', {minimumFractionDigits: 2})}</p>
                </div>
            </div>
        `;
        container.append(html);
    });
}

function updateBreakdownTable(breakdown) {
    const tbody = $('#breakdown-tbody');
    tbody.empty();
    
    if (breakdown.length === 0) {
        tbody.html('<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No data available</td></tr>');
        return;
    }
    
    breakdown.forEach(item => {
        const growthClass = item.growth >= 0 ? 'text-green-600' : 'text-red-600';
        const growthIcon = item.growth >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
        
        const html = `
            <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.period}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.salesCount}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">RM ${Number(item.revenue).toLocaleString('en-MY', {minimumFractionDigits: 2})}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">RM ${Number(item.avgSale).toLocaleString('en-MY', {minimumFractionDigits: 2})}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm ${growthClass}">
                    <i class="fas ${growthIcon} mr-1"></i>
                    ${Math.abs(item.growth)}%
                </td>
            </tr>
        `;
        tbody.append(html);
    });
}

function setDateRange(range) {
    const today = new Date();
    let startDate, endDate;
    
    switch(range) {
        case 'today':
            startDate = endDate = today.toISOString().split('T')[0];
            break;
        case 'yesterday':
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);
            startDate = endDate = yesterday.toISOString().split('T')[0];
            break;
        case 'week':
            const weekStart = new Date(today);
            weekStart.setDate(today.getDate() - today.getDay());
            startDate = weekStart.toISOString().split('T')[0];
            endDate = today.toISOString().split('T')[0];
            break;
        case 'month':
            startDate = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-01';
            endDate = today.toISOString().split('T')[0];
            break;
        case 'quarter':
            const quarter = Math.floor(today.getMonth() / 3);
            const quarterStart = new Date(today.getFullYear(), quarter * 3, 1);
            startDate = quarterStart.toISOString().split('T')[0];
            endDate = today.toISOString().split('T')[0];
            break;
        case 'year':
            startDate = today.getFullYear() + '-01-01';
            endDate = today.toISOString().split('T')[0];
            break;
    }
    
    $('#start-date').val(startDate);
    $('#end-date').val(endDate);
    generateReport();
}

function exportReport() {
    const startDate = $('#start-date').val();
    const endDate = $('#end-date').val();
    const reportType = $('#report-type').val();
    
    if (!startDate || !endDate) {
        showAlert('Please select start and end dates', 'error');
        return;
    }
    
    const params = new URLSearchParams();
    params.append('start_date', startDate);
    params.append('end_date', endDate);
    params.append('report_type', reportType);
    params.append('export', 'true');
    
    window.open('ajax/export-sales-report.php?' + params.toString(), '_blank');
}

function printReport() {
    const startDate = $('#start-date').val();
    const endDate = $('#end-date').val();
    const reportType = $('#report-type').val();
    
    if (!startDate || !endDate) {
        showAlert('Please select start and end dates', 'error');
        return;
    }
    
    const params = new URLSearchParams();
    params.append('start_date', startDate);
    params.append('end_date', endDate);
    params.append('report_type', reportType);
    params.append('print', 'true');
    
    window.open('ajax/print-sales-report.php?' + params.toString(), '_blank');
}
</script>

<?php include 'include/footer.php'; ?>
