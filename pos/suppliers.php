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
                            <i class="fas fa-truck text-white text-xl"></i>
                        </div>
                        <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                            Suppliers Management
                        </h1>
                    </div>
                    <p class="text-gray-600 text-lg">Manage your product suppliers efficiently</p>
                </div>
                <button onclick="openAddModal()" class="group relative inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <div class="absolute inset-0 bg-white/20 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <i class="fas fa-plus mr-2 relative z-10"></i>
                    <span class="relative z-10 font-medium">Add Supplier</span>
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6 lg:mb-8">
            <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-lg border border-white/20 p-4 lg:p-6 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs lg:text-sm font-medium text-gray-600">Total Suppliers</p>
                        <p class="text-lg lg:text-2xl font-bold text-gray-900" id="total-suppliers">-</p>
                    </div>
                    <div class="p-2 lg:p-3 bg-green-100 rounded-lg lg:rounded-xl">
                        <i class="fas fa-truck text-green-600 text-sm lg:text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-lg border border-white/20 p-4 lg:p-6 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs lg:text-sm font-medium text-gray-600">Active</p>
                        <p class="text-lg lg:text-2xl font-bold text-green-600" id="active-suppliers">-</p>
                    </div>
                    <div class="p-2 lg:p-3 bg-blue-100 rounded-lg lg:rounded-xl">
                        <i class="fas fa-check-circle text-blue-600 text-sm lg:text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-lg border border-white/20 p-4 lg:p-6 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs lg:text-sm font-medium text-gray-600">Inactive</p>
                        <p class="text-lg lg:text-2xl font-bold text-yellow-600" id="inactive-suppliers">-</p>
                    </div>
                    <div class="p-2 lg:p-3 bg-yellow-100 rounded-lg lg:rounded-xl">
                        <i class="fas fa-pause-circle text-yellow-600 text-sm lg:text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-lg border border-white/20 p-4 lg:p-6 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs lg:text-sm font-medium text-gray-600">Products</p>
                        <p class="text-lg lg:text-2xl font-bold text-purple-600" id="total-products">-</p>
                    </div>
                    <div class="p-2 lg:p-3 bg-purple-100 rounded-lg lg:rounded-xl">
                        <i class="fas fa-boxes text-purple-600 text-sm lg:text-xl"></i>
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
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
                <div class="space-y-2">
                    <label class="block text-xs lg:text-sm font-semibold text-gray-700">Search Suppliers</label>
                    <div class="relative group">
                        <i class="fas fa-search absolute left-3 lg:left-4 top-1/2 transform -translate-y-1/2 text-gray-400 group-focus-within:text-green-500 transition-colors duration-200"></i>
                        <input type="text" id="search-input" placeholder="Search suppliers..." 
                               class="w-full pl-10 lg:pl-12 pr-4 py-2.5 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-lg lg:rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 placeholder-gray-400 text-sm lg:text-base">
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-xs lg:text-sm font-semibold text-gray-700">Status</label>
                    <select id="status-filter" class="w-full px-3 lg:px-4 py-2.5 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-lg lg:rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 text-sm lg:text-base">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-xs lg:text-sm font-semibold text-gray-700">Sort By</label>
                    <select id="sort-filter" class="w-full px-3 lg:px-4 py-2.5 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-lg lg:rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 text-sm lg:text-base">
                        <option value="name">Name</option>
                        <option value="products_count">Products Count</option>
                        <option value="created_at">Date Added</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Suppliers Table with Glassmorphism -->
        <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-xl border border-white/20 overflow-hidden">
            <div class="px-4 lg:px-6 py-4 lg:py-6 border-b border-gray-200/50 bg-gradient-to-r from-gray-50/50 to-gray-100/50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2 lg:space-x-3">
                        <div class="p-1.5 lg:p-2 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-lg">
                            <i class="fas fa-list text-white text-sm lg:text-base"></i>
                        </div>
                        <h3 class="text-lg lg:text-xl font-semibold text-gray-900">Suppliers List</h3>
                    </div>
                    <div class="flex items-center space-x-1 lg:space-x-2 text-xs lg:text-sm text-gray-600">
                        <i class="fas fa-info-circle"></i>
                        <span id="suppliers-count">0 suppliers</span>
                    </div>
                </div>
            </div>
            
            <!-- Mobile Cards View -->
            <div class="block lg:hidden">
                <div id="suppliers-mobile" class="divide-y divide-gray-200/50">
                    <!-- Mobile cards will be loaded here -->
                </div>
            </div>
            
            <!-- Desktop Table View -->
            <div class="hidden lg:block overflow-x-auto">
                <table id="suppliers-table" class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50/80 to-gray-100/80">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Supplier</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Contact Information</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Address</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Products</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="suppliers-tbody" class="bg-white/50 divide-y divide-gray-200/50">
                        <!-- Suppliers will be loaded here -->
                    </tbody>
                </table>
            </div>
            
            <!-- Empty State -->
            <div id="empty-state" class="hidden p-12 text-center">
                <div class="max-w-md mx-auto">
                    <div class="relative mb-6">
                        <div class="w-24 h-24 mx-auto bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-truck text-3xl text-gray-400"></i>
                        </div>
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-plus text-white text-sm"></i>
                        </div>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No suppliers found</h3>
                    <p class="text-gray-500 mb-6">Get started by adding your first supplier to the system.</p>
                    <button onclick="openAddModal()" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <i class="fas fa-plus mr-2"></i>Add Your First Supplier
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Supplier Modal with Glassmorphism -->
<div id="supplier-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="backdrop-blur-md bg-white/95 rounded-3xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto border border-white/20">
            <div class="p-8 border-b border-gray-200/50 bg-gradient-to-r from-gray-50/50 to-gray-100/50">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl">
                            <i class="fas fa-truck text-white text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900" id="modal-title">Add New Supplier</h3>
                    </div>
                    <button onclick="closeModal()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all duration-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-8">
                <form id="supplier-form" class="space-y-8">
                    <input type="hidden" id="supplier-id" name="id">
                    
                    <!-- Basic Information -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="p-2 bg-green-100 rounded-lg">
                                <i class="fas fa-info-circle text-green-600"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900">Basic Information</h4>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Supplier Name *</label>
                                <input type="text" id="name" name="name" required 
                                       class="w-full px-4 py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200">
                            </div>
                            
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Contact Person</label>
                                <input type="text" id="contact_person" name="contact_person" 
                                       class="w-full px-4 py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact Information -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <i class="fas fa-phone text-blue-600"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900">Contact Information</h4>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Phone</label>
                                <div class="relative">
                                    <i class="fas fa-phone absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    <input type="text" id="phone" name="phone" 
                                           class="w-full pl-12 pr-4 py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200">
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Email</label>
                                <div class="relative">
                                    <i class="fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    <input type="email" id="email" name="email" 
                                           class="w-full pl-12 pr-4 py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Address -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="p-2 bg-purple-100 rounded-lg">
                                <i class="fas fa-map-marker-alt text-purple-600"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900">Address Information</h4>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Address</label>
                            <textarea id="address" name="address" rows="4" 
                                      class="w-full px-4 py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 resize-none"></textarea>
                        </div>
                    </div>
                    
                    <!-- Status -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="p-2 bg-yellow-100 rounded-lg">
                                <i class="fas fa-toggle-on text-yellow-600"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900">Status</h4>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" id="is_active" name="is_active" value="1" checked 
                                   class="w-5 h-5 rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                            <label for="is_active" class="text-sm font-medium text-gray-700">Active Supplier</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="p-8 border-t border-gray-200/50 bg-gradient-to-r from-gray-50/50 to-gray-100/50 flex justify-end space-x-4">
                <button onclick="closeModal()" 
                        class="px-6 py-3 text-gray-600 border border-gray-300 rounded-xl hover:bg-gray-50 transition-all duration-200 font-medium">
                    Cancel
                </button>
                <button onclick="saveSupplier()" 
                        class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 font-medium">
                    Save Supplier
                </button>
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
    #suppliers-mobile .p-4 {
        padding: 1rem;
    }
    
    /* Mobile button improvements */
    #suppliers-mobile button {
        min-height: 44px; /* Better touch targets */
    }
    
    /* Mobile modal improvements */
    #supplier-modal .backdrop-blur-md {
        max-width: 95vw;
        margin: 1rem;
    }
    
    #supplier-modal .p-8 {
        padding: 1rem;
    }
    
    /* Mobile form improvements */
    #supplier-modal input,
    #supplier-modal select,
    #supplier-modal textarea {
        font-size: 16px; /* Prevents zoom on iOS */
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
    loadSuppliers();
    
    // Initialize DataTable with modern styling
    $('#suppliers-table').DataTable({
        pageLength: 25,
        order: [[0, 'asc']],
        responsive: true,
        language: {
            search: "",
            searchPlaceholder: "Search suppliers...",
            lengthMenu: "Show _MENU_ suppliers per page",
            info: "Showing _START_ to _END_ of _TOTAL_ suppliers",
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
});

// Load suppliers
function loadSuppliers() {
    $('#empty-state').addClass('hidden');
    
    $.ajax({
        url: 'ajax/get-suppliers-list.php',
        type: 'GET',
        success: function(response) {
            $('#suppliers-tbody').html(response);
            
            // Update supplier count
            const supplierCount = $('#suppliers-tbody tr').not('.no-data').length;
            $('#suppliers-count').text(supplierCount + ' suppliers');
            
            // Check if table is empty
            if ($('#suppliers-tbody tr').length === 0 || $('#suppliers-tbody tr').hasClass('no-data')) {
                $('#empty-state').removeClass('hidden');
            }
            
            // Update mobile view
            updateMobileView();
            
            // Update stats
            updateStats();
        },
        error: function() {
            showAlert('Error loading suppliers', 'error');
        }
    });
}

// Update mobile view
function updateMobileView() {
    const mobileContainer = $('#suppliers-mobile');
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
        mobileContainer.html('<div class="p-8 text-center text-gray-500">No suppliers found</div>');
    }
}

// Update statistics
function updateStats() {
    // Show loading state
    $('#total-suppliers, #active-suppliers, #inactive-suppliers, #total-products').html('<div class="animate-pulse bg-gray-200 h-6 rounded"></div>');
    
    $.ajax({
        url: 'ajax/get-suppliers-stats.php',
        type: 'GET',
        dataType: 'json',
        success: function(stats) {
            // Check if there's an error in the response
            if (stats.error) {
                console.error('Stats error:', stats.error);
                // Fallback to calculating from table
                const rows = $('#suppliers-tbody tr').not('.no-data');
                const total = rows.length;
                $('#total-suppliers').text(total);
                $('#active-suppliers, #inactive-suppliers, #total-products').text('0');
                return;
            }
            
            $('#total-suppliers').text(stats.total_suppliers);
            $('#active-suppliers').text(stats.active_suppliers);
            $('#inactive-suppliers').text(stats.inactive_suppliers);
            $('#total-products').text(stats.total_products);
        },
        error: function(xhr, status, error) {
            console.error('Error loading supplier statistics:', status, error);
            // Fallback to calculating from table
            const rows = $('#suppliers-tbody tr').not('.no-data');
            const total = rows.length;
            $('#total-suppliers').text(total);
            $('#active-suppliers, #inactive-suppliers, #total-products').text('0');
        }
    });
}

// Modal functions
function openAddModal() {
    $('#modal-title').text('Add New Supplier');
    $('#supplier-form')[0].reset();
    $('#supplier-id').val('');
    $('#supplier-modal').removeClass('hidden');
    $('body').addClass('overflow-hidden');
}

function openEditModal(id) {
    $('#modal-title').text('Edit Supplier');
    
    $.ajax({
        url: 'ajax/get-supplier.php',
        type: 'GET',
        data: { id: id },
        success: function(response) {
            try {
                const supplier = JSON.parse(response);
                
                if (supplier.error) {
                    showAlert(supplier.error, 'error');
                    return;
                }
                
                $('#supplier-id').val(supplier.id);
                $('#name').val(supplier.name);
                $('#contact_person').val(supplier.contact_person || '');
                $('#phone').val(supplier.phone || '');
                $('#email').val(supplier.email || '');
                $('#address').val(supplier.address || '');
                $('#is_active').prop('checked', supplier.is_active == 1);
                
                $('#supplier-modal').removeClass('hidden');
                $('body').addClass('overflow-hidden');
            } catch (e) {
                showAlert('Error loading supplier data', 'error');
            }
        },
        error: function() {
            showAlert('Error loading supplier data', 'error');
        }
    });
}

function closeModal() {
    $('#supplier-modal').addClass('hidden');
    $('body').removeClass('overflow-hidden');
}

// Save supplier
function saveSupplier() {
    const formData = new FormData($('#supplier-form')[0]);
    const supplierId = $('#supplier-id').val();
    
    // Determine if this is an edit or add operation
    const url = supplierId ? 'ajax/edit-supplier.php' : 'ajax/save-supplier.php';
    
    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            const data = JSON.parse(response);
            if (data.success) {
                closeModal();
                loadSuppliers();
                showAlert(data.message, 'success');
            } else {
                showAlert(data.message, 'error');
            }
        },
        error: function() {
            showAlert('Error saving supplier', 'error');
        }
    });
}

// Delete supplier
function deleteSupplier(id) {
    confirmDelete('ajax/delete-supplier.php?id=' + id, 'Are you sure you want to delete this supplier?');
}

// Search and filter with debouncing
let searchTimeout;
$('#search-input, #status-filter, #sort-filter').on('change keyup', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
        const search = $('#search-input').val();
        const status = $('#status-filter').val();
        const sort = $('#sort-filter').val();
        
        $.ajax({
            url: 'ajax/filter-suppliers.php',
            type: 'GET',
            data: { search, status, sort },
            success: function(response) {
                $('#suppliers-tbody').html(response);
                
                // Update supplier count
                const supplierCount = $('#suppliers-tbody tr').not('.no-data').length;
                $('#suppliers-count').text(supplierCount + ' suppliers');
                
                // Check if table is empty
                if ($('#suppliers-tbody tr').length === 0 || $('#suppliers-tbody tr').hasClass('no-data')) {
                    $('#empty-state').removeClass('hidden');
                } else {
                    $('#empty-state').addClass('hidden');
                }
                
                // Update mobile view
                updateMobileView();
                
                // Update stats after filtering
                updateStats();
            }
        });
    }, 300);
});

// Close modal when clicking outside
$(document).on('click', '#supplier-modal', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Close modal with Escape key
$(document).on('keydown', function(e) {
    if (e.key === 'Escape' && !$('#supplier-modal').hasClass('hidden')) {
        closeModal();
    }
});
</script>

<?php include 'include/footer.php'; ?>
