<?php
include 'include/conn.php';
include 'include/session.php';
include 'include/head.php';
include 'include/header.php';
include 'include/sidebar.php';
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
                            <i class="fas fa-clipboard-check text-white text-xl"></i>
                        </div>
                        <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                            Stock Take Management
                        </h1>
                    </div>
                    <p class="text-gray-600 text-lg">Physically count and reconcile your inventory with system records</p>
                </div>
                <button onclick="openNewSessionModal()" class="group relative inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <div class="absolute inset-0 bg-white/20 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <i class="fas fa-plus mr-2 relative z-10"></i>
                    <span class="relative z-10 font-medium">New Stock Take Session</span>
                </button>
            </div>
        </div>

        <!-- Stock Take Process Overview -->
        <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-xl border border-white/20 p-6 mb-8">
            <div class="flex items-center space-x-3 mb-6">
                <div class="p-2 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg">
                    <i class="fas fa-info-circle text-white"></i>
                </div>
                <h3 class="text-lg lg:text-xl font-semibold text-gray-900">Stock Take Process</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border border-blue-200">
                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-clipboard-list text-white"></i>
                    </div>
                    <h4 class="font-semibold text-blue-900 mb-1">1. Preparation</h4>
                    <p class="text-sm text-blue-700">Freeze stock movement and organize items</p>
                </div>
                
                <div class="text-center p-4 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl border border-yellow-200">
                    <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-search text-white"></i>
                    </div>
                    <h4 class="font-semibold text-yellow-900 mb-1">2. Counting</h4>
                    <p class="text-sm text-yellow-700">Physically count each item</p>
                </div>
                
                <div class="text-center p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-xl border border-green-200">
                    <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-edit text-white"></i>
                    </div>
                    <h4 class="font-semibold text-green-900 mb-1">3. Recording</h4>
                    <p class="text-sm text-green-700">Enter counted quantities</p>
                </div>
                
                <div class="text-center p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl border border-purple-200">
                    <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-balance-scale text-white"></i>
                    </div>
                    <h4 class="font-semibold text-purple-900 mb-1">4. Reconciliation</h4>
                    <p class="text-sm text-purple-700">Compare counted vs system stock</p>
                </div>
                
                <div class="text-center p-4 bg-gradient-to-br from-red-50 to-red-100 rounded-xl border border-red-200">
                    <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-sync-alt text-white"></i>
                    </div>
                    <h4 class="font-semibold text-red-900 mb-1">5. Adjustment</h4>
                    <p class="text-sm text-red-700">Update system inventory</p>
                </div>
            </div>
        </div>

        <!-- Stock Take Sessions -->
        <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-xl border border-white/20 overflow-hidden mb-8">
            <div class="px-6 py-6 border-b border-gray-200/50 bg-gradient-to-r from-gray-50/50 to-gray-100/50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-lg">
                            <i class="fas fa-list text-white"></i>
                        </div>
                        <h3 class="text-lg lg:text-xl font-semibold text-gray-900">Stock Take Sessions</h3>
                    </div>
                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                        <i class="fas fa-info-circle"></i>
                        <span id="sessions-count">0 sessions</span>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table id="sessions-table" class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50/80 to-gray-100/80">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Session Name</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Start Date</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">End Date</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Items Counted</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Differences</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Created By</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sessions-tbody" class="bg-white/50 divide-y divide-gray-200/50">
                        <!-- Sessions will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Stock Take -->
        <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-xl border border-white/20 overflow-hidden">
            <div class="px-6 py-6 border-b border-gray-200/50 bg-gradient-to-r from-gray-50/50 to-gray-100/50">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="p-2 bg-gradient-to-r from-orange-500 to-red-500 rounded-lg">
                        <i class="fas fa-bolt text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg lg:text-xl font-semibold text-gray-900">Quick Stock Take</h3>
                        <p class="text-sm text-gray-600">Quickly adjust stock levels for individual products without creating a session</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                    <div class="space-y-2">
                        <label class="block text-xs lg:text-sm font-semibold text-gray-700">Search Product</label>
                        <div class="relative group">
                            <i class="fas fa-search absolute left-3 lg:left-4 top-1/2 transform -translate-y-1/2 text-gray-400 group-focus-within:text-green-500 transition-colors duration-200"></i>
                            <input type="text" id="product-search" placeholder="Search by SKU, barcode, or name..." 
                                   class="w-full pl-10 lg:pl-12 pr-4 py-2.5 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-lg lg:rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 placeholder-gray-400 text-sm lg:text-base">
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-xs lg:text-sm font-semibold text-gray-700">Category</label>
                        <select id="category-filter" class="w-full px-3 lg:px-4 py-2.5 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-lg lg:rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 text-sm lg:text-base">
                            <option value="">All Categories</option>
                        </select>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-xs lg:text-sm font-semibold text-gray-700">Stock Status</label>
                        <select id="stock-status-filter" class="w-full px-3 lg:px-4 py-2.5 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-lg lg:rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 text-sm lg:text-base">
                            <option value="">All</option>
                            <option value="in_stock">In Stock</option>
                            <option value="low_stock">Low Stock</option>
                            <option value="out_of_stock">Out of Stock</option>
                        </select>
                    </div>
                    
                    <div class="flex items-end">
                        <button onclick="loadProductsForStockTake()" class="w-full px-4 py-2.5 lg:py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg lg:rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i class="fas fa-search mr-2"></i>Search
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <div id="quick-stock-take-results" class="hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gradient-to-r from-gray-50/80 to-gray-100/80">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Product</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">SKU</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Current Stock</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Counted Stock</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Difference</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="quick-stock-take-tbody" class="bg-white/50 divide-y divide-gray-200/50">
                                <!-- Results will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Empty State for Quick Stock Take -->
                <div id="quick-empty-state" class="text-center py-12">
                    <div class="max-w-md mx-auto">
                        <div class="relative mb-6">
                            <div class="w-24 h-24 mx-auto bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                                <i class="fas fa-clipboard-list text-3xl text-gray-400"></i>
                            </div>
                            <div class="absolute -top-2 -right-2 w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-search text-white text-sm"></i>
                            </div>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Ready to count stock?</h3>
                        <p class="text-gray-500 mb-6">Search for products above to start your quick stock take or create a formal session for comprehensive inventory counting.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Session Modal with Glassmorphism -->
<div id="session-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-6">
        <div class="backdrop-blur-md bg-white/95 rounded-3xl shadow-2xl max-w-2xl w-full border border-white/20">
            <div class="p-8 border-b border-gray-200/50 bg-gradient-to-r from-gray-50/50 to-gray-100/50">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl">
                            <i class="fas fa-clipboard-check text-white text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">New Stock Take Session</h3>
                    </div>
                    <button onclick="closeSessionModal()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all duration-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-8">
                <form id="session-form" class="space-y-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Session Name *</label>
                        <input type="text" id="session-name" name="session_name" required 
                               class="w-full px-4 py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                               placeholder="e.g., Monthly Stock Take - January 2024">
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Notes</label>
                        <textarea id="session-notes" name="notes" rows="4" 
                                  class="w-full px-4 py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 resize-none"
                                  placeholder="Add any notes about this stock take session..."></textarea>
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                            <div>
                                <h4 class="font-semibold text-blue-900 mb-1">Stock Take Session Guidelines</h4>
                                <ul class="text-sm text-blue-800 space-y-1">
                                    <li>• Freeze stock movement during counting</li>
                                    <li>• Count all items systematically</li>
                                    <li>• Record any discrepancies with notes</li>
                                    <li>• Complete the session to update inventory</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="p-8 border-t border-gray-200/50 bg-gradient-to-r from-gray-50/50 to-gray-100/50 flex justify-end space-x-4">
                <button onclick="closeSessionModal()" 
                        class="px-6 py-3 text-gray-600 border border-gray-300 rounded-xl hover:bg-gray-50 transition-all duration-200 font-medium">
                    Cancel
                </button>
                <button onclick="createSession()" 
                        class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 font-medium">
                    Create Session
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Stock Count Modal with Glassmorphism -->
<div id="stock-count-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-6">
        <div class="backdrop-blur-md bg-white/95 rounded-3xl shadow-2xl max-w-2xl w-full border border-white/20">
            <div class="p-8 border-b border-gray-200/50 bg-gradient-to-r from-gray-50/50 to-gray-100/50">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl">
                            <i class="fas fa-edit text-white text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">Update Stock Count</h3>
                    </div>
                    <button onclick="closeStockCountModal()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all duration-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-8">
                <form id="stock-count-form" class="space-y-6">
                    <input type="hidden" id="product-id" name="product_id">
                    <input type="hidden" id="session-id" name="session_id">
                    
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-xl border border-blue-200">
                        <h4 class="font-semibold text-blue-900 mb-2" id="product-name"></h4>
                        <p class="text-sm text-blue-700" id="product-sku"></p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">System Stock</label>
                            <input type="number" id="current-stock" readonly 
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-600">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Counted Stock *</label>
                            <input type="number" id="counted-stock" name="counted_quantity" required min="0"
                                   class="w-full px-4 py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Difference</label>
                            <input type="number" id="stock-difference" readonly 
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl">
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Notes</label>
                        <textarea id="stock-notes" name="notes" rows="3" 
                                  class="w-full px-4 py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none"
                                  placeholder="Add notes about this count (e.g., damaged items, location, etc.)"></textarea>
                    </div>
                    
                    <div id="difference-alert" class="hidden">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                            <div class="flex items-start space-x-3">
                                <i class="fas fa-exclamation-triangle text-yellow-600 mt-1"></i>
                                <div>
                                    <h4 class="font-semibold text-yellow-900 mb-1">Stock Difference Detected</h4>
                                    <p class="text-sm text-yellow-800">There is a difference between system and counted stock. Please verify your count and add notes explaining the discrepancy.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="p-8 border-t border-gray-200/50 bg-gradient-to-r from-gray-50/50 to-gray-100/50 flex justify-end space-x-4">
                <button onclick="closeStockCountModal()" 
                        class="px-6 py-3 text-gray-600 border border-gray-300 rounded-xl hover:bg-gray-50 transition-all duration-200 font-medium">
                    Cancel
                </button>
                <button onclick="saveStockCount()" 
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 font-medium">
                    Save Count
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom DataTable Styling for Stock Take */
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
    box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.5) !important;
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
    box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.5) !important;
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
    background: rgba(240, 253, 244, 1) !important;
    border-color: rgba(187, 247, 208, 1) !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: linear-gradient(135deg, #22c55e, #16a34a) !important;
    color: white !important;
    border-color: #22c55e !important;
    box-shadow: 0 4px 6px -1px rgba(34, 197, 94, 0.3), 0 2px 4px -1px rgba(34, 197, 94, 0.2) !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: linear-gradient(135deg, #16a34a, #15803d) !important;
    transform: translateY(-1px) !important;
}

/* Status badges */
.status-badge {
    @apply px-3 py-1 rounded-full text-xs font-medium;
}

.status-in-progress {
    @apply bg-yellow-100 text-yellow-800;
}

.status-completed {
    @apply bg-green-100 text-green-800;
}

.status-cancelled {
    @apply bg-red-100 text-red-800;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .main-content {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
    
    #session-modal .backdrop-blur-md,
    #stock-count-modal .backdrop-blur-md {
        max-width: 95vw;
        margin: 1rem;
    }
    
    #session-modal .p-8,
    #stock-count-modal .p-8 {
        padding: 1rem;
    }
}
</style>

<script>
$(document).ready(function() {
    loadSessions();
    loadCategories();
    
    // Initialize DataTable
    $('#sessions-table').DataTable({
        pageLength: 25,
        order: [[1, 'desc']],
        responsive: true,
        language: {
            search: "",
            searchPlaceholder: "Search sessions...",
            lengthMenu: "Show _MENU_ sessions per page",
            info: "Showing _START_ to _END_ of _TOTAL_ sessions",
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
            $('.dataTables_filter input').addClass('px-4 py-3 pl-12 pr-4 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 placeholder-gray-400');
            $('.dataTables_length select').addClass('px-4 py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200');
            
            // Ensure proper positioning for search icon
            $('.dataTables_filter').addClass('relative');
            
            // Style pagination buttons
            $('.dataTables_paginate .paginate_button').addClass('px-3 py-2 mx-1 rounded-lg border border-gray-200 bg-white/80 backdrop-blur-sm hover:bg-green-50 hover:border-green-200 transition-all duration-200');
            $('.dataTables_paginate .paginate_button.current').addClass('bg-green-600 text-white border-green-600 hover:bg-green-700');
            $('.dataTables_paginate .paginate_button.disabled').addClass('bg-gray-100 text-gray-400 border-gray-100 cursor-not-allowed');
            
            // Style info text
            $('.dataTables_info').addClass('text-sm text-gray-600 font-medium');
            
            // Style length menu
            $('.dataTables_length label').addClass('text-sm font-medium text-gray-700');
        }
    });
    
    // Calculate difference when counted stock changes
    $('#counted-stock').on('input', function() {
        const current = parseInt($('#current-stock').val()) || 0;
        const counted = parseInt($(this).val()) || 0;
        const difference = counted - current;
        $('#stock-difference').val(difference);
        
        // Show/hide difference alert
        if (difference !== 0) {
            $('#difference-alert').removeClass('hidden');
        } else {
            $('#difference-alert').addClass('hidden');
        }
    });
});

// Load sessions
function loadSessions() {
    $.ajax({
        url: 'ajax/get-stock-take-sessions.php',
        type: 'GET',
        success: function(response) {
            $('#sessions-tbody').html(response);
            
            // Update session count
            const sessionCount = $('#sessions-tbody tr').not('.no-data').length;
            $('#sessions-count').text(sessionCount + ' sessions');
        },
        error: function() {
            showAlert('Error loading sessions', 'error');
        }
    });
}

// Load categories for filter
function loadCategories() {
    $.ajax({
        url: 'ajax/get-categories.php',
        type: 'GET',
        success: function(response) {
            $('#category-filter').html('<option value="">All Categories</option>' + response);
        }
    });
}

// Session modal functions
function openNewSessionModal() {
    $('#session-form')[0].reset();
    $('#session-modal').removeClass('hidden');
    $('body').addClass('overflow-hidden');
}

function closeSessionModal() {
    $('#session-modal').addClass('hidden');
    $('body').removeClass('overflow-hidden');
}

function createSession() {
    const formData = new FormData($('#session-form')[0]);
    
    $.ajax({
        url: 'ajax/create-stock-take-session.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            const data = JSON.parse(response);
            if (data.success) {
                closeSessionModal();
                loadSessions();
                showAlert(data.message, 'success');
            } else {
                showAlert(data.message, 'error');
            }
        },
        error: function() {
            showAlert('Error creating session', 'error');
        }
    });
}

// Stock count modal functions
function openStockCountModal(productId, sessionId, productName, sku, currentStock) {
    $('#product-id').val(productId);
    $('#session-id').val(sessionId);
    $('#product-name').text(productName);
    $('#product-sku').text('SKU: ' + sku);
    $('#current-stock').val(currentStock);
    $('#counted-stock').val(currentStock);
    $('#stock-difference').val(0);
    $('#stock-notes').val('');
    $('#difference-alert').addClass('hidden');
    
    $('#stock-count-modal').removeClass('hidden');
    $('body').addClass('overflow-hidden');
}

function closeStockCountModal() {
    $('#stock-count-modal').addClass('hidden');
    $('body').removeClass('overflow-hidden');
}

function saveStockCount() {
    const formData = new FormData($('#stock-count-form')[0]);
    
    $.ajax({
        url: 'ajax/save-stock-count.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            const data = JSON.parse(response);
            if (data.success) {
                closeStockCountModal();
                loadProductsForStockTake();
                showAlert(data.message, 'success');
            } else {
                showAlert(data.message, 'error');
            }
        },
        error: function() {
            showAlert('Error saving stock count', 'error');
        }
    });
}

// Load products for quick stock take
function loadProductsForStockTake() {
    const search = $('#product-search').val();
    const category = $('#category-filter').val();
    const stockStatus = $('#stock-status-filter').val();
    
    $.ajax({
        url: 'ajax/get-products-for-stock-take.php',
        type: 'GET',
        data: { search, category, stock_status: stockStatus },
        success: function(response) {
            $('#quick-stock-take-tbody').html(response);
            $('#quick-stock-take-results').removeClass('hidden');
            $('#quick-empty-state').addClass('hidden');
        },
        error: function() {
            showAlert('Error loading products', 'error');
        }
    });
}

// Session actions
function viewSession(id) {
    window.location.href = 'stock-take-session.php?id=' + id;
}

function completeSession(id) {
    if (confirm('Are you sure you want to complete this stock take session? This will update all stock levels based on the counted quantities.')) {
        $.ajax({
            url: 'ajax/complete-stock-take-session.php',
            type: 'POST',
            data: { id: id },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    loadSessions();
                    showAlert(data.message, 'success');
                } else {
                    showAlert(data.message, 'error');
                }
            },
            error: function() {
                showAlert('Error completing session', 'error');
            }
        });
    }
}

function deleteSession(id) {
    confirmDelete('ajax/delete-stock-take-session.php?id=' + id, 'Are you sure you want to delete this stock take session? This action cannot be undone.');
}

// Close modals when clicking outside
$(document).on('click', '#session-modal, #stock-count-modal', function(e) {
    if (e.target === this) {
        closeSessionModal();
        closeStockCountModal();
    }
});

// Close modals with Escape key
$(document).on('keydown', function(e) {
    if (e.key === 'Escape') {
        if (!$('#session-modal').hasClass('hidden')) {
            closeSessionModal();
        }
        if (!$('#stock-count-modal').hasClass('hidden')) {
            closeStockCountModal();
        }
    }
});
</script>

<?php include 'include/footer.php'; ?>
