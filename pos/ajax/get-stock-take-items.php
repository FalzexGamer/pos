<?php
include '../include/conn.php';
include '../include/session.php';

if (!isset($_GET['session_id'])) {
    echo '<tr><td colspan="6" class="px-6 py-4 text-center text-red-500">Session ID is required</td></tr>';
    exit;
}

$session_id = $_GET['session_id'];

$sql = "SELECT sti.*, p.name as product_name, p.sku, u.name as uom_name 
        FROM stock_take_items sti 
        LEFT JOIN products p ON sti.product_id = p.id 
        LEFT JOIN uom u ON p.uom_id = u.id 
        WHERE sti.session_id = ? 
        ORDER BY p.name ASC";

$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $session_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    echo '<tr><td colspan="6" class="px-6 py-4 text-center text-red-500">Error loading items</td></tr>';
    exit;
}

if (!$result) {
    echo '<tr><td colspan="6" class="px-6 py-4 text-center text-red-500">Error loading items</td></tr>';
    exit;
}

$output = '';

while ($item = mysqli_fetch_assoc($result)) {
    $difference_class = '';
    if ($item['difference'] > 0) {
        $difference_class = 'text-green-600';
    } elseif ($item['difference'] < 0) {
        $difference_class = 'text-red-600';
    }
    
    $output .= '<tr class="hover:bg-gray-50">';
    $output .= '<td class="px-6 py-4 whitespace-nowrap">';
    $output .= '<div class="text-sm font-medium text-gray-900">' . htmlspecialchars($item['product_name']) . '</div>';
    $output .= '</td>';
    $output .= '<td class="px-6 py-4 whitespace-nowrap">';
    $output .= '<div class="text-sm text-gray-900">' . htmlspecialchars($item['sku']) . '</div>';
    $output .= '</td>';
    $output .= '<td class="px-6 py-4 whitespace-nowrap">';
    $output .= '<div class="text-sm text-gray-900">' . $item['system_quantity'] . ' ' . htmlspecialchars($item['uom_name']) . '</div>';
    $output .= '</td>';
    $output .= '<td class="px-6 py-4 whitespace-nowrap">';
    $output .= '<div class="text-sm text-gray-900">' . $item['counted_quantity'] . ' ' . htmlspecialchars($item['uom_name']) . '</div>';
    $output .= '</td>';
    $output .= '<td class="px-6 py-4 whitespace-nowrap">';
    $output .= '<div class="text-sm ' . $difference_class . '">' . ($item['difference'] > 0 ? '+' : '') . $item['difference'] . ' ' . htmlspecialchars($item['uom_name']) . '</div>';
    $output .= '</td>';
    $output .= '<td class="px-6 py-4">';
    $output .= '<div class="text-sm text-gray-900">' . htmlspecialchars($item['notes'] ?: '-') . '</div>';
    $output .= '</td>';
    $output .= '</tr>';
}

if (mysqli_num_rows($result) == 0) {
    $output = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No items counted in this session</td></tr>';
}

echo $output;

mysqli_stmt_close($stmt);
?>
