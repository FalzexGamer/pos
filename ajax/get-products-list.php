<?php
include '../include/conn.php';

$query = mysqli_query($conn, "
    SELECT p.*, c.name as category_name, u.abbreviation as uom_abbr, s.name as supplier_name
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    LEFT JOIN uom u ON p.uom_id = u.id 
    LEFT JOIN suppliers s ON p.supplier_id = s.id
    ORDER BY p.name
");

$html = '';
$mobile_html = '';

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
    
    // Status badge styling
    $status_badge = $product['is_active'] ? 
        '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
            <i class="fas fa-check-circle mr-1"></i>Active
        </span>' : 
        '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
            <i class="fas fa-times-circle mr-1"></i>Inactive
        </span>';
    
    // Stock quantity badge
    $stock_badge = '<span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium ' . $stock_bg_class . ' border ' . $stock_class . '">
        <i class="' . $stock_icon . ' mr-1"></i>' . $stock_text . '
    </span>';
    
    // Desktop table row
    $html .= '
    <tr class="group hover:bg-gradient-to-r hover:from-blue-50/50 hover:to-indigo-50/50 transition-all duration-200 border-b border-gray-100/50">
        <td class="px-6 py-5">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-box text-white text-sm"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 transition-colors duration-200">
                        ' . htmlspecialchars($product['name']) . '
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-barcode mr-1"></i>' . htmlspecialchars($product['sku']) . '
                    </div>
                    ' . ($product['barcode'] ? '<div class="text-xs text-gray-400 mt-1">
                        <i class="fas fa-qrcode mr-1"></i>' . htmlspecialchars($product['barcode']) . '
                    </div>' : '') . '
                </div>
            </div>
        </td>
        <td class="px-6 py-5">
            <div class="space-y-1">
                <div class="text-sm font-medium text-gray-900">' . htmlspecialchars($product['sku']) . '</div>
                ' . ($product['barcode'] ? '<div class="text-xs text-gray-500 bg-gray-50 px-2 py-1 rounded-md inline-block">
                    <i class="fas fa-qrcode mr-1"></i>' . htmlspecialchars($product['barcode']) . '
                </div>' : '<div class="text-xs text-gray-400 italic">No barcode</div>') . '
            </div>
        </td>
        <td class="px-6 py-5">
            <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full"></div>
                <span class="text-sm font-medium text-gray-900">' . htmlspecialchars($product['category_name'] ?: 'Uncategorized') . '</span>
            </div>
            ' . ($product['supplier_name'] ? '<div class="text-xs text-gray-500 mt-1">
                <i class="fas fa-truck mr-1"></i>' . htmlspecialchars($product['supplier_name']) . '
            </div>' : '') . '
        </td>
        <td class="px-6 py-5">
            <div class="space-y-2">
                ' . $stock_badge . '
                <div class="text-sm font-semibold ' . $stock_class . '">
                    ' . number_format($product['stock_quantity']) . ' ' . htmlspecialchars($product['uom_abbr']) . '
                </div>
                ' . ($product['min_stock_level'] > 0 ? '<div class="text-xs text-gray-500">
                    Min: ' . number_format($product['min_stock_level']) . ' ' . htmlspecialchars($product['uom_abbr']) . '
                </div>' : '') . '
            </div>
        </td>
        <td class="px-6 py-5">
            <div class="space-y-1">
                <div class="text-sm font-bold text-gray-900">
                    RM ' . number_format($product['selling_price'], 2) . '
                </div>
                <div class="text-xs text-gray-500">
                    Cost: RM ' . number_format($product['cost_price'], 2) . '
                </div>
                ' . ($product['selling_price'] > $product['cost_price'] ? '<div class="text-xs text-green-600 font-medium">
                    <i class="fas fa-arrow-up mr-1"></i>+' . number_format((($product['selling_price'] - $product['cost_price']) / $product['cost_price']) * 100, 1) . '% margin
                </div>' : '') . '
            </div>
        </td>
        <td class="px-6 py-5">
            ' . $status_badge . '
        </td>
        <td class="px-6 py-5">
            <div class="flex items-center space-x-2">
                <button onclick="openEditModal(' . $product['id'] . ')" 
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 hover:border-blue-300 transition-all duration-200 group">
                    <i class="fas fa-edit mr-1 group-hover:scale-110 transition-transform duration-200"></i>
                    Edit
                </button>
                <button onclick="deleteProduct(' . $product['id'] . ')" 
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 hover:border-red-300 transition-all duration-200 group">
                    <i class="fas fa-trash mr-1 group-hover:scale-110 transition-transform duration-200"></i>
                    Delete
                </button>
            </div>
        </td>
    </tr>';
    
    // Mobile card
    $mobile_html .= '<div class="mobile-card p-4 bg-white/80 backdrop-blur-sm border-b border-gray-200/50 last:border-b-0">
        <div class="flex items-start justify-between mb-3">
            <div class="flex-1">
                <div class="flex items-center space-x-3 mb-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-box text-white text-sm"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900">' . htmlspecialchars($product['name']) . '</h4>
                        <p class="text-sm text-gray-600">' . htmlspecialchars($product['sku']) . '</p>
                    </div>
                </div>
                <div class="space-y-1 mb-3">
                    <p class="text-sm text-gray-700">
                        <i class="fas fa-tag mr-1"></i>' . htmlspecialchars($product['category_name'] ?: 'Uncategorized') . '
                    </p>
                    ' . ($product['supplier_name'] ? '<p class="text-sm text-gray-600">
                        <i class="fas fa-truck mr-1"></i>' . htmlspecialchars($product['supplier_name']) . '
                    </p>' : '') . '
                    ' . ($product['barcode'] ? '<p class="text-sm text-gray-500">
                        <i class="fas fa-qrcode mr-1"></i>' . htmlspecialchars($product['barcode']) . '
                    </p>' : '') . '
                </div>
            </div>
            <div class="flex flex-col items-end space-y-2">
                ' . $status_badge . '
                ' . $stock_badge . '
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4 mb-3">
            <div class="text-center p-2 bg-gray-50 rounded-lg">
                <div class="text-sm font-semibold ' . $stock_class . '">
                    ' . number_format($product['stock_quantity']) . ' ' . htmlspecialchars($product['uom_abbr']) . '
                </div>
                <div class="text-xs text-gray-500">Stock</div>
            </div>
            <div class="text-center p-2 bg-gray-50 rounded-lg">
                <div class="text-sm font-bold text-gray-900">
                    RM ' . number_format($product['selling_price'], 2) . '
                </div>
                <div class="text-xs text-gray-500">Price</div>
            </div>
        </div>
        <div class="flex items-center justify-between">
            <div class="text-xs text-gray-500">
                Cost: RM ' . number_format($product['cost_price'], 2) . '
            </div>
            <div class="flex items-center space-x-2">
                <button onclick="openEditModal(' . $product['id'] . ')" class="inline-flex items-center px-3 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors duration-200 text-sm font-medium">
                    <i class="fas fa-edit mr-1"></i>Edit
                </button>
                <button onclick="deleteProduct(' . $product['id'] . ')" class="inline-flex items-center px-3 py-2 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors duration-200 text-sm font-medium">
                    <i class="fas fa-trash mr-1"></i>Delete
                </button>
            </div>
        </div>
    </div>';
}

if (empty($html)) {
    $html = '<tr class="no-data">
        <td colspan="7" class="px-6 py-12 text-center">
            <div class="flex flex-col items-center space-y-4">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-box-open text-2xl text-gray-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">No products found</h3>
                    <p class="text-sm text-gray-500 mt-1">Get started by adding your first product</p>
                </div>
            </div>
        </td>
    </tr>';
    $mobile_html = '<div id="mobile-empty-state" class="p-8 text-center text-gray-500">No products found</div>';
}

// Store mobile output in a hidden div for JavaScript to access
echo '<div id="mobile-data" style="display: none;">' . $mobile_html . '</div>';
echo $html;
?>
