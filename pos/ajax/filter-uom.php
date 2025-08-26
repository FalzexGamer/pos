<?php
include '../include/conn.php';
include '../include/session.php';

$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';

$where_conditions = [];
$params = [];
$types = '';

if (!empty($search)) {
    $where_conditions[] = "(u.name LIKE ? OR u.abbreviation LIKE ? OR u.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'sss';
}

if ($status !== '') {
    $where_conditions[] = "u.is_active = ?";
    $params[] = $status;
    $types .= 'i';
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

$sql = "SELECT u.*, COUNT(p.id) as products_count 
        FROM uom u 
        LEFT JOIN products p ON u.id = p.uom_id 
        $where_clause
        GROUP BY u.id 
        ORDER BY u.name ASC";

if (!empty($params)) {
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        echo '<tr><td colspan="6" class="px-6 py-4 text-center text-red-500">Error loading UOM</td></tr>';
        exit;
    }
} else {
    $result = mysqli_query($conn, $sql);
}

if (!$result) {
    echo '<tr><td colspan="6" class="px-6 py-4 text-center text-red-500">Error loading UOM</td></tr>';
    exit;
}

$output = '';
$mobile_output = '';

while ($uom = mysqli_fetch_assoc($result)) {
    $status_class = $uom['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
    $status_text = $uom['is_active'] ? 'Active' : 'Inactive';
    
    // Desktop table row
    $output .= '<tr class="hover:bg-gray-50">';
    $output .= '<td class="px-6 py-4 whitespace-nowrap">';
    $output .= '<div class="text-sm font-medium text-gray-900">' . htmlspecialchars($uom['name']) . '</div>';
    $output .= '</td>';
    $output .= '<td class="px-6 py-4 whitespace-nowrap">';
    $output .= '<div class="text-sm text-gray-900">' . htmlspecialchars($uom['abbreviation'] ?: '-') . '</div>';
    $output .= '</td>';
    $output .= '<td class="px-6 py-4">';
    $output .= '<div class="text-sm text-gray-900">' . htmlspecialchars($uom['description'] ?: '-') . '</div>';
    $output .= '</td>';
    $output .= '<td class="px-6 py-4 whitespace-nowrap">';
    $output .= '<div class="text-sm text-gray-900">' . $uom['products_count'] . '</div>';
    $output .= '</td>';
    $output .= '<td class="px-6 py-4 whitespace-nowrap">';
    $output .= '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ' . $status_class . '">' . $status_text . '</span>';
    $output .= '</td>';
    $output .= '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium">';
    $output .= '<div class="flex items-center space-x-2">';
    $output .= '<button onclick="openEditModal(' . $uom['id'] . ')" class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors duration-200 text-xs font-medium">';
    $output .= '<i class="fas fa-edit mr-1"></i>Edit';
    $output .= '</button>';
    $output .= '<button onclick="deleteUOM(' . $uom['id'] . ')" class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors duration-200 text-xs font-medium">';
    $output .= '<i class="fas fa-trash mr-1"></i>Delete';
    $output .= '</button>';
    $output .= '</div>';
    $output .= '</td>';
    $output .= '</tr>';
    
    // Mobile card
    $mobile_output .= '<div class="mobile-card p-4 bg-white/80 backdrop-blur-sm border-b border-gray-200/50 last:border-b-0">';
    $mobile_output .= '<div class="flex items-start justify-between mb-3">';
    $mobile_output .= '<div class="flex-1">';
    $mobile_output .= '<h4 class="text-lg font-semibold text-gray-900 mb-1">' . htmlspecialchars($uom['name']) . '</h4>';
    $mobile_output .= '<p class="text-sm text-gray-600 mb-2">' . htmlspecialchars($uom['abbreviation'] ?: 'No abbreviation') . '</p>';
    $mobile_output .= '<p class="text-sm text-gray-700 mb-3">' . htmlspecialchars($uom['description'] ?: 'No description') . '</p>';
    $mobile_output .= '</div>';
    $mobile_output .= '<span class="px-2 py-1 text-xs font-semibold rounded-full ' . $status_class . ' ml-2">' . $status_text . '</span>';
    $mobile_output .= '</div>';
    $mobile_output .= '<div class="flex items-center justify-between">';
    $mobile_output .= '<div class="flex items-center space-x-4 text-sm text-gray-600">';
    $mobile_output .= '<span><i class="fas fa-box mr-1"></i>' . $uom['products_count'] . ' products</span>';
    $mobile_output .= '</div>';
    $mobile_output .= '<div class="flex items-center space-x-2">';
    $mobile_output .= '<button onclick="openEditModal(' . $uom['id'] . ')" class="inline-flex items-center px-3 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors duration-200 text-sm font-medium">';
    $mobile_output .= '<i class="fas fa-edit mr-1"></i>Edit';
    $mobile_output .= '</button>';
    $mobile_output .= '<button onclick="deleteUOM(' . $uom['id'] . ')" class="inline-flex items-center px-3 py-2 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors duration-200 text-sm font-medium">';
    $mobile_output .= '<i class="fas fa-trash mr-1"></i>Delete';
    $mobile_output .= '</button>';
    $mobile_output .= '</div>';
    $mobile_output .= '</div>';
    $mobile_output .= '</div>';
}

if (mysqli_num_rows($result) == 0) {
    $output = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No UOM found</td></tr>';
    $mobile_output = '<div id="mobile-empty-state" class="p-8 text-center text-gray-500">No UOMs found</div>';
}

// Store mobile output in a hidden div for JavaScript to access
echo '<div id="mobile-data" style="display: none;">' . $mobile_output . '</div>';
echo $output;

if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}
?>
