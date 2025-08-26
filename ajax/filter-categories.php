<?php
include '../include/conn.php';
include '../include/session.php';

$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';

$where_conditions = [];
$params = [];
$types = '';

if (!empty($search)) {
    $where_conditions[] = "(c.name LIKE ? OR c.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ss';
}

if ($status !== '') {
    $where_conditions[] = "c.is_active = ?";
    $params[] = $status;
    $types .= 'i';
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

$sql = "SELECT c.*, COUNT(p.id) as products_count 
        FROM categories c 
        LEFT JOIN products p ON c.id = p.category_id 
        $where_clause
        GROUP BY c.id 
        ORDER BY c.name ASC";

if (!empty($params)) {
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        echo '<tr class="no-data">
            <td colspan="5" class="px-6 py-12 text-center">
                <div class="flex flex-col items-center space-y-4">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-2xl text-red-400"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Error loading categories</h3>
                        <p class="text-sm text-gray-500 mt-1">Please try refreshing the page</p>
                    </div>
                </div>
            </td>
        </tr>';
        exit;
    }
} else {
    $result = mysqli_query($conn, $sql);
}

if (!$result) {
    echo '<tr class="no-data">
        <td colspan="5" class="px-6 py-12 text-center">
            <div class="flex flex-col items-center space-y-4">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-2xl text-red-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Error loading categories</h3>
                    <p class="text-sm text-gray-500 mt-1">Please try refreshing the page</p>
                </div>
            </div>
        </td>
    </tr>';
    exit;
}

$output = '';

while ($category = mysqli_fetch_assoc($result)) {
    $status_class = $category['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
    $status_text = $category['is_active'] ? 'Active' : 'Inactive';
    
    $output .= '<tr class="group hover:bg-gradient-to-r hover:from-green-50/50 hover:to-emerald-50/50 transition-all duration-200 border-b border-gray-100/50">';
    $output .= '<td class="px-6 py-5">';
    $output .= '<div class="text-sm font-semibold text-gray-900 group-hover:text-green-600 transition-colors duration-200">' . htmlspecialchars($category['name']) . '</div>';
    $output .= '</td>';
    $output .= '<td class="px-6 py-5">';
    $output .= '<div class="text-sm text-gray-900">' . htmlspecialchars($category['description'] ?: '-') . '</div>';
    $output .= '</td>';
    $output .= '<td class="px-6 py-5 whitespace-nowrap">';
    $output .= '<div class="flex items-center space-x-2">';
    $output .= '<div class="w-2 h-2 bg-gradient-to-r from-purple-400 to-pink-500 rounded-full"></div>';
    $output .= '<span class="text-sm font-semibold text-gray-900">' . $category['products_count'] . ' products</span>';
    $output .= '</div>';
    $output .= '</td>';
    $output .= '<td class="px-6 py-5 whitespace-nowrap">';
    $output .= '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ' . $status_class . ' border border-' . ($category['is_active'] ? 'green' : 'red') . '-200">';
    $output .= '<i class="fas fa-' . ($category['is_active'] ? 'check' : 'times') . '-circle mr-1"></i>' . $status_text;
    $output .= '</span>';
    $output .= '</td>';
    $output .= '<td class="px-6 py-5 whitespace-nowrap">';
    $output .= '<div class="flex items-center space-x-2">';
    $output .= '<button onclick="openEditModal(' . $category['id'] . ')" ';
    $output .= 'class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 hover:border-blue-300 transition-all duration-200 group">';
    $output .= '<i class="fas fa-edit mr-1 group-hover:scale-110 transition-transform duration-200"></i>';
    $output .= 'Edit';
    $output .= '</button>';
    $output .= '<button onclick="deleteCategory(' . $category['id'] . ')" ';
    $output .= 'class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 hover:border-red-300 transition-all duration-200 group">';
    $output .= '<i class="fas fa-trash mr-1 group-hover:scale-110 transition-transform duration-200"></i>';
    $output .= 'Delete';
    $output .= '</button>';
    $output .= '</div>';
    $output .= '</td>';
    $output .= '</tr>';
}

if (mysqli_num_rows($result) == 0) {
    $output = '<tr class="no-data">
        <td colspan="5" class="px-6 py-12 text-center">
            <div class="flex flex-col items-center space-y-4">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-tags text-2xl text-gray-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">No categories found</h3>
                    <p class="text-sm text-gray-500 mt-1">Try adjusting your search criteria</p>
                </div>
            </div>
        </td>
    </tr>';
}

echo $output;

if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}
?>
