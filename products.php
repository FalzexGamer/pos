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
                        <div class="p-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl">
                            <i class="fas fa-boxes text-white text-xl"></i>
                        </div>
                        <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                            Products Management
                        </h1>
                    </div>
                    <p class="text-gray-600 text-lg">Manage your inventory products efficiently</p>
                </div>
                <button onclick="openAddModal()" class="group relative inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <div class="absolute inset-0 bg-white/20 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <i class="fas fa-plus mr-2 relative z-10"></i>
                    <span class="relative z-10 font-medium">Add Product</span>
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6 lg:mb-8">
            <div class="stats-card backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-lg border border-white/20 p-4 lg:p-6 hover:shadow-xl transition-all duration-300 cursor-pointer" onclick="filterByStockStatus('all')">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs lg:text-sm font-medium text-gray-600">Total Products</p>
                        <p class="text-lg lg:text-2xl font-bold text-gray-900" id="total-products">-</p>
                    </div>
                    <div class="p-2 lg:p-3 bg-blue-100 rounded-lg lg:rounded-xl">
                        <i class="fas fa-box text-blue-600 text-sm lg:text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="stats-card backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-lg border border-white/20 p-4 lg:p-6 hover:shadow-xl transition-all duration-300 cursor-pointer group" onclick="filterByStockStatus('in_stock')">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs lg:text-sm font-medium text-gray-600">In Stock</p>
                        <p class="text-lg lg:text-2xl font-bold text-green-600" id="in-stock">-</p>
                        <div class="w-8 h-1 bg-green-500 rounded-full mt-1 group-hover:w-12 transition-all duration-300"></div>
                    </div>
                    <div class="p-2 lg:p-3 bg-green-100 rounded-lg lg:rounded-xl group-hover:bg-green-200 transition-colors duration-300">
                        <div class="relative">
                            <div class="w-6 h-6 lg:w-8 lg:h-8 bg-green-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white text-xs lg:text-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="stats-card backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-lg border border-white/20 p-4 lg:p-6 hover:shadow-xl transition-all duration-300 cursor-pointer group" onclick="filterByStockStatus('low_stock')">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs lg:text-sm font-medium text-gray-600">Low Stock</p>
                        <p class="text-lg lg:text-2xl font-bold text-yellow-600" id="low-stock">-</p>
                        <div class="w-8 h-1 bg-yellow-500 rounded-full mt-1 group-hover:w-12 transition-all duration-300"></div>
                    </div>
                    <div class="p-2 lg:p-3 bg-yellow-100 rounded-lg lg:rounded-xl group-hover:bg-yellow-200 transition-colors duration-300">
                        <div class="relative">
                            <div class="w-6 h-6 lg:w-8 lg:h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-white text-xs lg:text-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="stats-card backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-lg border border-white/20 p-4 lg:p-6 hover:shadow-xl transition-all duration-300 cursor-pointer group" onclick="filterByStockStatus('out_stock')">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs lg:text-sm font-medium text-gray-600">Out of Stock</p>
                        <p class="text-lg lg:text-2xl font-bold text-red-600" id="out-of-stock">-</p>
                        <div class="w-8 h-1 bg-red-500 rounded-full mt-1 group-hover:w-12 transition-all duration-300"></div>
                    </div>
                    <div class="p-2 lg:p-3 bg-red-100 rounded-lg lg:rounded-xl group-hover:bg-red-200 transition-colors duration-300">
                        <div class="relative">
                            <div class="w-6 h-6 lg:w-8 lg:h-8 bg-red-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-times text-white text-xs lg:text-sm"></i>
                            </div>
                        </div>
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
                    <label class="block text-xs lg:text-sm font-semibold text-gray-700">Search Products</label>
                    <div class="relative group">
                        <i class="fas fa-search absolute left-3 lg:left-4 top-1/2 transform -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 transition-colors duration-200"></i>
                        <input type="text" id="search-input" placeholder="Search products..." 
                               class="w-full pl-10 lg:pl-12 pr-4 py-2.5 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-lg lg:rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 placeholder-gray-400 text-sm lg:text-base">
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-xs lg:text-sm font-semibold text-gray-700">Status</label>
                    <select id="status-filter" class="w-full px-3 lg:px-4 py-2.5 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-lg lg:rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm lg:text-base">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-xs lg:text-sm font-semibold text-gray-700">Stock Status</label>
                    <select id="stock-status-filter" class="w-full px-3 lg:px-4 py-2.5 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-lg lg:rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm lg:text-base">
                        <option value="">All Stock Status</option>
                        <option value="in_stock">In Stock</option>
                        <option value="low_stock">Low Stock</option>
                        <option value="out_stock">Out of Stock</option>
                    </select>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-xs lg:text-sm font-semibold text-gray-700">Sort By</label>
                    <select id="sort-filter" class="w-full px-3 lg:px-4 py-2.5 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-lg lg:rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm lg:text-base">
                        <option value="name">Name</option>
                        <option value="sku">SKU</option>
                        <option value="stock_quantity">Stock Quantity</option>
                        <option value="created_at">Date Added</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Products Table with Glassmorphism -->
        <div class="backdrop-blur-sm bg-white/70 rounded-xl lg:rounded-2xl shadow-xl border border-white/20 overflow-hidden">
            <div class="px-4 lg:px-6 py-4 lg:py-6 border-b border-gray-200/50 bg-gradient-to-r from-gray-50/50 to-gray-100/50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2 lg:space-x-3">
                        <div class="p-1.5 lg:p-2 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-lg">
                            <i class="fas fa-list text-white text-sm lg:text-base"></i>
                        </div>
                        <h3 class="text-lg lg:text-xl font-semibold text-gray-900">Products List</h3>
                    </div>
                    <div class="flex items-center space-x-1 lg:space-x-2 text-xs lg:text-sm text-gray-600">
                        <i class="fas fa-info-circle"></i>
                        <span id="products-count">0 products</span>
                    </div>
                </div>
            </div>
            
            <!-- Mobile Cards View -->
            <div class="block lg:hidden">
                <div id="products-mobile" class="divide-y divide-gray-200/50">
                    <!-- Mobile cards will be loaded here -->
                </div>
            </div>
            
            <!-- Desktop Table View -->
            <div class="hidden lg:block overflow-x-auto">
                <table id="products-table" class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50/80 to-gray-100/80">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">SKU/Barcode</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Stock</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="products-tbody" class="bg-white/50 divide-y divide-gray-200/50">
                        <!-- Products will be loaded here -->
                    </tbody>
                </table>
            </div>
            

            
            <!-- Empty State -->
            <div id="empty-state" class="hidden p-12 text-center">
                <div class="max-w-md mx-auto">
                    <div class="relative mb-6">
                        <div class="w-24 h-24 mx-auto bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-box-open text-3xl text-gray-400"></i>
                        </div>
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-plus text-white text-sm"></i>
                        </div>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No products found</h3>
                    <p class="text-gray-500 mb-6">Get started by adding your first product to the inventory system.</p>
                    <button onclick="openAddModal()" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <i class="fas fa-plus mr-2"></i>Add Your First Product
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Product Modal with Glassmorphism -->
<div id="product-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-6">
        <div class="backdrop-blur-md bg-white/95 rounded-3xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto border border-white/20 m-6">
            <div class="p-8 border-b border-gray-200/50 bg-gradient-to-r from-gray-50/50 to-gray-100/50">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl">
                            <i class="fas fa-box text-white text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900" id="modal-title">Add New Product</h3>
                    </div>
                    <button onclick="closeModal()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all duration-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-8">
                <form id="product-form" class="space-y-8">
                    <input type="hidden" id="product-id" name="product_id">
                    
                    <!-- Basic Information -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <i class="fas fa-info-circle text-blue-600"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900">Basic Information</h4>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">SKU *</label>
                                <input type="text" id="sku" name="sku" required 
                                       class="w-full px-4 py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Barcode</label>
                                <input type="text" id="barcode" name="barcode" 
                                       class="w-full px-4 py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Product Name *</label>
                            <input type="text" id="name" name="name" required 
                                   class="w-full px-4 py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        </div>
                    </div>
                    
                    <!-- Categories & Suppliers -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="p-2 bg-green-100 rounded-lg">
                                <i class="fas fa-tags text-green-600"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900">Categories & Suppliers</h4>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Category *</label>
                                <select id="category_id" name="category_id" required 
                                        class="w-full px-4 py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                    <option value="">Select Category</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Supplier *</label>
                                <select id="supplier_id" name="supplier_id" required 
                                        class="w-full px-4 py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                    <option value="">Select Supplier</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">UOM *</label>
                                <select id="uom_id" name="uom_id" required 
                                        class="w-full px-4 py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                    <option value="">Select UOM</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pricing -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="p-2 bg-yellow-100 rounded-lg">
                                <i class="fas fa-dollar-sign text-yellow-600"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900">Pricing Information</h4>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Cost Price *</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">$</span>
                                    <input type="number" id="cost_price" name="cost_price" step="0.01" required 
                                           class="w-full pl-10 pr-4 py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Selling Price *</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">$</span>
                                    <input type="number" id="selling_price" name="selling_price" step="0.01" required 
                                           class="w-full pl-10 pr-4 py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Inventory -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="p-2 bg-purple-100 rounded-lg">
                                <i class="fas fa-warehouse text-purple-600"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900">Inventory Management</h4>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Stock Quantity</label>
                                <input type="number" id="stock_quantity" name="stock_quantity" value="0" 
                                       class="w-full px-4 py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Min Stock Level</label>
                                <input type="number" id="min_stock_level" name="min_stock_level" value="0" 
                                       class="w-full px-4 py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product Image -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="p-2 bg-purple-100 rounded-lg">
                                <i class="fas fa-image text-purple-600"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900">Product Image</h4>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Upload Image</label>
                                <div class="flex items-center justify-center w-full">
                                    <label for="product_image" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors duration-200">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                            <p class="mb-2 text-sm text-gray-500">
                                                <span class="font-semibold">Click to upload</span> or drag and drop
                                            </p>
                                            <p class="text-xs text-gray-500">PNG, JPG, JPEG (MAX. 2MB)</p>
                                        </div>
                                        <input id="product_image" name="product_image" type="file" class="hidden" accept="image/*" onchange="previewImage(this)">
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Image Preview -->
                            <div id="image-preview" class="hidden">
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Preview</label>
                                    <div class="relative inline-block">
                                        <img id="preview-img" src="" alt="Product Preview" class="w-32 h-32 object-cover rounded-lg border border-gray-200">
                                        <button type="button" onclick="removeImage()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 transition-colors">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Current Image Display (for editing) -->
                            <div id="current-image" class="hidden">
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Current Image</label>
                                    <div class="relative inline-block">
                                        <img id="current-img" src="" alt="Current Product" class="w-32 h-32 object-cover rounded-lg border border-gray-200">
                                        <button type="button" onclick="removeCurrentImage()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 transition-colors">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="p-2 bg-indigo-100 rounded-lg">
                                <i class="fas fa-align-left text-indigo-600"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900">Additional Information</h4>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Description</label>
                            <textarea id="description" name="description" rows="4" 
                                      class="w-full px-4 py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none"></textarea>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" id="is_active" name="is_active" value="1" checked 
                                   class="w-5 h-5 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <label for="is_active" class="text-sm font-medium text-gray-700">Active Product</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="p-8 border-t border-gray-200/50 bg-gradient-to-r from-gray-50/50 to-gray-100/50 flex justify-end space-x-4">
                <button onclick="closeModal()" 
                        class="px-6 py-3 text-gray-600 border border-gray-300 rounded-xl hover:bg-gray-50 transition-all duration-200 font-medium">
                    Cancel
                </button>
                <button onclick="saveProduct()" 
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 font-medium">
                    Save Product
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
    border-color: rgba(191, 219, 254, 1) !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
    color: white !important;
    border-color: #3b82f6 !important;
    box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3), 0 2px 4px -1px rgba(59, 130, 246, 0.2) !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
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
    #products-mobile .p-4 {
        padding: 1rem;
    }
    
    /* Mobile button improvements */
    #products-mobile button {
        min-height: 44px; /* Better touch targets */
    }
    
    /* Mobile modal improvements */
    #product-modal .backdrop-blur-md {
        max-width: 95vw;
        margin: 1rem;
    }
    
    #product-modal .p-8 {
        padding: 1rem;
    }
    
    /* Mobile form improvements */
    #product-modal input,
    #product-modal select,
    #product-modal textarea {
        font-size: 16px; /* Prevents zoom on iOS */
    }
}

/* Add top, left, and right margin to DataTable wrapper */
.dataTables_wrapper {
    margin-top: 0.75rem !important;
    margin-left: 0.75rem !important;
    margin-right: 0.75rem !important;
}

/* Stock status cards enhancements */
.stats-card {
    position: relative;
    overflow: hidden;
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.1), transparent);
    transform: translateX(-100%);
    transition: transform 0.6s ease;
}

.stats-card:hover::before {
    transform: translateX(100%);
}

.stats-card.ring-2 {
    transform: scale(1.02);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Stock status indicators */
.stock-indicator {
    position: relative;
    display: inline-block;
}

.stock-indicator::after {
    content: '';
    position: absolute;
    top: -2px;
    right: -2px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: currentColor;
    opacity: 0.8;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 0.8;
        transform: scale(1);
    }
    50% {
        opacity: 0.4;
        transform: scale(1.2);
    }
}
</style>

<script>
$(document).ready(function() {
    loadProducts();
    loadCategories();
    loadSuppliers();
    loadUOM();
    
    // Initialize stock status filter with "all" active
    $('.stats-card[onclick="filterByStockStatus(\'all\')"]').addClass('ring-2 ring-blue-500 bg-blue-50');
    
    // Initialize DataTable with modern styling
    $('#products-table').DataTable({
        pageLength: 25,
        order: [[0, 'asc']],
        responsive: true,
        language: {
            search: "",
            searchPlaceholder: "Search products...",
            lengthMenu: "Show _MENU_ products per page",
            info: "Showing _START_ to _END_ of _TOTAL_ products",
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

// Load products
function loadProducts() {
    $('#empty-state').addClass('hidden');
    
    $.ajax({
        url: 'ajax/get-products-list.php',
        type: 'GET',
        success: function(response) {
            $('#products-tbody').html(response);
            
            // Update product count
            const productCount = $('#products-tbody tr').not('.no-data').length;
            $('#products-count').text(productCount + ' products');
            
            // Check if table is empty
            if ($('#products-tbody tr').length === 0 || $('#products-tbody tr').hasClass('no-data')) {
                $('#empty-state').removeClass('hidden');
            }
            
            // Update mobile view
            updateMobileView();
            
            // Update stats
            updateStats();
        },
        error: function() {
            showAlert('Error loading products', 'error');
        }
    });
}

// Update mobile view
function updateMobileView() {
    const mobileContainer = $('#products-mobile');
    mobileContainer.empty();
    
    // Get mobile data from hidden div
    const mobileData = $('#mobile-data');
    
    if (mobileData.length > 0) {
        const mobileContent = mobileData.html();
        mobileContainer.html(mobileContent);
    } else {
        mobileContainer.html('<div class="p-8 text-center text-gray-500">No products found</div>');
    }
}

// Update statistics
function updateStats() {
    $.ajax({
        url: 'ajax/get-stock-stats.php',
        type: 'GET',
        success: function(response) {
            $('#total-products').text(response.total);
            $('#in-stock').text(response.in_stock);
            $('#low-stock').text(response.low_stock);
            $('#out-of-stock').text(response.out_stock);
        },
        error: function() {
            console.error('Error loading stock statistics');
        }
    });
}

// Load form data
function loadCategories() {
    $.ajax({
        url: 'ajax/get-categories.php',
        type: 'GET',
        success: function(response) {
            $('#category_id').html(response);
            $('#category-filter').html('<option value="">All Categories</option>' + response);
        }
    });
}

function loadSuppliers() {
    $.ajax({
        url: 'ajax/get-suppliers.php',
        type: 'GET',
        success: function(response) {
            $('#supplier_id').html(response);
        }
    });
}

function loadUOM() {
    $.ajax({
        url: 'ajax/get-uom.php',
        type: 'GET',
        success: function(response) {
            $('#uom_id').html(response);
        }
    });
}

// Modal functions
function openAddModal() {
    $('#modal-title').text('Add New Product');
    $('#product-form')[0].reset();
    $('#product-id').val('');
    $('#product-modal').removeClass('hidden');
    $('body').addClass('overflow-hidden');
}

function openEditModal(id) {
    $('#modal-title').text('Edit Product');
    
    $.ajax({
        url: 'ajax/get-product.php',
        type: 'GET',
        data: { id: id },
        success: function(response) {
            try {
                const product = JSON.parse(response);
                
                if (product.error) {
                    showAlert(product.error, 'error');
                    return;
                }
                
                $('#product-id').val(product.id);
                $('#sku').val(product.sku);
                $('#barcode').val(product.barcode);
                $('#name').val(product.name);
                $('#category_id').val(product.category_id);
                $('#supplier_id').val(product.supplier_id);
                $('#uom_id').val(product.uom_id);
                $('#cost_price').val(product.cost_price);
                $('#selling_price').val(product.selling_price);
                $('#stock_quantity').val(product.stock_quantity);
                $('#min_stock_level').val(product.min_stock_level);
                $('#description').val(product.description);
                $('#is_active').prop('checked', product.is_active == 1);
                
                // Handle product image
                if (product.img && product.img !== '-' && product.img !== '') {
                    $('#current-img').attr('src', 'uploads/products/' + product.img);
                    $('#current-image').removeClass('hidden');
                } else {
                    $('#current-image').addClass('hidden');
                }
                
                // Reset new image preview
                $('#image-preview').addClass('hidden');
                $('#product_image').val('');
                
                $('#product-modal').removeClass('hidden');
                $('body').addClass('overflow-hidden');
            } catch (e) {
                showAlert('Error loading product data', 'error');
            }
        },
        error: function() {
            showAlert('Error loading product data', 'error');
        }
    });
}

function closeModal() {
    $('#product-modal').addClass('hidden');
    $('body').removeClass('overflow-hidden');
    // Reset image previews when closing modal
    resetImagePreviews();
}

// Image handling functions
function previewImage(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate file size (2MB max)
        if (file.size > 2 * 1024 * 1024) {
            showAlert('File size must be less than 2MB', 'error');
            input.value = '';
            return;
        }
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!allowedTypes.includes(file.type)) {
            showAlert('Please select a valid image file (JPG, JPEG, PNG)', 'error');
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#preview-img').attr('src', e.target.result);
            $('#image-preview').removeClass('hidden');
        };
        reader.readAsDataURL(file);
    }
}

function removeImage() {
    $('#product_image').val('');
    $('#image-preview').addClass('hidden');
    $('#preview-img').attr('src', '');
}

function removeCurrentImage() {
    $('#current-image').addClass('hidden');
    $('#current-img').attr('src', '');
    // Add hidden input to indicate image should be removed
    if ($('#remove_current_image').length === 0) {
        $('#product-form').append('<input type="hidden" id="remove_current_image" name="remove_current_image" value="1">');
    }
}

function resetImagePreviews() {
    $('#product_image').val('');
    $('#image-preview').addClass('hidden');
    $('#current-image').addClass('hidden');
    $('#preview-img').attr('src', '');
    $('#current-img').attr('src', '');
    $('#remove_current_image').remove();
}

// Save product
function saveProduct() {
    const formData = new FormData($('#product-form')[0]);
    const productId = $('#product-id').val();
    
    // Determine if this is an edit or add operation
    const url = productId ? 'ajax/edit-product.php' : 'ajax/save-product.php';
    
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
                loadProducts();
                showAlert(data.message, 'success');
            } else {
                showAlert(data.message, 'error');
            }
        },
        error: function() {
            showAlert('Error saving product', 'error');
        }
    });
}

// Delete product
function deleteProduct(id) {
    confirmDelete('ajax/delete-product.php?id=' + id, 'Are you sure you want to delete this product?');
}

// Filter by stock status
function filterByStockStatus(status) {
    // Remove active class from all cards
    $('.stats-card').removeClass('ring-2 ring-blue-500 bg-blue-50');
    
    // Add active class to clicked card
    $(`[onclick="filterByStockStatus('${status}')"]`).addClass('ring-2 ring-blue-500 bg-blue-50');
    
    // Update filter dropdown
    $('#stock-status-filter').val(status);
    
    // Trigger filter
    applyFilters();
}

// Apply all filters
function applyFilters() {
    const search = $('#search-input').val();
    const status = $('#status-filter').val();
    const sort = $('#sort-filter').val();
    const stockStatus = $('#stock-status-filter').val();
    
    $.ajax({
        url: 'ajax/filter-products.php',
        type: 'GET',
        data: { search, status, sort, stock_status: stockStatus },
        success: function(response) {
            $('#products-tbody').html(response);
            
            // Update product count
            const productCount = $('#products-tbody tr').not('.no-data').length;
            $('#products-count').text(productCount + ' products');
            
            // Check if table is empty
            if ($('#products-tbody tr').length === 0 || $('#products-tbody tr').hasClass('no-data')) {
                $('#empty-state').removeClass('hidden');
            } else {
                $('#empty-state').addClass('hidden');
            }
            
            // Update mobile view
            updateMobileView();
        }
    });
}

// Search and filter with debouncing
let searchTimeout;
$('#search-input, #status-filter, #sort-filter, #stock-status-filter').on('change keyup', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
        applyFilters();
    }, 300);
});

// Close modal when clicking outside
$(document).on('click', '#product-modal', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Close modal with Escape key
$(document).on('keydown', function(e) {
    if (e.key === 'Escape' && !$('#product-modal').hasClass('hidden')) {
        closeModal();
    }
});
</script>

<?php include 'include/footer.php'; ?>
