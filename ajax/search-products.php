<?php
include '../include/conn.php';
include '../include/session.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get search term
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';

if (empty($search_term)) {
    echo json_encode(['success' => false, 'message' => 'Search term is required']);
    exit;
}

try {
    // Prepare the search query
    $search_term = mysqli_real_escape_string($conn, $search_term);
    
    // Search in products table by name, SKU, or barcode
    $query = "SELECT p.id, p.name, p.sku, p.barcode, p.selling_price, p.stock_quantity, 
                     c.name as category_name, c.id as category_id
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              WHERE p.is_active = 1 
              AND (p.name LIKE '%$search_term%' 
                   OR p.sku LIKE '%$search_term%' 
                   OR p.barcode LIKE '%$search_term%')
              ORDER BY p.name ASC 
              LIMIT 20";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        throw new Exception("Database query failed: " . mysqli_error($conn));
    }
    
    $products = [];
    $html = '';
    
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
            
            // Generate HTML for each product
            $stock_status = $row['stock_quantity'] > 0 ? 'text-green-600' : 'text-red-600';
            $stock_text = $row['stock_quantity'] > 0 ? 'In Stock' : 'Out of Stock';
            
            $html .= '
            <button onclick="addProductToCartFromSidebar(' . $row['id'] . ')" 
                    class="w-full flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg hover:bg-green-50 hover:border-green-300 hover:shadow-md transition-all duration-200 transform hover:-translate-y-1">
                <div class="flex items-center space-x-3">
                    ' . ($row['img'] && $row['img'] !== '-' ? 
                        '<img src="uploads/products/' . htmlspecialchars($row['img']) . '" alt="' . htmlspecialchars($row['name']) . '" class="w-12 h-12 rounded-lg object-cover border border-gray-200">' :
                        '<div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-box text-white text-sm"></i>
                        </div>') . '
                    <div class="text-left">
                        <div class="font-medium text-gray-900 text-sm">' . htmlspecialchars($row['name']) . '</div>
                        <div class="text-xs text-gray-500">' . htmlspecialchars($row['sku']) . '</div>
                        <div class="text-xs text-gray-400">' . htmlspecialchars($row['category_name']) . '</div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="font-semibold text-green-600 text-sm">RM ' . number_format($row['selling_price'], 2) . '</div>
                    <div class="text-xs ' . $stock_status . '">' . $stock_text . '</div>
                    <div class="text-xs text-gray-400">Stock: ' . $row['stock_quantity'] . '</div>
                </div>
            </button>';
        }
        
        echo json_encode([
            'success' => true,
            'products' => $products,
            'html' => $html,
            'count' => count($products)
        ]);
    } else {
        // No products found
        $html = '
        <div class="text-center py-6">
            <div class="w-12 h-12 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-search text-gray-400 text-lg"></i>
            </div>
            <h3 class="text-sm font-medium text-gray-900 mb-1">No Products Found</h3>
            <p class="text-xs text-gray-500">Try adjusting your search terms</p>
        </div>';
        
        echo json_encode([
            'success' => true,
            'products' => [],
            'html' => $html,
            'count' => 0
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

mysqli_close($conn);
?>
