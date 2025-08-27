<?php
include 'include/conn.php';
include 'include/session.php';
include 'include/pos-head.php';
include 'include/header.php';

// Get current session
$user_id = $_SESSION['user_id'];
$query_session = mysqli_query($conn, "SELECT * FROM sales_sessions WHERE user_id = $user_id AND status = 'open' ORDER BY id DESC LIMIT 1");
$current_session = mysqli_fetch_array($query_session);

if (!$current_session) {
    // Create new session
    mysqli_query($conn, "INSERT INTO sales_sessions (user_id, opening_amount) VALUES ($user_id, 0)");
    $current_session_id = mysqli_insert_id($conn);
} else {
    $current_session_id = $current_session['id'];
}

// Get categories and products for sidebar
$categories_query = mysqli_query($conn, "SELECT * FROM categories WHERE is_active = 1 ORDER BY name");
$categories = [];
while ($category = mysqli_fetch_array($categories_query)) {
    $category_id = $category['id'];
    $products_query = mysqli_query($conn, "SELECT id, name, sku, selling_price, stock_quantity FROM products WHERE category_id = $category_id AND is_active = 1 ORDER BY name");
    $products = [];
    while ($product = mysqli_fetch_array($products_query)) {
        $products[] = $product;
    }
    $category['products'] = $products;
    $categories[] = $category;
}
?>

<!-- Custom POS Sidebar -->
<aside class="sidebar fixed left-0 top-0 h-full bg-white shadow-lg transform transition-transform duration-300 ease-in-out z-40 lg:translate-x-0" style="top: 64px;">
    <div class="p-4">
        <!-- Header with Toggle Button -->
        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center space-x-2">
                <!-- Desktop Toggle Button -->
                <button onclick="toggleDesktopSidebar()" class="hidden lg:flex p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors" title="Toggle Sidebar (Ctrl+B)">
                    <i class="fas fa-chevron-left text-lg sidebar-toggle-icon"></i>
                </button>
                <!-- Mobile Close Button -->
                <button onclick="closeMobileSidebar()" class="lg:hidden p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>
        
        <!-- Back to Dashboard Button -->
        <div class="mb-6 sidebar-content">
            <a href="dashboard.php" class="flex items-center px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <i class="fas fa-arrow-left w-5 h-5 mr-3"></i>
                <span class="font-medium">Back to Dashboard</span>
            </a>
        </div>
        
        <!-- Icon-only Back Button (for collapsed state) -->
        <div class="mb-6 sidebar-icon-only">
            <a href="dashboard.php" class="flex items-center justify-center w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <i class="fas fa-arrow-left w-5 h-5"></i>
            </a>
        </div>

        <!-- Categories Grid -->
        <div class="space-y-4 sidebar-content">
            <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center">
                <i class="fas fa-tags mr-2 text-blue-600"></i>
                Categories
            </h3>
            
            <!-- Search Bar -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400 text-sm"></i>
                </div>
                <input type="text" id="product-search" placeholder="Search products..." 
                       class="w-full pl-10 pr-10 py-2 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm"
                       autocomplete="off">
                <div id="search-loading" class="absolute inset-y-0 right-0 pr-3 flex items-center hidden">
                    <div class="w-4 h-4 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                </div>
                <button id="clear-search" onclick="clearSearch()" class="absolute inset-y-0 right-0 pr-3 flex items-center justify-center hidden w-6 h-6">
                    <i class="fas fa-times text-gray-400 hover:text-gray-600 text-sm"></i>
                </button>
            </div>
            
            <!-- Search Results -->
            <div id="search-results" class="hidden space-y-2 max-h-64 overflow-y-auto">
                <!-- Search results will be loaded here -->
            </div>
            
            <!-- Categories Grid - 4 columns on desktop, 2 on mobile -->
            <div id="categories-grid" class="grid grid-cols-2 lg:grid-cols-4 gap-2 lg:gap-3">
                <?php foreach ($categories as $category): ?>
                <button class="category-card flex flex-col items-center justify-center p-3 lg:p-4 bg-white border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 hover:shadow-md transition-all duration-200 transform hover:-translate-y-1" 
                        onclick="toggleCategory('category-<?= $category['id'] ?>')"
                        data-category-id="<?= $category['id'] ?>">
                    <div class="w-8 h-8 lg:w-10 lg:h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center mb-2">
                        <i class="fas fa-folder text-white text-sm lg:text-base"></i>
                    </div>
                    <span class="text-xs lg:text-sm font-medium text-gray-700 text-center leading-tight"><?= htmlspecialchars($category['name']) ?></span>
                    <span class="text-xs text-gray-500 mt-1"><?= count($category['products']) ?> items</span>
                </button>
                <?php endforeach; ?>
            </div>
            
            <!-- Products Section -->
            <div class="mt-6">
                <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-box mr-2 text-green-600"></i>
                    Products
                </h4>
                
                <?php foreach ($categories as $category): ?>
                <div id="category-<?= $category['id'] ?>" class="hidden space-y-2 mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <h5 class="text-sm font-medium text-gray-700"><?= htmlspecialchars($category['name']) ?></h5>
                        <button onclick="closeCategory('category-<?= $category['id'] ?>')" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                    
                    <?php if (empty($category['products'])): ?>
                        <div class="text-sm text-gray-500 italic px-4 py-2 bg-gray-50 rounded-lg">No products in this category</div>
                    <?php else: ?>
                        <?php foreach ($category['products'] as $product): ?>
                        <button onclick="addProductToCartFromSidebar(<?= $product['id'] ?>)" 
                                class="w-full flex items-center justify-between px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-green-50 hover:text-green-600 transition-colors border border-gray-100 hover:border-green-200">
                            <div class="flex items-center">
                                <i class="fas fa-box w-4 h-4 mr-2 text-green-500"></i>
                                <div class="text-left">
                                    <div class="font-medium"><?= htmlspecialchars($product['name']) ?></div>
                                    <div class="text-xs text-gray-400"><?= htmlspecialchars($product['sku']) ?></div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-semibold text-green-600">RM <?= number_format($product['selling_price'], 2) ?></div>
                                <div class="text-xs text-gray-400">Stock: <?= $product['stock_quantity'] ?></div>
                            </div>
                        </button>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</aside>



<!-- Mobile Sidebar Overlay -->
<div id="mobile-sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden" onclick="closeMobileSidebar()"></div>

<!-- Main Content -->
<div class="main-content ml-0 pt-16">
    <!-- Background Gradient -->
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-indigo-50 -z-10"></div>
    
    <div class="flex flex-col lg:flex-row h-screen">
        <!-- Left Side - Product Search and Grid -->
        <div class="flex-1 flex flex-col order-2 lg:order-1">
            <!-- Header with Session Info -->
            <div class="backdrop-blur-sm bg-white/70 border-b border-gray-200/50 p-3 lg:p-4">
                <div class="flex items-center justify-between mb-3 lg:mb-4">
                    <div class="flex items-center space-x-2 lg:space-x-4">
                        <div class="p-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl">
                            <i class="fas fa-shopping-cart text-white text-lg lg:text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-lg lg:text-2xl font-bold text-gray-900">Shopping Cart</h1>
                            <p class="text-xs lg:text-sm text-gray-600">Session #<?= $current_session_id ?></p>
                        </div>

                    </div>
                    <div class="flex items-center space-x-2 lg:space-x-3">
                        <div class="text-right hidden sm:block">
                            <p class="text-xs lg:text-sm text-gray-600">Cashier</p>
                            <p class="font-medium text-gray-900 text-sm lg:text-base"><?= $_SESSION['full_name'] ?></p>
                        </div>
                        <div class="w-8 h-8 lg:w-10 lg:h-10 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-white text-sm lg:text-base"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Product Entry Bar -->
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
                    <div class="flex-1 relative group">
                        <i class="fas fa-barcode absolute left-3 lg:left-4 top-1/2 transform -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 transition-colors duration-200 text-sm lg:text-base"></i>
                        <input type="text" id="product-entry" placeholder="Scan barcode or enter SKU..." 
                               class="w-full pl-10 lg:pl-12 pr-4 py-2 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 placeholder-gray-400 text-sm lg:text-base"
                               autocomplete="off">
                        <div id="entry-status" class="absolute right-3 lg:right-4 top-1/2 transform -translate-y-1/2 hidden">
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        </div>
                        <div id="entry-loading" class="absolute right-3 lg:right-4 top-1/2 transform -translate-y-1/2 hidden">
                            <div class="w-4 h-4 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                        </div>
                    </div>
                    <button onclick="clearCart()" class="inline-flex items-center justify-center px-4 lg:px-6 py-2 lg:py-3 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-xl hover:from-red-700 hover:to-pink-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 text-sm lg:text-base">
                        <i class="fas fa-trash mr-2"></i><span class="hidden sm:inline">Clear Cart</span><span class="sm:hidden">Clear</span>
                    </button>
                </div>
            </div>

            <!-- Cart Items Grid -->
            <div class="flex-1 p-3 lg:p-6 bg-gradient-to-br from-gray-50/50 to-blue-50/50 overflow-y-auto">
                <div id="cart-grid" class="space-y-3 lg:space-y-4">
                    <!-- Cart items will be loaded here -->
                </div>
            </div>
        </div>

        <!-- Right Side - Cart and Payment -->
        <div class="w-full lg:w-96 backdrop-blur-sm bg-white/70 border-t lg:border-l lg:border-t-0 border-gray-200/50 flex flex-col shadow-2xl order-1 lg:order-2">
            <!-- Cart Header -->
            <div class="p-4 lg:p-6 border-b border-gray-200/50 bg-gradient-to-r from-gray-50/50 to-gray-100/50">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-lg lg:text-xl font-bold text-gray-900">Shopping Cart</h2>
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="text-xs text-gray-500">Live</span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <p class="text-xs lg:text-sm text-gray-600">Invoice #<span id="invoice-number" class="font-mono font-medium"><?= date('Ymd') . str_pad($current_session_id, 4, '0', STR_PAD_LEFT) ?></span></p>
                    <div class="text-xs text-gray-500">
                        <i class="fas fa-clock mr-1"></i><?= date('H:i') ?>
                    </div>
                </div>
            </div>

            <!-- Cart Items -->
            <div class="flex-1 overflow-y-auto p-4 lg:p-6">
                <div id="cart-items" class="space-y-3 lg:space-y-4">
                    <!-- Cart items will be loaded here -->
                </div>
                
                <!-- Empty Cart State -->
                <div id="empty-cart" class="hidden text-center py-8 lg:py-12">
                    <div class="w-16 h-16 lg:w-20 lg:h-20 mx-auto bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-shopping-cart text-xl lg:text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-base lg:text-lg font-medium text-gray-900 mb-2">Cart is Empty</h3>
                    <p class="text-xs lg:text-sm text-gray-500">Start adding products to begin a sale</p>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="p-4 lg:p-6 border-t border-gray-200/50 bg-gradient-to-r from-gray-50/50 to-gray-100/50 space-y-4 lg:space-y-6">
                <!-- Summary Details -->
                <div class="space-y-2 lg:space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm lg:text-base text-gray-600">Subtotal:</span>
                        <span id="subtotal" class="font-semibold text-gray-900 text-sm lg:text-base">RM 0.00</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm lg:text-base text-gray-600">Discount:</span>
                        <span id="discount" class="font-semibold text-green-600 text-sm lg:text-base">RM 0.00</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm lg:text-base text-gray-600">Tax (6%):</span>
                        <span id="tax" class="font-semibold text-gray-900 text-sm lg:text-base">RM 0.00</span>
                    </div>
                    <div class="border-t border-gray-200 pt-2 lg:pt-3">
                        <div class="flex justify-between items-center">
                            <span class="text-base lg:text-lg font-bold text-gray-900">Total:</span>
                            <span id="total" class="text-2xl lg:text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">RM 0.00</span>
                        </div>
                    </div>
                </div>

                <!-- Member Selection -->
                <div class="space-y-2">
                    <label class="block text-xs lg:text-sm font-semibold text-gray-700">Customer</label>
                    <div class="flex space-x-2">
                        <div class="flex-1">
                            <input type="text" id="selected-member-display" placeholder="Walk-in Customer" readonly 
                                   class="w-full px-3 lg:px-4 py-2 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 cursor-pointer text-sm lg:text-base">
                            <input type="hidden" id="member-select" value="">
                        </div>
                        <button onclick="openMemberModal()" class="px-3 lg:px-4 py-2 lg:py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i class="fas fa-users text-sm lg:text-base"></i>
                        </button>
                    </div>
                    <div id="member-discount-info" class="hidden"></div>
                </div>

                <!-- Payment Buttons -->
                <div class="space-y-2 lg:space-y-3">
                    <div class="grid grid-cols-2 gap-2 lg:gap-3">
                        <button onclick="processPayment('cash')" class="group relative px-3 lg:px-4 py-3 lg:py-4 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <div class="absolute inset-0 bg-white/20 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="relative z-10">
                                <i class="fas fa-money-bill text-lg lg:text-xl mb-1"></i>
                                <div class="text-xs lg:text-sm font-medium">Cash</div>
                            </div>
                        </button>
                        <button onclick="processPayment('card')" class="group relative px-3 lg:px-4 py-3 lg:py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <div class="absolute inset-0 bg-white/20 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="relative z-10">
                                <i class="fas fa-credit-card text-lg lg:text-xl mb-1"></i>
                                <div class="text-xs lg:text-sm font-medium">Card</div>
                            </div>
                        </button>
                    </div>
                    <button onclick="processPayment('ewallet')" class="group relative w-full px-3 lg:px-4 py-3 lg:py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <div class="absolute inset-0 bg-white/20 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative z-10 flex items-center justify-center">
                            <i class="fas fa-mobile-alt text-lg lg:text-xl mr-2"></i>
                            <span class="font-medium text-sm lg:text-base">E-Wallet</span>
                        </div>
                    </button>
                </div>
                
                <!-- Keyboard Shortcut Hint -->
                <div class="text-center hidden lg:block">
                    <p class="text-xs text-gray-500">
                        <i class="fas fa-keyboard mr-1"></i>Press <kbd class="px-2 py-1 bg-gray-100 rounded text-xs">Ctrl + Enter</kbd> for quick cash payment
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Modal with Glassmorphism -->
<div id="product-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-2 sm:p-4">
        <div class="backdrop-blur-md bg-white/95 rounded-2xl sm:rounded-3xl shadow-2xl max-w-4xl w-full max-h-screen overflow-y-auto border border-white/20">
            <div class="p-4 sm:p-6 lg:p-8 border-b border-gray-200/50 bg-gradient-to-r from-gray-50/50 to-gray-100/50">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        <div class="p-2 sm:p-3 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl">
                            <i class="fas fa-box text-white text-lg sm:text-xl"></i>
                        </div>
                        <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900">Add New Product</h3>
                    </div>
                    <button onclick="closeProductModal()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all duration-200">
                        <i class="fas fa-times text-lg sm:text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-4 sm:p-6 lg:p-8">
                <form id="product-form" class="space-y-8">
                    <!-- Basic Information -->
                    <div class="space-y-4 lg:space-y-6">
                        <div class="flex items-center space-x-2 sm:space-x-3 mb-4 lg:mb-6">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <i class="fas fa-info-circle text-blue-600"></i>
                            </div>
                            <h4 class="text-base lg:text-lg font-semibold text-gray-900">Basic Information</h4>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-6">
                            <div class="space-y-2">
                                <label class="block text-xs lg:text-sm font-semibold text-gray-700">SKU *</label>
                                <input type="text" name="sku" required class="w-full px-3 lg:px-4 py-2 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm lg:text-base">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs lg:text-sm font-semibold text-gray-700">Barcode</label>
                                <input type="text" name="barcode" class="w-full px-3 lg:px-4 py-2 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm lg:text-base">
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-xs lg:text-sm font-semibold text-gray-700">Product Name *</label>
                            <input type="text" name="name" required class="w-full px-3 lg:px-4 py-2 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm lg:text-base">
                        </div>
                    </div>
                    
                    <!-- Categories & Suppliers -->
                    <div class="space-y-4 lg:space-y-6">
                        <div class="flex items-center space-x-2 sm:space-x-3 mb-4 lg:mb-6">
                            <div class="p-2 bg-green-100 rounded-lg">
                                <i class="fas fa-tags text-green-600"></i>
                            </div>
                            <h4 class="text-base lg:text-lg font-semibold text-gray-900">Categories & Suppliers</h4>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
                            <div class="space-y-2">
                                <label class="block text-xs lg:text-sm font-semibold text-gray-700">Category *</label>
                                <select name="category_id" required class="w-full px-3 lg:px-4 py-2 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm lg:text-base">
                                    <option value="">Select Category</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs lg:text-sm font-semibold text-gray-700">Supplier *</label>
                                <select name="supplier_id" required class="w-full px-3 lg:px-4 py-2 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm lg:text-base">
                                    <option value="">Select Supplier</option>
                                </select>
                            </div>
                            <div class="space-y-2 sm:col-span-2 lg:col-span-1">
                                <label class="block text-xs lg:text-sm font-semibold text-gray-700">UOM *</label>
                                <select name="uom_id" required class="w-full px-3 lg:px-4 py-2 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm lg:text-base">
                                    <option value="">Select UOM</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pricing -->
                    <div class="space-y-4 lg:space-y-6">
                        <div class="flex items-center space-x-2 sm:space-x-3 mb-4 lg:mb-6">
                            <div class="p-2 bg-yellow-100 rounded-lg">
                                <i class="fas fa-dollar-sign text-yellow-600"></i>
                            </div>
                            <h4 class="text-base lg:text-lg font-semibold text-gray-900">Pricing Information</h4>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-6">
                            <div class="space-y-2">
                                <label class="block text-xs lg:text-sm font-semibold text-gray-700">Cost Price *</label>
                                <div class="relative">
                                    <span class="absolute left-3 lg:left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium text-sm lg:text-base">RM</span>
                                    <input type="number" name="cost_price" step="0.01" required class="w-full pl-10 lg:pl-12 pr-4 py-2 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm lg:text-base">
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs lg:text-sm font-semibold text-gray-700">Selling Price *</label>
                                <div class="relative">
                                    <span class="absolute left-3 lg:left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium text-sm lg:text-base">RM</span>
                                    <input type="number" name="selling_price" step="0.01" required class="w-full pl-10 lg:pl-12 pr-4 py-2 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm lg:text-base">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Inventory -->
                    <div class="space-y-4 lg:space-y-6">
                        <div class="flex items-center space-x-2 sm:space-x-3 mb-4 lg:mb-6">
                            <div class="p-2 bg-purple-100 rounded-lg">
                                <i class="fas fa-warehouse text-purple-600"></i>
                            </div>
                            <h4 class="text-base lg:text-lg font-semibold text-gray-900">Inventory Management</h4>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-6">
                            <div class="space-y-2">
                                <label class="block text-xs lg:text-sm font-semibold text-gray-700">Stock Quantity</label>
                                <input type="number" name="stock_quantity" value="0" class="w-full px-3 lg:px-4 py-2 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm lg:text-base">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs lg:text-sm font-semibold text-gray-700">Min Stock Level</label>
                                <input type="number" name="min_stock_level" value="0" class="w-full px-3 lg:px-4 py-2 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm lg:text-base">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div class="space-y-4 lg:space-y-6">
                        <div class="flex items-center space-x-2 sm:space-x-3 mb-4 lg:mb-6">
                            <div class="p-2 bg-indigo-100 rounded-lg">
                                <i class="fas fa-align-left text-indigo-600"></i>
                            </div>
                            <h4 class="text-base lg:text-lg font-semibold text-gray-900">Additional Information</h4>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-xs lg:text-sm font-semibold text-gray-700">Description</label>
                            <textarea name="description" rows="4" class="w-full px-3 lg:px-4 py-2 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none text-sm lg:text-base"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="p-4 sm:p-6 lg:p-8 border-t border-gray-200/50 bg-gradient-to-r from-gray-50/50 to-gray-100/50 flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-4">
                <button onclick="closeProductModal()" class="px-4 lg:px-6 py-2 lg:py-3 text-gray-600 border border-gray-300 rounded-xl hover:bg-gray-50 transition-all duration-200 font-medium text-sm lg:text-base">
                    Cancel
                </button>
                <button onclick="saveProduct()" class="px-4 lg:px-6 py-2 lg:py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 font-medium text-sm lg:text-base">
                    Save Product
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal with Glassmorphism -->
<div id="payment-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-2 sm:p-4">
        <div class="backdrop-blur-md bg-white/95 rounded-2xl sm:rounded-3xl shadow-2xl max-w-md w-full border border-white/20">
            <div class="p-4 sm:p-6 lg:p-8 border-b border-gray-200/50 bg-gradient-to-r from-gray-50/50 to-gray-100/50">
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <div class="p-2 sm:p-3 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl">
                        <i class="fas fa-credit-card text-white text-lg sm:text-xl"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900">Complete Payment</h3>
                </div>
            </div>
            <div class="p-4 sm:p-6 lg:p-8">
                <div class="space-y-4 lg:space-y-6">
                    <div class="text-center">
                        <label class="block text-xs lg:text-sm font-semibold text-gray-700 mb-2">Total Amount</label>
                        <div class="text-2xl sm:text-3xl lg:text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent" id="payment-total">RM 0.00</div>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs lg:text-sm font-semibold text-gray-700">Amount Received</label>
                        <div class="relative">
                            <span class="absolute left-3 lg:left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium text-sm lg:text-base">RM</span>
                            <input type="number" id="amount-received" step="0.01" class="w-full pl-10 lg:pl-12 pr-4 py-3 lg:py-4 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-base lg:text-lg font-medium">
                        </div>
                    </div>
                    
                    <!-- Malaysian Ringgit Banknotes -->
                    <div id="quick-amount-selection" class="space-y-3">
                        <label class="block text-xs lg:text-sm font-semibold text-gray-700">Quick Amount Selection</label>
                        <div class="grid grid-cols-5 gap-2">
                            <!-- RM 5 Banknote -->
                            <button onclick="addBanknoteAmount(5)" class="group relative overflow-hidden rounded-lg border-2 border-gray-200 hover:border-green-400 hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                                <div class="w-full h-16 bg-gradient-to-br from-green-400 to-green-600 relative">
                                    <div class="absolute inset-0 bg-white/20"></div>
                                    <div class="absolute top-1 left-1 text-white text-xs font-bold">RM</div>
                                    <div class="absolute bottom-1 right-1 text-white text-lg font-bold">5</div>
                                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-white text-xs opacity-80">MALAYSIA</div>
                                </div>
                                <div class="bg-white p-1 text-center">
                                    <span class="text-xs font-medium text-gray-700">RM 5</span>
                                </div>
                            </button>
                            
                            <!-- RM 10 Banknote -->
                            <button onclick="addBanknoteAmount(10)" class="group relative overflow-hidden rounded-lg border-2 border-gray-200 hover:border-orange-400 hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                                <div class="w-full h-16 bg-gradient-to-br from-orange-400 to-orange-600 relative">
                                    <div class="absolute inset-0 bg-white/20"></div>
                                    <div class="absolute top-1 left-1 text-white text-xs font-bold">RM</div>
                                    <div class="absolute bottom-1 right-1 text-white text-lg font-bold">10</div>
                                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-white text-xs opacity-80">MALAYSIA</div>
                                </div>
                                <div class="bg-white p-1 text-center">
                                    <span class="text-xs font-medium text-gray-700">RM 10</span>
                                </div>
                            </button>
                            
                            <!-- RM 20 Banknote -->
                            <button onclick="addBanknoteAmount(20)" class="group relative overflow-hidden rounded-lg border-2 border-gray-200 hover:border-yellow-400 hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                                <div class="w-full h-16 bg-gradient-to-br from-yellow-400 to-yellow-600 relative">
                                    <div class="absolute inset-0 bg-white/20"></div>
                                    <div class="absolute top-1 left-1 text-white text-xs font-bold">RM</div>
                                    <div class="absolute bottom-1 right-1 text-white text-lg font-bold">20</div>
                                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-white text-xs opacity-80">MALAYSIA</div>
                                </div>
                                <div class="bg-white p-1 text-center">
                                    <span class="text-xs font-medium text-gray-700">RM 20</span>
                                </div>
                            </button>
                            
                            <!-- RM 50 Banknote -->
                            <button onclick="addBanknoteAmount(50)" class="group relative overflow-hidden rounded-lg border-2 border-gray-200 hover:border-blue-400 hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                                <div class="w-full h-16 bg-gradient-to-br from-blue-400 to-blue-600 relative">
                                    <div class="absolute inset-0 bg-white/20"></div>
                                    <div class="absolute top-1 left-1 text-white text-xs font-bold">RM</div>
                                    <div class="absolute bottom-1 right-1 text-white text-lg font-bold">50</div>
                                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-white text-xs opacity-80">MALAYSIA</div>
                                </div>
                                <div class="bg-white p-1 text-center">
                                    <span class="text-xs font-medium text-gray-700">RM 50</span>
                                </div>
                            </button>
                            
                            <!-- RM 100 Banknote -->
                            <button onclick="addBanknoteAmount(100)" class="group relative overflow-hidden rounded-lg border-2 border-gray-200 hover:border-purple-400 hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                                <div class="w-full h-16 bg-gradient-to-br from-purple-400 to-purple-600 relative">
                                    <div class="absolute inset-0 bg-white/20"></div>
                                    <div class="absolute top-1 left-1 text-white text-xs font-bold">RM</div>
                                    <div class="absolute bottom-1 right-1 text-white text-lg font-bold">100</div>
                                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-white text-xs opacity-80">MALAYSIA</div>
                                </div>
                                <div class="bg-white p-1 text-center">
                                    <span class="text-xs font-medium text-gray-700">RM 100</span>
                                </div>
                            </button>
                        </div>
                    </div>
                    <div class="text-center">
                        <label class="block text-xs lg:text-sm font-semibold text-gray-700 mb-2">Change</label>
                        <div class="text-xl sm:text-2xl font-bold text-green-600" id="change-amount">RM 0.00</div>
                    </div>
                </div>
            </div>
            <div class="p-4 sm:p-6 lg:p-8 border-t border-gray-200/50 bg-gradient-to-r from-gray-50/50 to-gray-100/50 flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-4">
                <button onclick="closePaymentModal()" class="px-4 lg:px-6 py-2 lg:py-3 text-gray-600 border border-gray-300 rounded-xl hover:bg-gray-50 transition-all duration-200 font-medium text-sm lg:text-base">
                    Cancel
                </button>
                <button onclick="completePayment()" class="px-4 lg:px-6 py-2 lg:py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 font-medium text-sm lg:text-base">
                    Complete Sale
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Member Selection Modal -->
<div id="member-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-2 sm:p-4">
        <div class="backdrop-blur-md bg-white/95 rounded-2xl sm:rounded-3xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden border border-white/20">
            <div class="p-4 sm:p-6 border-b border-gray-200/50 bg-gradient-to-r from-gray-50/50 to-gray-100/50">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        <div class="p-2 sm:p-3 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl">
                            <i class="fas fa-users text-white text-lg sm:text-xl"></i>
                        </div>
                        <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900">Select Customer</h3>
                    </div>
                    <button onclick="closeMemberModal()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all duration-200">
                        <i class="fas fa-times text-lg sm:text-xl"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-4 sm:p-6">
                <!-- Search Bar and Quick Add Button -->
                <div class="mb-4 lg:mb-6">
                    <div class="flex space-x-3">
                        <div class="relative flex-1">
                            <i class="fas fa-search absolute left-3 lg:left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm lg:text-base"></i>
                            <input type="text" id="member-search" placeholder="Search by member code, name, or phone..." 
                                   class="w-full pl-10 lg:pl-12 pr-4 py-2 lg:py-3 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm lg:text-base">
                        </div>
                        <button onclick="openQuickAddMemberModal()" 
                                class="px-4 py-2 lg:py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-200 text-sm lg:text-base font-medium">
                            <i class="fas fa-plus mr-1"></i>
                            Quick Add
                        </button>
                    </div>
                </div>
                
                <!-- Walk-in Customer Option -->
                <div class="mb-4">
                    <button onclick="selectMember('', 'Walk-in Customer')" 
                            class="w-full p-3 lg:p-4 bg-gradient-to-r from-gray-100 to-gray-200 rounded-xl hover:from-gray-200 hover:to-gray-300 transition-all duration-200 text-left border-2 border-transparent hover:border-blue-300">
                        <div class="flex items-center space-x-2 lg:space-x-3">
                            <div class="w-8 h-8 lg:w-10 lg:h-10 bg-gradient-to-r from-gray-500 to-gray-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-white text-sm lg:text-base"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 text-sm lg:text-base">Walk-in Customer</h4>
                                <p class="text-xs lg:text-sm text-gray-600">No membership discount</p>
                            </div>
                        </div>
                    </button>
                </div>
                
                <!-- Members List -->
                <div class="space-y-2 lg:space-y-3 max-h-96 overflow-y-auto" id="members-list">
                    <!-- Members will be loaded here -->
                </div>
                
                <!-- Loading State -->
                <div id="members-loading" class="text-center py-6 lg:py-8">
                    <div class="w-6 h-6 lg:w-8 lg:h-8 border-2 border-blue-500 border-t-transparent rounded-full animate-spin mx-auto mb-2"></div>
                    <p class="text-gray-500 text-sm lg:text-base">Loading members...</p>
                </div>
                
                <!-- No Results -->
                <div id="no-members-found" class="hidden text-center py-6 lg:py-8">
                    <div class="w-12 h-12 lg:w-16 lg:h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-search text-xl lg:text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-base lg:text-lg font-medium text-gray-900 mb-2">Search for Members</h3>
                    <p class="text-xs lg:text-sm text-gray-500">Type a member code, name, or phone number to search</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Add Member Modal -->
<div id="quick-add-member-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-2 sm:p-4">
        <div class="backdrop-blur-md bg-white/95 rounded-2xl sm:rounded-3xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden border border-white/20">
            <div class="p-4 sm:p-6 border-b border-gray-200/50 bg-gradient-to-r from-gray-50/50 to-gray-100/50">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        <div class="p-2 sm:p-3 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl">
                            <i class="fas fa-user-plus text-white text-lg sm:text-xl"></i>
                        </div>
                        <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900">Quick Add Member</h3>
                    </div>
                    <button onclick="closeQuickAddMemberModal()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all duration-200">
                        <i class="fas fa-times text-lg sm:text-xl"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-4 sm:p-6">
                <form id="quick-add-member-form">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Member Code *</label>
                            <input type="text" id="quick-member-code" name="member_code" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                            <input type="text" id="quick-member-name" name="name" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                            <input type="tel" id="quick-member-phone" name="phone"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" id="quick-member-email" name="email"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Membership Tier</label>
                        <select id="quick-membership-tier" name="membership_tier_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Tier</option>
                        </select>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                        <textarea id="quick-member-address" name="address" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeQuickAddMemberModal()" 
                                class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all duration-200">
                            Add Member
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let cart = [];
let currentPaymentMethod = '';

// Load cart on page load
$(document).ready(function() {
    loadCart();
    loadCategories();
    loadSuppliers();
    loadUOM();
    
    // Focus on product entry input
    $('#product-entry').focus();
    
    // Handle member search
    $('#member-search').on('input', function() {
        const searchTerm = $(this).val();
        searchMembers(searchTerm);
    });
    
    // Handle member display click
    $('#selected-member-display').on('click', function() {
        openMemberModal();
    });
    
    // Handle mobile viewport
    function setViewportHeight() {
        let vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
    }
    
    setViewportHeight();
    window.addEventListener('resize', setViewportHeight);
    window.addEventListener('orientationchange', setViewportHeight);
    
    // Initialize mobile sidebar state
    function initializeSidebar() {
        if (window.innerWidth < 1024) {
            $('.sidebar').addClass('-translate-x-full');
            $('#mobile-sidebar-overlay').addClass('hidden');
            $('.main-content').css('margin-left', '0');
            console.log('Mobile sidebar initialized - hidden');
        } else {
            $('.sidebar').removeClass('-translate-x-full');
            $('#mobile-sidebar-overlay').addClass('hidden');
            $('.main-content').css('margin-left', '45vw');
            console.log('Desktop sidebar initialized - visible');
        }
    }
    
    // Initialize on page load
    initializeSidebar();
    
    // Handle orientation change
    window.addEventListener('orientationchange', function() {
        setTimeout(function() {
            initializeSidebar();
        }, 100);
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        setTimeout(function() {
            initializeSidebar();
        }, 100);
    });
    
    // Handle overlay click to close sidebar
    $('#mobile-sidebar-overlay').on('click', function(e) {
        e.preventDefault();
        closeMobileSidebar();
    });
    
    // Close sidebar with Escape key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && window.innerWidth < 1024) {
            closeMobileSidebar();
        }
    });
});

// Product entry functionality
let isProcessing = false;

// Handle Enter key for manual entry
$('#product-entry').on('keypress', function(e) {
    if (e.which === 13) { // Enter key
        e.preventDefault();
        const entry = $(this).val();
        if (entry && !isProcessing) {
            processProductEntry(entry);
        }
    }
});

// Load cart
function loadCart() {
    const memberId = $('#member-select').val();
    
    $.ajax({
        url: 'ajax/get-cart.php',
        type: 'GET',
        data: { member_id: memberId },
        success: function(response) {
            const data = JSON.parse(response);
            cart = data.cart;
            
            $('#cart-grid').html(data.html);
            
            // Update cart summary
            $('#subtotal').text('RM ' + parseFloat(data.subtotal).toFixed(2));
            $('#discount').text('RM ' + parseFloat(data.discount).toFixed(2));
            $('#tax').text('RM ' + parseFloat(data.tax).toFixed(2));
            $('#total').text('RM ' + parseFloat(data.total).toFixed(2));
            
            // Show member discount info if applicable
            if (data.member_name && data.discount_percentage > 0) {
                $('#member-discount-info').removeClass('hidden').html(
                    '<div class="text-sm text-green-600 font-medium bg-green-50 p-2 rounded-lg border border-green-200">' +
                    '<i class="fas fa-user-tag mr-1"></i>' +
                    data.member_name + ' - ' + data.discount_percentage + '% discount applied' +
                    '</div>'
                );
            } else {
                $('#member-discount-info').addClass('hidden');
            }
            
            // Show/hide empty cart state
            if (cart.length === 0) {
                $('#empty-cart').removeClass('hidden');
                $('#cart-items').addClass('hidden');
            } else {
                $('#empty-cart').addClass('hidden');
                $('#cart-items').removeClass('hidden');
            }
        }
    });
}

// Process product entry (barcode, SKU, or search)
function processProductEntry(entry) {
    if (isProcessing) return; // Prevent multiple simultaneous requests
    
    isProcessing = true;
    
    // Show loading status
    $('#entry-loading').removeClass('hidden');
    $('#entry-status').addClass('hidden');
    
    $.ajax({
        url: 'ajax/find-product.php',
        type: 'POST',
        data: { entry: entry },
        success: function(response) {
            try {
                const data = JSON.parse(response);
                
                if (data.success) {
                    // Add product to cart
                    addProductToCart(data.product_id);
                    
                    // Clear the entry input
                    $('#product-entry').val('');
                    
                    // Show success feedback
                    showAlert('Product added to cart: ' + data.product_name, 'success');
                    showEntryFeedback('success');
                    
                    // Play success sound
                    playBeepSound();
                } else {
                    // Show error message
                    showAlert(data.message, 'error');
                    showEntryFeedback('error');
                    
                    // Clear the entry input
                    $('#product-entry').val('');
                }
            } catch (e) {
                showAlert('Invalid response from server', 'error');
                $('#product-entry').val('');
            }
        },
        error: function() {
            showAlert('Error processing product entry', 'error');
            $('#product-entry').val('');
        },
        complete: function() {
            // Hide loading status
            $('#entry-loading').addClass('hidden');
            $('#entry-status').addClass('hidden');
            isProcessing = false;
            
            // Refocus on product entry
            $('#product-entry').focus();
        }
    });
}

// Add product to cart
function addProductToCart(productId) {
    $.ajax({
        url: 'ajax/add-to-cart.php',
        type: 'POST',
        data: { product_id: productId },
        success: function(response) {
            const data = JSON.parse(response);
            if (data.success) {
                updateCart();
            } else {
                showAlert(data.message, 'error');
            }
        }
    });
}

// Add product to cart from sidebar
function addProductToCartFromSidebar(productId) {
    $.ajax({
        url: 'ajax/add-to-cart.php',
        type: 'POST',
        data: { product_id: productId },
        success: function(response) {
            const data = JSON.parse(response);
            if (data.success) {
                updateCart();
                playBeepSound();
                
                // Close mobile sidebar if open
                if (window.innerWidth < 1024) {
                    closeMobileSidebar();
                }
            } else {
                showAlert(data.message, 'error');
            }
        }
    });
}

// Play beep sound for successful entry
function playBeepSound() {
    try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.value = 800;
        oscillator.type = 'sine';
        
        gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.1);
    } catch (e) {
        console.log('Audio feedback not supported');
    }
}

// Visual feedback for entry
function showEntryFeedback(type) {
    const entryInput = $('#product-entry');
    
    if (type === 'success') {
        entryInput.addClass('border-green-500 bg-green-50');
        setTimeout(() => {
            entryInput.removeClass('border-green-500 bg-green-50');
        }, 1000);
    } else if (type === 'error') {
        entryInput.addClass('border-red-500 bg-red-50');
        setTimeout(() => {
            entryInput.removeClass('border-red-500 bg-red-50');
        }, 1000);
    }
}

// Clear cart
function clearCart() {
    if (cart.length === 0) {
        showAlert('Cart is already empty', 'info');
        return;
    }
    
    if (confirm('Are you sure you want to clear all items from the cart?')) {
        $.ajax({
            url: 'ajax/clear-cart.php',
            type: 'POST',
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    updateCart();
                    showAlert('Cart cleared successfully', 'success');
                } else {
                    showAlert(data.message, 'error');
                }
            }
        });
    }
}

// Update cart display
function updateCart() {
    loadCart(); // Use the same function to update both cart grid and cart items
}

// Remove item from cart
function removeFromCart(cartId) {
    $.ajax({
        url: 'ajax/remove-from-cart.php',
        type: 'POST',
        data: { cart_id: cartId },
        success: function(response) {
            const data = JSON.parse(response);
            if (data.success) {
                updateCart();
                showAlert('Item removed from cart', 'success');
            } else {
                showAlert(data.message, 'error');
            }
        }
    });
}

// Update item quantity
function updateQuantity(cartId, quantity) {
    if (quantity > 0) {
        $.ajax({
            url: 'ajax/update-cart-quantity.php',
            type: 'POST',
            data: { 
                cart_id: cartId,
                quantity: quantity
            },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    updateCart();
                } else {
                    showAlert(data.message, 'error');
                }
            }
        });
    }
}

// Process payment
function processPayment(method) {
    if (cart.length === 0) {
        showAlert('Cart is empty', 'error');
        return;
    }
    
    currentPaymentMethod = method;
    const total = parseFloat($('#total').text().replace('RM ', ''));
    
    $('#payment-total').text('RM ' + total.toFixed(2));
    $('#amount-received').val(total.toFixed(2));
    $('#change-amount').text('RM 0.00');
    
    // Show/hide quick amount selection based on payment method
    if (method === 'cash') {
        $('#quick-amount-selection').show();
        $('#amount-received').prop('readonly', false);
    } else {
        $('#quick-amount-selection').hide();
        $('#amount-received').prop('readonly', true);
    }
    
    $('#payment-modal').removeClass('hidden');
    $('body').addClass('overflow-hidden');
    $('#amount-received').focus();
}

// Set banknote amount as received amount
function addBanknoteAmount(amount) {
    $('#amount-received').val(amount.toFixed(2));
    
    // Trigger the change calculation
    const total = parseFloat($('#payment-total').text().replace('RM ', ''));
    const change = amount - total;
    
    // Show negative change if amount is insufficient
    if (change < 0) {
        $('#change-amount').text('RM ' + change.toFixed(2)).removeClass('text-green-600').addClass('text-red-600');
    } else {
        $('#change-amount').text('RM ' + change.toFixed(2)).removeClass('text-red-600').addClass('text-green-600');
    }
    
    // Add visual feedback
    const button = event.target.closest('button');
    button.classList.add('ring-4', 'ring-green-400', 'ring-opacity-50');
    setTimeout(() => {
        button.classList.remove('ring-4', 'ring-green-400', 'ring-opacity-50');
    }, 300);
}

// Calculate change
$('#amount-received').on('input', function() {
    const total = parseFloat($('#payment-total').text().replace('RM ', ''));
    const received = parseFloat($(this).val()) || 0;
    const change = received - total;
    
    // Show negative change if amount is insufficient
    if (change < 0) {
        $('#change-amount').text('RM ' + change.toFixed(2)).removeClass('text-green-600').addClass('text-red-600');
    } else {
        $('#change-amount').text('RM ' + change.toFixed(2)).removeClass('text-red-600').addClass('text-green-600');
    }
});

// Complete payment
function completePayment() {
    const memberId = $('#member-select').val();
    const amountReceived = parseFloat($('#amount-received').val()) || 0;
    const total = parseFloat($('#total').text().replace('RM ', ''));
    
    if (amountReceived < total) {
        showAlert('Amount received is less than total', 'error');
        return;
    }
    
    $.ajax({
        url: 'ajax/complete-sale.php',
        type: 'POST',
        data: {
            member_id: memberId,
            payment_method: currentPaymentMethod,
            amount_received: amountReceived
        },
        success: function(response) {
            const data = JSON.parse(response);
            if (data.success) {
                closePaymentModal();
                updateCart();
                showAlert('Sale completed successfully!', 'success');
                printReceipt(data.invoice_number);
            } else {
                showAlert(data.message, 'error');
            }
        }
    });
}

// Print receipt
function printReceipt(invoiceNumber) {
    window.open('print-receipt.php?invoice=' + invoiceNumber, '_blank');
}

// Modal functions
function openProductModal() {
    $('#product-modal').removeClass('hidden');
    $('body').addClass('overflow-hidden');
}

function closeProductModal() {
    $('#product-modal').addClass('hidden');
    $('body').removeClass('overflow-hidden');
    $('#product-form')[0].reset();
}

function closePaymentModal() {
    $('#payment-modal').addClass('hidden');
    $('body').removeClass('overflow-hidden');
    $('#amount-received').val('');
}

// Load form data
function loadCategories() {
    $.ajax({
        url: 'ajax/get-categories.php',
        type: 'GET',
        success: function(response) {
            $('select[name="category_id"]').html(response);
        }
    });
}

function loadSuppliers() {
    $.ajax({
        url: 'ajax/get-suppliers.php',
        type: 'GET',
        success: function(response) {
            $('select[name="supplier_id"]').html(response);
        }
    });
}

function loadUOM() {
    $.ajax({
        url: 'ajax/get-uom.php',
        type: 'GET',
        success: function(response) {
            $('select[name="uom_id"]').html(response);
        }
    });
}

// Member modal functions
function openMemberModal() {
    $('#member-modal').removeClass('hidden');
    $('body').addClass('overflow-hidden');
    $('#member-search').val('').focus();
    loadMembersForModal();
}

function closeMemberModal() {
    $('#member-modal').addClass('hidden');
    $('body').removeClass('overflow-hidden');
}

function loadMembersForModal(search = '') {
    $('#members-loading').removeClass('hidden');
    $('#members-list').addClass('hidden');
    $('#no-members-found').addClass('hidden');
    
    console.log('Searching for:', search);
    
    // If no search term, show search message
    if (!search.trim()) {
        $('#members-loading').addClass('hidden');
        $('#no-members-found').removeClass('hidden');
        return;
    }
    
    $.ajax({
        url: 'ajax/get-members-for-modal.php',
        type: 'GET',
        data: { search: search },
        success: function(response) {
            console.log('Response received:', response);
            $('#members-loading').addClass('hidden');
            
            try {
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                
                if (response.success && response.members && response.members.length > 0) {
                    console.log('Found members:', response.members);
                    displayMembers(response.members);
                } else {
                    console.log('No members found');
                    $('#no-members-found').removeClass('hidden').html(`
                        <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-users text-2xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Members Found</h3>
                        <p class="text-sm text-gray-500">Try adjusting your search terms</p>
                    `);
                }
            } catch (e) {
                console.error('Error parsing response:', e);
                $('#no-members-found').removeClass('hidden');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', status, error);
            console.error('Response text:', xhr.responseText);
            $('#members-loading').addClass('hidden');
            $('#no-members-found').removeClass('hidden');
        }
    });
}

function displayMembers(members) {
    let html = '';
    
    members.forEach(function(member) {
        const tierColor = getTierColor(member.tier_name);
        const discountText = member.discount_percentage > 0 ? member.discount_percentage + '% discount' : 'No discount';
        
        html += `
            <button onclick="selectMember('${member.id}', '${member.name}')" 
                    class="w-full p-3 lg:p-4 bg-gradient-to-r from-white to-gray-50 rounded-xl hover:from-blue-50 hover:to-indigo-50 transition-all duration-200 text-left border-2 border-transparent hover:border-blue-300">
                <div class="flex items-center space-x-2 lg:space-x-3">
                    <div class="w-8 h-8 lg:w-10 lg:h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white text-sm lg:text-base"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-1">
                            <h4 class="font-semibold text-gray-900 text-sm lg:text-base">${member.name}</h4>
                            <span class="inline-flex items-center px-1 lg:px-2 py-1 rounded-full text-xs font-medium ${tierColor}">
                                ${member.tier_name}
                            </span>
                        </div>
                        <p class="text-xs lg:text-sm text-gray-500 mb-1"><strong>${member.member_code}</strong> • ${member.phone || 'No phone'}</p>
                        <p class="text-xs text-gray-400">${discountText} • RM ${member.total_spent.toFixed(2)} spent</p>
                    </div>
                </div>
            </button>
        `;
    });
    
    $('#members-list').html(html).removeClass('hidden');
}

function getTierColor(tierName) {
    switch(tierName.toLowerCase()) {
        case 'bronze': return 'bg-orange-100 text-orange-800';
        case 'silver': return 'bg-gray-100 text-gray-800';
        case 'gold': return 'bg-yellow-100 text-yellow-800';
        case 'platinum': return 'bg-purple-100 text-purple-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

// Quick Add Member Functions
function openQuickAddMemberModal() {
    $('#quick-add-member-modal').removeClass('hidden');
    $('body').addClass('overflow-hidden');
    loadMembershipTiers();
    generateMemberCode();
    $('#quick-member-name').focus();
}

function closeQuickAddMemberModal() {
    $('#quick-add-member-modal').addClass('hidden');
    $('body').removeClass('overflow-hidden');
    $('#quick-add-member-form')[0].reset();
}

function loadMembershipTiers() {
    $.ajax({
        url: 'ajax/get-membership-tiers.php',
        type: 'GET',
        success: function(response) {
            try {
                const data = JSON.parse(response);
                if (data.success && data.tiers) {
                    let options = '<option value="">Select Tier</option>';
                    data.tiers.forEach(function(tier) {
                        options += `<option value="${tier.id}">${tier.name} (${tier.discount_percentage}% discount)</option>`;
                    });
                    $('#quick-membership-tier').html(options);
                }
            } catch (e) {
                console.error('Error loading membership tiers:', e);
            }
        }
    });
}

function generateMemberCode() {
    // Generate a simple member code with timestamp
    const timestamp = Date.now().toString().slice(-6);
    const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
    const memberCode = `MEM${timestamp}${random}`;
    $('#quick-member-code').val(memberCode);
}

// Handle quick add member form submission
$(document).ready(function() {
    $('#quick-add-member-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: 'ajax/save-member.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    const data = JSON.parse(response);
                    if (data.success) {
                        showAlert('Member added successfully!', 'success');
                        closeQuickAddMemberModal();
                        
                        // Refresh member search in the main modal
                        const currentSearch = $('#member-search').val();
                        if (currentSearch) {
                            searchMembers(currentSearch);
                        }
                    } else {
                        showAlert(data.message, 'error');
                    }
                } catch (e) {
                    showAlert('Error processing response', 'error');
                }
            },
            error: function() {
                showAlert('Error adding member', 'error');
            }
        });
    });
});

function selectMember(memberId, memberName) {
    $('#member-select').val(memberId);
    $('#selected-member-display').val(memberName);
    closeMemberModal();
    loadCart(); // Reload cart with new member discount
}

function searchMembers(searchTerm) {
    loadMembersForModal(searchTerm);
}

// Save product
function saveProduct() {
    const formData = new FormData($('#product-form')[0]);
    
    $.ajax({
        url: 'ajax/save-product.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            const data = JSON.parse(response);
            if (data.success) {
                closeProductModal();
                loadCart();
                showAlert('Product saved successfully', 'success');
            } else {
                showAlert(data.message, 'error');
            }
        }
    });
}

// Keyboard shortcuts
$(document).keydown(function(e) {
    if (e.ctrlKey && e.keyCode === 13) { // Ctrl + Enter
        if (cart.length > 0) {
            processPayment('cash');
        }
    }
    
    // Toggle sidebar with Ctrl + B
    if (e.ctrlKey && e.keyCode === 66) { // Ctrl + B
        e.preventDefault();
        if (window.innerWidth >= 1024) {
            toggleDesktopSidebar();
        } else {
            toggleMobileSidebar();
        }
    }
    
    // Focus search with Ctrl + F
    if (e.ctrlKey && e.keyCode === 70) { // Ctrl + F
        e.preventDefault();
        const searchInput = document.getElementById('product-search');
        if (searchInput) {
            searchInput.focus();
            searchInput.select();
        }
    }
});

// Mobile-specific improvements
function isMobile() {
    return window.innerWidth <= 768;
}

// Handle mobile-specific interactions
if (isMobile()) {
    // Prevent zoom on input focus
    $('input, select, textarea').on('focus', function() {
        $(this).css('font-size', '16px');
    });
    
    // Add touch feedback
    $('button').on('touchstart', function() {
        $(this).addClass('scale-95');
    }).on('touchend touchcancel', function() {
        $(this).removeClass('scale-95');
    });
}

// Handle orientation change
$(window).on('orientationchange', function() {
    setTimeout(function() {
        // Recalculate viewport height
        let vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
        
        // Refocus on product entry if it was focused
        if ($('#product-entry').is(':focus')) {
            $('#product-entry').focus();
        }
    }, 100);
});

// Close modals when clicking outside
$(document).on('click', '#product-modal, #payment-modal, #member-modal, #quick-add-member-modal', function(e) {
    if (e.target === this) {
        if ($('#product-modal').hasClass('hidden') === false) {
            closeProductModal();
        }
        if ($('#payment-modal').hasClass('hidden') === false) {
            closePaymentModal();
        }
        if ($('#member-modal').hasClass('hidden') === false) {
            closeMemberModal();
        }
        if ($('#quick-add-member-modal').hasClass('hidden') === false) {
            closeQuickAddMemberModal();
        }
    }
});

// Close modals with Escape key
$(document).on('keydown', function(e) {
    if (e.key === 'Escape') {
        if (!$('#product-modal').hasClass('hidden')) {
            closeProductModal();
        }
        if (!$('#payment-modal').hasClass('hidden')) {
            closePaymentModal();
        }
        if (!$('#member-modal').hasClass('hidden')) {
            closeMemberModal();
        }
        if (!$('#quick-add-member-modal').hasClass('hidden')) {
            closeQuickAddMemberModal();
        }
    }
});

// Sidebar functions
function toggleCategory(categoryId) {
    const category = document.getElementById(categoryId);
    
    // Hide all other categories first
    const allCategories = document.querySelectorAll('[id^="category-"]');
    allCategories.forEach(cat => {
        if (cat.id !== categoryId) {
            cat.classList.add('hidden');
        }
    });
    
    // Toggle the selected category
    category.classList.toggle('hidden');
    
    // Update category card styling
    const categoryCards = document.querySelectorAll('.category-card');
    categoryCards.forEach(card => {
        card.classList.remove('bg-blue-50', 'border-blue-300');
    });
    
    if (!category.classList.contains('hidden')) {
        // Highlight the selected category card
        const categoryIdNum = categoryId.replace('category-', '');
        const selectedCard = document.querySelector(`[data-category-id="${categoryIdNum}"]`);
        if (selectedCard) {
            selectedCard.classList.add('bg-blue-50', 'border-blue-300');
        }
    }
}

function closeCategory(categoryId) {
    const category = document.getElementById(categoryId);
    category.classList.add('hidden');
    
    // Remove highlighting from category card
    const categoryIdNum = categoryId.replace('category-', '');
    const selectedCard = document.querySelector(`[data-category-id="${categoryIdNum}"]`);
    if (selectedCard) {
        selectedCard.classList.remove('bg-blue-50', 'border-blue-300');
    }
}

// Desktop sidebar toggle function
function toggleDesktopSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    const expandButton = document.querySelector('.sidebar-expand-button');
    
    if (sidebar && mainContent) {
        const isCollapsed = sidebar.classList.contains('collapsed');
        
        if (isCollapsed) {
            // Expand sidebar
            sidebar.classList.remove('collapsed');
            mainContent.classList.remove('expanded');
            mainContent.style.marginLeft = '45vw';
            if (expandButton) expandButton.style.display = 'none';
            console.log('Desktop sidebar expanded');
        } else {
            // Collapse sidebar
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
            mainContent.style.marginLeft = '4rem';
            if (expandButton) expandButton.style.display = 'flex';
            console.log('Desktop sidebar collapsed');
        }
    }
}

function toggleMobileSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('mobile-sidebar-overlay');
    
    if (sidebar && overlay) {
        const isHidden = sidebar.classList.contains('-translate-x-full');
        
        if (isHidden) {
            // Open sidebar
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('show');
            overlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            console.log('Sidebar opened');
        } else {
            // Close sidebar
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('show');
            overlay.classList.add('hidden');
            document.body.style.overflow = '';
            console.log('Sidebar closed');
        }
    }
}

function closeMobileSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('mobile-sidebar-overlay');
    
    if (sidebar && overlay) {
        sidebar.classList.add('-translate-x-full');
        sidebar.classList.remove('show');
        overlay.classList.add('hidden');
        document.body.style.overflow = '';
        console.log('Sidebar closed');
    }
}

function openMobileSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('mobile-sidebar-overlay');
    
    if (sidebar && overlay) {
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('show');
        overlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        console.log('Sidebar opened');
    }
}

// Handle window resize for sidebar
$(window).on('resize', function() {
    if (window.innerWidth >= 1024) {
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.getElementById('mobile-sidebar-overlay');
        const mainContent = document.querySelector('.main-content');
        
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.add('hidden');
        
        // Maintain collapsed state on desktop
        const expandButton = document.querySelector('.sidebar-expand-button');
        if (sidebar.classList.contains('collapsed')) {
            mainContent.classList.add('expanded');
            mainContent.style.marginLeft = '4rem';
            if (expandButton) expandButton.style.display = 'flex';
        } else {
            mainContent.classList.remove('expanded');
            mainContent.style.marginLeft = '45vw';
            if (expandButton) expandButton.style.display = 'none';
        }
    } else {
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');
        
        sidebar.classList.add('-translate-x-full');
        sidebar.classList.remove('collapsed');
        mainContent.classList.remove('expanded');
        mainContent.style.marginLeft = '0';
        const expandButton = document.querySelector('.sidebar-expand-button');
        if (expandButton) expandButton.style.display = 'none';
    }
});

// Keyboard navigation for category grid
document.addEventListener('keydown', function(e) {
    // ESC key to close any open category
    if (e.key === 'Escape') {
        const openCategories = document.querySelectorAll('[id^="category-"]:not(.hidden)');
        openCategories.forEach(category => {
            closeCategory(category.id);
        });
    }
});

// Auto-close categories when clicking outside (for mobile)
document.addEventListener('click', function(e) {
    if (window.innerWidth < 1024) {
        const categoryCards = document.querySelectorAll('.category-card');
        const categorySections = document.querySelectorAll('[id^="category-"]');
        const isClickOnCard = Array.from(categoryCards).some(card => card.contains(e.target));
        const isClickInCategory = Array.from(categorySections).some(section => section.contains(e.target));
        
        if (!isClickOnCard && !isClickInCategory) {
            categorySections.forEach(category => {
                category.classList.add('hidden');
            });
            categoryCards.forEach(card => {
                card.classList.remove('bg-blue-50', 'border-blue-300');
            });
        }
    }
});

// Initialize category grid
document.addEventListener('DOMContentLoaded', function() {
    // Add loading animation to category cards
    const categoryCards = document.querySelectorAll('.category-card');
    categoryCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add('animate-fade-in');
    });
    
    // Initialize product search functionality
    initializeProductSearch();
});

// Product search functionality
function initializeProductSearch() {
    const searchInput = document.getElementById('product-search');
    const searchResults = document.getElementById('search-results');
    const categoriesGrid = document.getElementById('categories-grid');
    const searchLoading = document.getElementById('search-loading');
    
    let searchTimeout;
    
    // Handle search input
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.trim();
        const clearSearchBtn = document.getElementById('clear-search');
        const searchLoading = document.getElementById('search-loading');
        
        // Clear previous timeout
        clearTimeout(searchTimeout);
        
        // Show/hide clear button based on input
        if (searchTerm !== '') {
            clearSearchBtn.classList.remove('hidden');
            searchLoading.classList.add('hidden');
        } else {
            clearSearchBtn.classList.add('hidden');
            searchLoading.classList.add('hidden');
        }
        
        // Hide results and show categories if search is empty
        if (searchTerm === '') {
            hideSearchResults();
            return;
        }
        
        // Show loading
        searchLoading.classList.remove('hidden');
        clearSearchBtn.classList.add('hidden');
        
        // Set timeout to avoid too many requests
        searchTimeout = setTimeout(() => {
            performProductSearch(searchTerm);
        }, 300);
    });
    
    // Handle search input focus
    searchInput.addEventListener('focus', function() {
        const searchTerm = this.value.trim();
        if (searchTerm !== '') {
            showSearchResults();
        }
    });
    
    // Handle click outside to close search results
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            hideSearchResults();
        }
    });
    
    // Handle escape key to clear search
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.activeElement === searchInput) {
            searchInput.value = '';
            hideSearchResults();
            searchInput.blur();
        }
    });
}

function performProductSearch(searchTerm) {
    const searchResults = document.getElementById('search-results');
    const searchLoading = document.getElementById('search-loading');
    
    $.ajax({
        url: 'ajax/search-products.php',
        type: 'GET',
        data: { search: searchTerm },
        success: function(response) {
            searchLoading.classList.add('hidden');
            
            try {
                const data = JSON.parse(response);
                
                if (data.success) {
                    if (data.count > 0) {
                        // Show search results
                        searchResults.innerHTML = data.html;
                        showSearchResults();
                    } else {
                        // Show no results message
                        searchResults.innerHTML = data.html;
                        showSearchResults();
                    }
                } else {
                    // Show error message
                    searchResults.innerHTML = `
                        <div class="text-center py-4">
                            <div class="text-sm text-red-600">${data.message}</div>
                        </div>`;
                    showSearchResults();
                }
            } catch (e) {
                console.error('Error parsing search response:', e);
                searchResults.innerHTML = `
                    <div class="text-center py-4">
                        <div class="text-sm text-red-600">Error processing search results</div>
                    </div>`;
                showSearchResults();
            }
        },
        error: function(xhr, status, error) {
            searchLoading.classList.add('hidden');
            console.error('Search error:', error);
            searchResults.innerHTML = `
                <div class="text-center py-4">
                    <div class="text-sm text-red-600">Error performing search</div>
                </div>`;
            showSearchResults();
        }
    });
}

function showSearchResults() {
    const searchResults = document.getElementById('search-results');
    const categoriesGrid = document.getElementById('categories-grid');
    
    searchResults.classList.remove('hidden');
    categoriesGrid.classList.add('hidden');
}

function hideSearchResults() {
    const searchResults = document.getElementById('search-results');
    const categoriesGrid = document.getElementById('categories-grid');
    const searchLoading = document.getElementById('search-loading');
    const clearSearchBtn = document.getElementById('clear-search');
    const searchInput = document.getElementById('product-search');
    
    searchResults.classList.add('hidden');
    categoriesGrid.classList.remove('hidden');
    searchLoading.classList.add('hidden');
    clearSearchBtn.classList.add('hidden');
    
    // Clear the search input
    if (searchInput) {
        searchInput.value = '';
    }
}

function clearSearch() {
    const searchInput = document.getElementById('product-search');
    const clearSearchBtn = document.getElementById('clear-search');
    const searchLoading = document.getElementById('search-loading');
    
    if (searchInput) {
        searchInput.value = '';
        searchInput.focus();
    }
    
    if (clearSearchBtn) {
        clearSearchBtn.classList.add('hidden');
    }
    
    if (searchLoading) {
        searchLoading.classList.add('hidden');
    }
    
    hideSearchResults();
}
</script>

<?php include 'include/footer.php'; ?>

