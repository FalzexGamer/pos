<?php
include '../include/conn.php';
include '../include/session.php';

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
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

if (!empty($category)) {
    $where_conditions[] = "p.category_id = ?";
    $params[] = $category;
    $types .= 'i';
}

if (!empty($stock_status)) {
    switch ($stock_status) {
        case 'in_stock':
            $where_conditions[] = "p.stock_quantity > 0";
            break;
        case 'low_stock':
            $where_conditions[] = "p.stock_quantity <= p.min_stock_level AND p.stock_quantity > 0";
            break;
        case 'out_of_stock':
            $where_conditions[] = "p.stock_quantity = 0";
            break;
    }
}

$where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

$sql = "SELECT p.*, c.name as category_name, u.name as uom_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        LEFT JOIN uom u ON p.uom_id = u.id 
        $where_clause
        ORDER BY p.name ASC 
        LIMIT 50";

if (!empty($params)) {
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        echo '<tr><td colspan="6" class="px-4 py-2 text-center text-red-500">Error loading products</td></tr>';
        exit;
    }
} else {
    $result = mysqli_query($conn, $sql);
}

if (!$result) {
    echo '<tr><td colspan="6" class="px-4 py-2 text-center text-red-500">Error loading products</td></tr>';
    exit;
}

$output = '';

while ($product = mysqli_fetch_assoc($result)) {
    $stock_class = '';
    if ($product['stock_quantity'] == 0) {
        $stock_class = 'text-red-600';
    } elseif ($product['stock_quantity'] <= $product['min_stock_level']) {
        $stock_class = 'text-yellow-600';
    } else {
        $stock_class = 'text-green-600';
    }
    
    $output .= '<tr class="hover:bg-gray-50">';
    $output .= '<td class="px-4 py-2">';
    $output .= '<div class="text-sm font-medium text-gray-900">' . htmlspecialchars($product['name']) . '</div>';
    $output .= '<div class="text-xs text-gray-500">' . htmlspecialchars($product['category_name']) . '</div>';
    $output .= '</td>';
    $output .= '<td class="px-4 py-2 text-sm text-gray-900">' . htmlspecialchars($product['sku']) . '</td>';
    $output .= '<td class="px-4 py-2 text-sm ' . $stock_class . '">' . $product['stock_quantity'] . ' ' . htmlspecialchars($product['uom_name']) . '</td>';
    $output .= '<td class="px-4 py-2 text-sm text-gray-900">' . $product['stock_quantity'] . '</td>';
    $output .= '<td class="px-4 py-2 text-sm text-gray-900">0</td>';
    $output .= '<td class="px-4 py-2 text-sm font-medium">';
    $output .= '<button onclick="openStockCountModal(' . $product['id'] . ', 0, \'' . addslashes($product['name']) . '\', \'' . $product['sku'] . '\', ' . $product['stock_quantity'] . ')" class="text-blue-600 hover:text-blue-900">Count</button>';
    $output .= '</td>';
    $output .= '</tr>';
}

if (mysqli_num_rows($result) == 0) {
    $output = '<tr><td colspan="6" class="px-4 py-2 text-center text-gray-500">No products found</td></tr>';
}

echo $output;

if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}
?>
