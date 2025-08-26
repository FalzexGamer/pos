<?php
include '../include/conn.php';

$query = mysqli_query($conn, "
    SELECT p.*, c.name as category_name, u.abbreviation as uom_abbr 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    LEFT JOIN uom u ON p.uom_id = u.id 
    WHERE p.is_active = 1 
    ORDER BY p.name
");

$html = '';
while ($product = mysqli_fetch_array($query)) {
    // Stock status styling
    $stock_class = '';
    $stock_bg_class = '';
    $stock_icon = '';
    
    if ($product['stock_quantity'] <= 0) {
        $stock_class = 'text-red-600';
        $stock_bg_class = 'bg-red-50 border-red-200';
        $stock_icon = 'fas fa-times-circle';
        $stock_text = 'Out of Stock';
    } elseif ($product['stock_quantity'] <= $product['min_stock_level']) {
        $stock_class = 'text-orange-600';
        $stock_bg_class = 'bg-orange-50 border-orange-200';
        $stock_icon = 'fas fa-exclamation-triangle';
        $stock_text = 'Low Stock';
    } else {
        $stock_class = 'text-green-600';
        $stock_bg_class = 'bg-green-50 border-green-200';
        $stock_icon = 'fas fa-check-circle';
        $stock_text = 'In Stock';
    }
    
    $html .= '
    <div class="group relative backdrop-blur-sm bg-white/70 rounded-2xl shadow-lg border border-white/20 p-6 cursor-pointer hover:shadow-xl hover:scale-105 transition-all duration-300 transform" onclick="addToCart(' . $product['id'] . ')">
        <!-- Product Icon -->
        <div class="flex items-center justify-center mb-4">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-box text-white text-2xl"></i>
            </div>
        </div>
        
        <!-- Product Info -->
        <div class="text-center space-y-3">
            <div>
                <h3 class="font-bold text-gray-900 text-sm mb-1 group-hover:text-blue-600 transition-colors duration-200">' . htmlspecialchars($product['name']) . '</h3>
                <p class="text-xs text-gray-500 font-mono">' . htmlspecialchars($product['sku']) . '</p>
            </div>
            
            <!-- Price -->
            <div class="text-center">
                <div class="text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                    RM ' . number_format($product['selling_price'], 2) . '
                </div>
                ' . ($product['cost_price'] > 0 ? '<div class="text-xs text-gray-500 mt-1">
                    Cost: RM ' . number_format($product['cost_price'], 2) . '
                </div>' : '') . '
            </div>
            
            <!-- Stock Status -->
            <div class="flex items-center justify-center">
                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium ' . $stock_bg_class . ' border ' . $stock_class . '">
                    <i class="' . $stock_icon . ' mr-1"></i>
                    ' . number_format($product['stock_quantity']) . ' ' . htmlspecialchars($product['uom_abbr']) . '
                </span>
            </div>
            
            <!-- Category -->
            <div class="flex items-center justify-center">
                <div class="w-2 h-2 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full mr-2"></div>
                <span class="text-xs text-gray-600">' . htmlspecialchars($product['category_name'] ?: 'Uncategorized') . '</span>
            </div>
        </div>
        
        <!-- Hover Overlay -->
        <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 to-indigo-500/10 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        
        <!-- Add to Cart Indicator -->
        <div class="absolute top-3 right-3 w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 transform scale-0 group-hover:scale-100">
            <i class="fas fa-plus text-white text-sm"></i>
        </div>
    </div>';
}

if (empty($html)) {
    $html = '<div class="col-span-full text-center py-16">
        <div class="max-w-md mx-auto">
            <div class="w-24 h-24 mx-auto bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-box-open text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No products found</h3>
            <p class="text-gray-500 mb-6">Start by adding products to your inventory</p>
            <button onclick="openProductModal()" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <i class="fas fa-plus mr-2"></i>Add Product
            </button>
        </div>
    </div>';
}

echo $html;
?>
