<?php
include 'include/conn.php';
include 'include/session.php';
include 'include/head.php';
include 'include/header.php';
include 'include/sidebar.php';

// Get today's date for default filters
$today = date('Y-m-d');
$current_month = date('Y-m');

// Get quick statistics
$query_today_sales = mysqli_query($conn, "SELECT COUNT(*) as count, SUM(total_amount) as total FROM sales WHERE DATE(created_at) = '$today'");
$today_sales = mysqli_fetch_array($query_today_sales);

$query_total_sales = mysqli_query($conn, "SELECT COUNT(*) as count, SUM(total_amount) as total FROM sales");
$total_sales = mysqli_fetch_array($query_total_sales);

$query_avg_sale = mysqli_query($conn, "SELECT AVG(total_amount) as avg_amount FROM sales");
$avg_sale = mysqli_fetch_array($query_avg_sale);
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
                        <div class="p-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl">
                            <i class="fas fa-list text-white text-xl"></i>
                        </div>
                        <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                            Sales History
                        </h1>
                    </div>
                    <p class="text-gray-600 text-lg">View and manage all sales transactions</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="exportSales()" class="group relative inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <i class="fas fa-download mr-2"></i>
                        <span class="font-medium">Export</span>
                    </button>
                    <button onclick="printSales()" class="group relative inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <i class="fas fa-print mr-2"></i>
                        <span class="font-medium">Print</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6 lg:mb-8">
            <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-lg border border-white/20 p-4 lg:p-6 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs lg:text-sm font-medium text-gray-600">Total Sales</p>
                        <p class="text-lg lg:text-2xl font-bold text-gray-900" id="total-sales"><?php echo number_format($total_sales['count'] ?? 0); ?></p>
                    </div>
                    <div class="p-2 lg:p-3 bg-blue-100 rounded-lg lg:rounded-xl">
                        <i class="fas fa-shopping-cart text-blue-600 text-sm lg:text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-lg border border-white/20 p-4 lg:p-6 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs lg:text-sm font-medium text-gray-600">Total Revenue</p>
                        <p class="text-lg lg:text-2xl font-bold text-green-600" id="total-revenue">RM <?php echo number_format($total_sales['total'] ?? 0, 2); ?></p>
                    </div>
                    <div class="p-2 lg:p-3 bg-green-100 rounded-lg lg:rounded-xl">
                        <i class="fas fa-dollar-sign text-green-600 text-sm lg:text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-lg border border-white/20 p-4 lg:p-6 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs lg:text-sm font-medium text-gray-600">Today's Sales</p>
                        <p class="text-lg lg:text-2xl font-bold text-purple-600" id="today-sales"><?php echo number_format($today_sales['count'] ?? 0); ?></p>
                    </div>
                    <div class="p-2 lg:p-3 bg-purple-100 rounded-lg lg:rounded-xl">
                        <i class="fas fa-calendar-day text-purple-600 text-sm lg:text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-lg border border-white/20 p-4 lg:p-6 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs lg:text-sm font-medium text-gray-600">Avg. Sale Value</p>
                        <p class="text-lg lg:text-2xl font-bold text-orange-600" id="avg-sale">RM <?php echo number_format($avg_sale['avg_amount'] ?? 0, 2); ?></p>
                    </div>
                    <div class="p-2 lg:p-3 bg-orange-100 rounded-lg lg:rounded-xl">
                        <i class="fas fa-chart-line text-orange-600 text-sm lg:text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters with Glassmorphism -->
        <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-xl border border-white/20 p-4 lg:p-6 mb-6 lg:mb-8">
            <div class="flex items-center space-x-3 mb-4 lg:mb-6">
                <div class="p-2 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg">
                    <i class="fas fa-filter text-white"></i>
                </div>
                <h3 class="text-base lg:text-lg font-semibold text-gray-900">Filters & Search</h3>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 lg:gap-6">
                <div class="space-y-2">
                    <label class="block text-xs lg:text-sm font-semibold text-gray-700">Search Sales</label>
                    <div class="relative group">
                        <i class="fas fa-search absolute left-3 lg:left-4 top-1/2 transform -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 transition-colors duration-200"></i>
                        <input type="text" id="search-input" placeholder="Invoice number, member..." 
                               class="w-full pl-10 lg:pl-12 pr-4 py-2.5 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-lg lg:rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 placeholder-gray-400 text-sm lg:text-base">
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-xs lg:text-sm font-semibold text-gray-700">Date Range</label>
                    <input type="date" id="start-date" value="<?php echo $current_month . '-01'; ?>"
                           class="w-full px-3 lg:px-4 py-2.5 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-lg lg:rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm lg:text-base">
                </div>
                
                <div class="space-y-2">
                    <label class="block text-xs lg:text-sm font-semibold text-gray-700">To Date</label>
                    <input type="date" id="end-date" value="<?php echo $today; ?>"
                           class="w-full px-3 lg:px-4 py-2.5 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-lg lg:rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm lg:text-base">
                </div>
                
                <div class="space-y-2">
                    <label class="block text-xs lg:text-sm font-semibold text-gray-700">Payment Status</label>
                    <select id="status-filter" class="w-full px-3 lg:px-4 py-2.5 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-lg lg:rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm lg:text-base">
                        <option value="">All Status</option>
                        <option value="paid">Paid</option>
                        <option value="pending">Pending</option>
                        <option value="refunded">Refunded</option>
                    </select>
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
            </div>
        </div>

        <!-- Sales Table with Glassmorphism -->
        <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-xl border border-white/20 overflow-hidden">
            <div class="px-4 lg:px-6 py-4 lg:py-6 border-b border-gray-200/50 bg-gradient-to-r from-gray-50/50 to-gray-100/50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2 lg:space-x-3">
                        <div class="p-1.5 lg:p-2 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-lg">
                            <i class="fas fa-list text-white text-sm lg:text-base"></i>
                        </div>
                        <h3 class="text-lg lg:text-xl font-semibold text-gray-900">Sales Transactions</h3>
                    </div>
                    <div class="flex items-center space-x-1 lg:space-x-2 text-xs lg:text-sm text-gray-600">
                        <i class="fas fa-info-circle"></i>
                        <span id="sales-count">0 sales</span>
                    </div>
                </div>
            </div>
            
            <!-- Loading State -->
            <div id="loading-state" class="p-8 text-center">
                <div class="inline-flex items-center space-x-2">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                    <span class="text-gray-600">Loading sales...</span>
                </div>
            </div>
            
            <!-- Mobile Cards View -->
            <div class="block lg:hidden">
                <div id="sales-mobile" class="divide-y divide-gray-200/50">
                    <!-- Mobile cards will be loaded here -->
                </div>
            </div>
            
            <!-- Desktop Table View -->
            <div class="hidden lg:block overflow-x-auto">
                <table id="sales-table" class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50/80 to-gray-100/80">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Invoice</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Items</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Payment</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sales-tbody" class="bg-white/50 divide-y divide-gray-200/50">
                        <!-- Sales will be loaded here -->
                    </tbody>
                </table>
            </div>
            
            <!-- Empty State -->
            <div id="empty-state" class="hidden p-12 text-center">
                <div class="max-w-md mx-auto">
                    <div class="relative mb-6">
                        <div class="w-24 h-24 mx-auto bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-3xl text-gray-400"></i>
                        </div>
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-search text-white text-sm"></i>
                        </div>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No sales found</h3>
                    <p class="text-gray-500 mb-6">Try adjusting your search criteria or date range.</p>
                    <button onclick="clearFilters()" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <i class="fas fa-refresh mr-2"></i>Clear Filters
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sales Detail Modal -->
<div id="sale-detail-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-6">
        <div class="backdrop-blur-md bg-white/95 rounded-3xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto border border-white/20 m-6">
            <div class="p-8 border-b border-gray-200/50 bg-gradient-to-r from-gray-50/50 to-gray-100/50">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl">
                            <i class="fas fa-receipt text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900" id="modal-title">Sale Details</h3>
                            <p class="text-sm text-gray-600" id="modal-subtitle">Invoice #<span id="invoice-number"></span></p>
                        </div>
                    </div>
                    <button onclick="closeSaleModal()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all duration-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-8">
                <div id="sale-detail-content">
                    <!-- Sale details will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Refund Confirmation Modal -->
<div id="refund-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-6">
        <div class="backdrop-blur-md bg-white/95 rounded-3xl shadow-2xl max-w-md w-full border border-white/20 m-6">
            <div class="p-8 border-b border-gray-200/50 bg-gradient-to-r from-red-50/50 to-orange-50/50">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 bg-gradient-to-r from-red-500 to-orange-600 rounded-xl">
                            <i class="fas fa-undo text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">Refund Sale</h3>
                            <p class="text-sm text-gray-600">Invoice #<span id="refund-invoice-number"></span></p>
                        </div>
                    </div>
                    <button onclick="closeRefundModal()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all duration-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-8">
                <div class="mb-6">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-600 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-yellow-800">Warning</h4>
                                <p class="text-sm text-yellow-700 mt-1">
                                    This action will refund the sale and restore inventory. This cannot be undone.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Refund Reason *</label>
                            <textarea id="refund-reason" rows="3" placeholder="Enter the reason for this refund..." 
                                class="w-full px-4 py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200 placeholder-gray-400 text-sm resize-none"></textarea>
                        </div>
                        
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Refund Details</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Amount:</span>
                                    <span class="font-semibold text-gray-900" id="refund-total-amount">RM 0.00</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Items:</span>
                                    <span class="font-semibold text-gray-900" id="refund-item-count">0 items</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Inventory:</span>
                                    <span class="font-semibold text-green-600">Will be restored</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex space-x-3">
                    <button onclick="closeRefundModal()" class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors font-medium">
                        Cancel
                    </button>
                    <button onclick="confirmRefund()" id="confirm-refund-btn" class="flex-1 px-6 py-3 bg-gradient-to-r from-red-600 to-orange-600 text-white rounded-xl hover:from-red-700 hover:to-orange-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 font-medium">
                        <i class="fas fa-undo mr-2"></i>Confirm Refund
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom DataTable Styling */
.dataTables_wrapper .dataTables_filter {
    position: relative;
}

.dataTables_wrapper .dataTables_filter input {
    padding-left: 3rem !important;
    background: rgba(255, 255, 255, 0.8) !important;
    backdrop-filter: blur(8px) !important;
    border: 1px solid rgba(229, 231, 235, 1) !important;
    border-radius: 0.75rem !important;
    transition: all 0.2s ease !important;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06) !important;
}

.dataTables_wrapper .dataTables_filter input:focus {
    outline: none !important;
    border-color: transparent !important;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5) !important;
    background: rgba(255, 255, 255, 0.95) !important;
}

.dataTables_wrapper .dataTables_length select {
    background: rgba(255, 255, 255, 0.8) !important;
    backdrop-filter: blur(8px) !important;
    border: 1px solid rgba(229, 231, 235, 1) !important;
    border-radius: 0.75rem !important;
    transition: all 0.2s ease !important;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06) !important;
}

.dataTables_wrapper .dataTables_length select:focus {
    outline: none !important;
    border-color: transparent !important;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5) !important;
    background: rgba(255, 255, 255, 0.95) !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    background: rgba(255, 255, 255, 0.8) !important;
    backdrop-filter: blur(8px) !important;
    border: 1px solid rgba(229, 231, 235, 1) !important;
    border-radius: 0.5rem !important;
    transition: all 0.2s ease !important;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06) !important;
    margin: 0 0.25rem !important;
    padding: 0.5rem 0.75rem !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: rgba(239, 246, 255, 1) !important;
    border-color: rgba(147, 197, 253, 1) !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8) !important;
    color: white !important;
    border-color: #3b82f6 !important;
    box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3), 0 2px 4px -1px rgba(59, 130, 246, 0.2) !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: linear-gradient(135deg, #1d4ed8, #1e40af) !important;
    transform: translateY(-1px) !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
    background: rgba(243, 244, 246, 1) !important;
    color: rgba(156, 163, 175, 1) !important;
    border-color: rgba(243, 244, 246, 1) !important;
    cursor: not-allowed !important;
    transform: none !important;
    box-shadow: none !important;
}

.dataTables_wrapper .dataTables_info {
    color: rgba(75, 85, 99, 1) !important;
    font-weight: 500 !important;
    font-size: 0.875rem !important;
}

.dataTables_wrapper .dataTables_length label {
    color: rgba(55, 65, 81, 1) !important;
    font-weight: 500 !important;
    font-size: 0.875rem !important;
}

/* Search icon positioning */
.dataTables_wrapper .dataTables_filter::before {
    content: '\f002';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(156, 163, 175, 1);
    z-index: 10;
    pointer-events: none;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_length {
        margin-bottom: 1rem;
    }
    
    .dataTables_wrapper .dataTables_paginate {
        margin-top: 1rem;
    }
    
    /* Mobile-specific improvements */
    .main-content {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
    
    /* Mobile card improvements */
    #sales-mobile .p-4 {
        padding: 1rem;
    }
    
    /* Mobile button improvements */
    #sales-mobile button {
        min-height: 44px; /* Better touch targets */
    }
    
    /* Mobile modal improvements */
    #sale-detail-modal .backdrop-blur-md {
        max-width: 95vw;
        margin: 1rem;
    }
    
    #sale-detail-modal .p-8 {
        padding: 1rem;
    }
}

/* Add top, left, and right margin to DataTable wrapper */
.dataTables_wrapper {
    margin-top: 0.75rem !important;
    margin-left: 0.75rem !important;
    margin-right: 0.75rem !important;
}
</style>

<script>
$(document).ready(function() {
    loadSales();
    
    // Initialize DataTable with modern styling
    $('#sales-table').DataTable({
        pageLength: 25,
        order: [[6, 'desc']], // Sort by date descending
        responsive: true,
        language: {
            search: "",
            searchPlaceholder: "Search sales...",
            lengthMenu: "Show _MENU_ sales per page",
            info: "Showing _START_ to _END_ of _TOTAL_ sales",
            paginate: {
                first: '<i class="fas fa-angle-double-left"></i>',
                previous: '<i class="fas fa-angle-left"></i>',
                next: '<i class="fas fa-angle-right"></i>',
                last: '<i class="fas fa-angle-double-right"></i>'
            }
        },
        dom: '<"flex flex-col sm:flex-row justify-between items-center mb-6"lf>rt<"flex flex-col sm:flex-row justify-between items-center mt-6"ip>',
        initComplete: function() {
            // Add custom styling to DataTable elements
            $('.dataTables_filter input').addClass('px-4 py-3 pl-12 pr-4 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 placeholder-gray-400');
            $('.dataTables_length select').addClass('px-4 py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200');
            
            // Ensure proper positioning for search icon
            $('.dataTables_filter').addClass('relative');
            
            // Style pagination buttons
            $('.dataTables_paginate .paginate_button').addClass('px-3 py-2 mx-1 rounded-lg border border-gray-200 bg-white/80 backdrop-blur-sm hover:bg-blue-50 hover:border-blue-200 transition-all duration-200');
            $('.dataTables_paginate .paginate_button.current').addClass('bg-blue-600 text-white border-blue-600 hover:bg-blue-700');
            $('.dataTables_paginate .paginate_button.disabled').addClass('bg-gray-100 text-gray-400 border-gray-100 cursor-not-allowed');
            
            // Style info text
            $('.dataTables_info').addClass('text-sm text-gray-600 font-medium');
            
            // Style length menu
            $('.dataTables_length label').addClass('text-sm font-medium text-gray-700');
        }
    });
});

// Load sales
function loadSales() {
    $('#loading-state').removeClass('hidden');
    $('#empty-state').addClass('hidden');
    
    $.ajax({
        url: 'ajax/get-sales-list.php',
        type: 'GET',
        data: {
            search: $('#search-input').val(),
            start_date: $('#start-date').val(),
            end_date: $('#end-date').val(),
            status: $('#status-filter').val()
        },
        success: function(response) {
            $('#loading-state').addClass('hidden');
            $('#sales-tbody').html(response);
            
            // Update sales count
            const salesCount = $('#sales-tbody tr').not('.no-data').length;
            $('#sales-count').text(salesCount + ' sales');
            
            // Check if table is empty
            if ($('#sales-tbody tr').length === 0 || $('#sales-tbody tr').hasClass('no-data')) {
                $('#empty-state').removeClass('hidden');
            }
            
            // Update mobile view
            updateMobileView();
        },
        error: function() {
            $('#loading-state').addClass('hidden');
            showAlert('Error loading sales', 'error');
        }
    });
}

// Update mobile view
function updateMobileView() {
    const mobileContainer = $('#sales-mobile');
    mobileContainer.empty();
    
    // Check if we have mobile cards
    const mobileCards = $('.mobile-card');
    const emptyState = $('#mobile-empty-state');
    
    if (mobileCards.length > 0) {
        mobileCards.each(function() {
            const cardContent = $(this).html();
            mobileContainer.append(cardContent);
        });
    } else if (emptyState.length > 0) {
        const emptyContent = emptyState.html();
        mobileContainer.html(emptyContent);
    } else {
        mobileContainer.html('<div class="p-8 text-center text-gray-500">No sales found</div>');
    }
}

// View sale details
function viewSaleDetails(id) {
    $.ajax({
        url: 'ajax/get-sale-details.php',
        type: 'GET',
        data: { id: id },
        success: function(response) {
            try {
                const data = JSON.parse(response);
                
                if (data.error) {
                    showAlert(data.error, 'error');
                    return;
                }
                
                $('#invoice-number').text(data.sale.invoice_number);
                $('#sale-detail-content').html(data.html);
                $('#sale-detail-modal').removeClass('hidden');
                $('body').addClass('overflow-hidden');
            } catch (e) {
                showAlert('Error loading sale details', 'error');
            }
        },
        error: function() {
            showAlert('Error loading sale details', 'error');
        }
    });
}

// Close sale modal
function closeSaleModal() {
    $('#sale-detail-modal').addClass('hidden');
    $('body').removeClass('overflow-hidden');
}

// Print receipt
function printReceipt(id) {
    window.open('print-receipt.php?id=' + id, '_blank');
}

// Export sales
function exportSales() {
    const search = $('#search-input').val();
    const startDate = $('#start-date').val();
    const endDate = $('#end-date').val();
    const status = $('#status-filter').val();
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (startDate) params.append('start_date', startDate);
    if (endDate) params.append('end_date', endDate);
    if (status) params.append('status', status);
    
    window.open('ajax/export-sales.php?' + params.toString(), '_blank');
}

// Print sales
function printSales() {
    const search = $('#search-input').val();
    const startDate = $('#start-date').val();
    const endDate = $('#end-date').val();
    const status = $('#status-filter').val();
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (startDate) params.append('start_date', startDate);
    if (endDate) params.append('end_date', endDate);
    if (status) params.append('status', status);
    
    window.open('ajax/print-sales.php?' + params.toString(), '_blank');
}

// Clear filters
function clearFilters() {
    $('#search-input').val('');
    $('#start-date').val('<?php echo $current_month . '-01'; ?>');
    $('#end-date').val('<?php echo $today; ?>');
    $('#status-filter').val('');
    loadSales();
}

// Set date range for quick filters
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
    }
    
    $('#start-date').val(startDate);
    $('#end-date').val(endDate);
    loadSales();
}

// Search and filter with debouncing
let searchTimeout;
$('#search-input, #start-date, #end-date, #status-filter').on('change keyup', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
        loadSales();
    }, 300);
});

// Close modal when clicking outside
$(document).on('click', '#sale-detail-modal', function(e) {
    if (e.target === this) {
        closeSaleModal();
    }
});

// Close modal with Escape key
$(document).on('keydown', function(e) {
    if (e.key === 'Escape' && !$('#sale-detail-modal').hasClass('hidden')) {
        closeSaleModal();
    }
    if (e.key === 'Escape' && !$('#refund-modal').hasClass('hidden')) {
        closeRefundModal();
    }
});

// Refund functionality
let currentRefundSaleId = null;

function refundSale(saleId) {
    currentRefundSaleId = saleId;
    
    // Get sale details for the modal
    $.ajax({
        url: 'ajax/get-sale-details.php',
        type: 'GET',
        dataType: 'json',
        data: { id: saleId },
        success: function(response) {
            try {
                // Check if response is already an object (jQuery sometimes auto-parses JSON)
                let data;
                if (typeof response === 'string') {
                    data = JSON.parse(response);
                } else {
                    data = response;
                }
                
                if (data.error) {
                    showAlert(data.error, 'error');
                    return;
                }
                
                // Populate refund modal with sale details
                $('#refund-invoice-number').text(data.sale.invoice_number);
                $('#refund-total-amount').text('RM ' + parseFloat(data.sale.total_amount).toFixed(2));
                $('#refund-item-count').text((data.sale.item_count || 0) + ' items');
                
                // Clear previous reason
                $('#refund-reason').val('');
                
                // Show modal
                $('#refund-modal').removeClass('hidden');
                $('body').addClass('overflow-hidden');
                
            } catch (e) {
                showAlert('Error loading sale details for refund: ' + e.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            showAlert('Error loading sale details for refund: ' + error, 'error');
        }
    });
}

function closeRefundModal() {
    $('#refund-modal').addClass('hidden');
    $('body').removeClass('overflow-hidden');
    currentRefundSaleId = null;
    $('#refund-reason').val('');
}

function confirmRefund() {
    const reason = $('#refund-reason').val().trim();
    
    if (!reason) {
        showAlert('Please enter a refund reason', 'error');
        $('#refund-reason').focus();
        return;
    }
    
    if (!currentRefundSaleId) {
        showAlert('Invalid sale ID', 'error');
        return;
    }
    
    // Disable button to prevent double submission
    const btn = $('#confirm-refund-btn');
    const originalText = btn.html();
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Processing...');
    
    $.ajax({
        url: 'ajax/refund-sale.php',
        type: 'POST',
        dataType: 'json',
        data: {
            sale_id: currentRefundSaleId,
            refund_reason: reason
        },
        success: function(response) {
            try {
                // Check if response is already an object (jQuery sometimes auto-parses JSON)
                let data;
                if (typeof response === 'string') {
                    data = JSON.parse(response);
                } else {
                    data = response;
                }
                
                if (data.success) {
                    showAlert('Sale refunded successfully', 'success');
                    closeRefundModal();
                    loadSales(); // Refresh the sales list
                } else {
                    showAlert(data.message || 'Failed to refund sale', 'error');
                }
            } catch (e) {
                showAlert('Error processing refund', 'error');
            }
        },
        error: function() {
            showAlert('Error processing refund', 'error');
        },
        complete: function() {
            // Re-enable button
            btn.prop('disabled', false).html(originalText);
        }
    });
}

// Close refund modal when clicking outside
$(document).on('click', '#refund-modal', function(e) {
    if (e.target === this) {
        closeRefundModal();
    }
});
</script>

<?php include 'include/footer.php'; ?>


