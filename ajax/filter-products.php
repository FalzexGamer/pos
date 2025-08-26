<?php
include '../include/conn.php';

$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$sort = $_GET['sort'] ?? 'name';
$stock_status = $_GET['stock_status'] ?? '';

$where_conditions = ['p.is_active = 1'];
$params = [];
$types = '';

if (!empty($search)) {
    $where_conditions[] = "(p.name LIKE ? OR p.sku LIKE ? OR p.barcode LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'sss';
}

if (!empty($status)) {
    $where_conditions[] = "p.is_active = ?";
    $params[] = $status;
    $types .= 'i';
}

if (!empty($stock_status)) {
    switch ($stock_status) {
        case 'in_stock':
            $where_conditions[] = "p.stock_quantity > p.min_stock_level AND p.stock_quantity > 0";
            break;
        case 'low_stock':
            $where_conditions[] = "p.stock_quantity <= p.min_stock_level AND p.stock_quantity > 0";
            break;
        case 'out_stock':
            $where_conditions[] = "p.stock_quantity = 0";
            break;
    }
}

$where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

// Determine sort order
$order_by = 'p.name ASC';
switch ($sort) {
    case 'sku':
        $order_by = 'p.sku ASC';
        break;
    case 'stock_quantity':
        $order_by = 'p.stock_quantity ASC';
        break;
    case 'created_at':
        $order_by = 'p.created_at DESC';
        break;
    default:
        $order_by = 'p.name ASC';
}

$sql = "SELECT p.*, c.name as category_name, u.abbreviation as uom_abbr, s.name as supplier_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        LEFT JOIN uom u ON p.uom_id = u.id 
        LEFT JOIN suppliers s ON p.supplier_id = s.id 
        $where_clause 
        ORDER BY $order_by";

if (!empty($params)) {
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        echo '<tr class="no-data"><td colspan="7" class="px-6 py-4 text-center text-red-500">Error loading products</td></tr>';
        exit;
    }
} else {
    $result = mysqli_query($conn, $sql);
}

if (!$result) {
    echo '<tr class="no-data"><td colspan="7" class="px-6 py-4 text-center text-red-500">Error loading products</td></tr>';
    exit;
}

$html = '';
$mobile_html = '';

while ($product = mysqli_fetch_array($result)) {
    // Stock status styling
    $stock_class = '';
    $stock_bg_class = '';
    $stock_icon = '';
    $stock_text = '';
    
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
    <tr class="hover:bg-gray-50/50 transition-colors duration-200">
        <td class="px-6 py-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12">
                    <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
                        <i class="fas fa-box text-blue-600 text-lg"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">' . htmlspecialchars($product['name']) . '</div>
                    <div class="text-sm text-gray-500">' . htmlspecialchars($product['category_name']) . '</div>
                </div>
            </div>
        </td>
        <td class="px-6 py-4">
            <div class="text-sm text-gray-900">' . htmlspecialchars($product['sku']) . '</div>
            <div class="text-sm text-gray-500">' . htmlspecialchars($product['barcode']) . '</div>
        </td>
        <td class="px-6 py-4 text-sm text-gray-900">' . htmlspecialchars($product['category_name']) . '</td>
        <td class="px-6 py-4">
            <div class="text-sm font-medium text-gray-900">' . $product['stock_quantity'] . ' ' . htmlspecialchars($product['uom_abbr']) . '</div>
            ' . $stock_badge . '
        </td>
        <td class="px-6 py-4 text-sm text-gray-900">$' . number_format($product['selling_price'], 2) . '</td>
        <td class="px-6 py-4">' . $status_badge . '</td>
        <td class="px-6 py-4 text-sm font-medium">
            <div class="flex items-center space-x-2">
                <button onclick="openEditModal(' . $product['id'] . ')" class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
                    <i class="fas fa-edit"></i>
                </button>
                <button onclick="deleteProduct(' . $product['id'] . ')" class="text-red-600 hover:text-red-900 transition-colors duration-200">
                    <i class="fas fa-trash"></i>
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
                        <i class="fas fa-tag mr-1"></i>' . htmlspecialchars($product['category_name']) . '
                    </p>
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
                    $' . number_format($product['selling_price'], 2) . '
                </div>
                <div class="text-xs text-gray-500">Price</div>
            </div>
        </div>
        <div class="flex items-center justify-between">
            <div class="text-xs text-gray-500">
                Cost: $' . number_format($product['cost_price'], 2) . '
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

if (mysqli_num_rows($result) == 0) {
    $html = '<tr class="no-data"><td colspan="7" class="px-6 py-4 text-center text-gray-500">No products found</td></tr>';
    $mobile_html = '<div id="mobile-empty-state" class="p-8 text-center text-gray-500">No products found</div>';
}

// Store mobile output in a hidden div for JavaScript to access
echo '<div id="mobile-data" style="display: none;">' . $mobile_html . '</div>';
echo $html;

if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}
?>
