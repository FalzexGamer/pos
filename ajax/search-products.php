<?php
include '../include/conn.php';

$search = $_GET['search'] ?? '';

if (empty($search)) {
    echo '<div class="col-span-full text-center py-8 text-gray-500">Enter search term</div>';
    exit;
}

$query = mysqli_query($conn, "
    SELECT p.*, c.name as category_name, u.abbreviation as uom_abbr 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    LEFT JOIN uom u ON p.uom_id = u.id 
    WHERE p.is_active = 1 
    AND (p.name LIKE '%$search%' OR p.sku LIKE '%$search%' OR p.barcode LIKE '%$search%')
    ORDER BY p.name
");

$html = '';
while ($product = mysqli_fetch_array($query)) {
    $stock_class = $product['stock_quantity'] <= $product['min_stock_level'] ? 'text-red-600' : 'text-green-600';
    
    $html .= '
    <div class="bg-white rounded-lg shadow p-4 cursor-pointer hover:shadow-lg transition-shadow" onclick="addToCart(' . $product['id'] . ')">
        <div class="text-center">
            <div class="w-16 h-16 bg-gray-200 rounded-lg mx-auto mb-3 flex items-center justify-center">
                <i class="fas fa-box text-2xl text-gray-500"></i>
            </div>
            <h3 class="font-medium text-gray-900 text-sm mb-1">' . $product['name'] . '</h3>
            <p class="text-xs text-gray-500 mb-2">' . $product['sku'] . '</p>
            <div class="flex justify-between items-center mb-2">
                <span class="text-lg font-bold text-blue-600">RM ' . number_format($product['selling_price'], 2) . '</span>
                <span class="text-xs ' . $stock_class . '">' . $product['stock_quantity'] . ' ' . $product['uom_abbr'] . '</span>
            </div>
            <div class="text-xs text-gray-500">
                ' . $product['category_name'] . '
            </div>
        </div>
    </div>';
}

if (empty($html)) {
    $html = '<div class="col-span-full text-center py-8 text-gray-500">No products found for "' . htmlspecialchars($search) . '"</div>';
}

echo $html;
?>
