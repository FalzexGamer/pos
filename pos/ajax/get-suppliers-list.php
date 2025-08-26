<?php
include '../include/conn.php';
include '../include/session.php';

$sql = "SELECT s.*, COUNT(p.id) as products_count 
        FROM suppliers s 
        LEFT JOIN products p ON s.id = p.supplier_id 
        GROUP BY s.id 
        ORDER BY s.name ASC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo '<tr><td colspan="6" class="px-6 py-4 text-center text-red-500">Error loading suppliers: ' . mysqli_error($conn) . '</td></tr>';
    exit;
}

$output = '';

while ($supplier = mysqli_fetch_assoc($result)) {
    $status_class = $supplier['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
    $status_text = $supplier['is_active'] ? 'Active' : 'Inactive';
    
    // Build contact information
    $contact_info = [];
    if (!empty($supplier['contact_person'])) {
        $contact_info[] = '<div class="font-medium text-gray-900">' . htmlspecialchars($supplier['contact_person']) . '</div>';
    }
    if (!empty($supplier['phone'])) {
        $contact_info[] = '<div class="text-sm text-gray-600 flex items-center"><i class="fas fa-phone mr-2"></i>' . htmlspecialchars($supplier['phone']) . '</div>';
    }
    if (!empty($supplier['email'])) {
        $contact_info[] = '<div class="text-sm text-gray-600 flex items-center"><i class="fas fa-envelope mr-2"></i>' . htmlspecialchars($supplier['email']) . '</div>';
    }
    
    $contact_html = !empty($contact_info) ? implode('', $contact_info) : '<div class="text-gray-400">No contact info</div>';
    
    $output .= '<tr class="hover:bg-gray-50/50 transition-colors duration-200">';
    
    // Supplier Name
    $output .= '<td class="px-6 py-4">';
    $output .= '<div class="flex items-center">';
    $output .= '<div class="flex-shrink-0 h-10 w-10 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center mr-4">';
    $output .= '<i class="fas fa-truck text-white text-sm"></i>';
    $output .= '</div>';
    $output .= '<div>';
    $output .= '<div class="text-sm font-semibold text-gray-900">' . htmlspecialchars($supplier['name']) . '</div>';
    $output .= '<div class="text-xs text-gray-500">ID: ' . $supplier['id'] . '</div>';
    $output .= '</div>';
    $output .= '</div>';
    $output .= '</td>';
    
    // Contact Information
    $output .= '<td class="px-6 py-4">';
    $output .= $contact_html;
    $output .= '</td>';
    
    // Address
    $output .= '<td class="px-6 py-4">';
    if (!empty($supplier['address'])) {
        $output .= '<div class="text-sm text-gray-900 max-w-xs truncate" title="' . htmlspecialchars($supplier['address']) . '">';
        $output .= '<i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>';
        $output .= htmlspecialchars($supplier['address']);
        $output .= '</div>';
    } else {
        $output .= '<div class="text-gray-400 text-sm">No address</div>';
    }
    $output .= '</td>';
    
    // Products Count
    $output .= '<td class="px-6 py-4">';
    $output .= '<div class="flex items-center">';
    $output .= '<div class="flex-shrink-0 h-8 w-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">';
    $output .= '<i class="fas fa-box text-purple-600 text-xs"></i>';
    $output .= '</div>';
    $output .= '<div>';
    $output .= '<div class="text-sm font-semibold text-gray-900">' . $supplier['products_count'] . '</div>';
    $output .= '<div class="text-xs text-gray-500">products</div>';
    $output .= '</div>';
    $output .= '</div>';
    $output .= '</td>';
    
    // Status
    $output .= '<td class="px-6 py-4">';
    $output .= '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ' . $status_class . '">';
    $output .= '<i class="fas fa-circle mr-1.5 text-xs"></i>';
    $output .= $status_text;
    $output .= '</span>';
    $output .= '</td>';
    
    // Actions
    $output .= '<td class="px-6 py-4">';
    $output .= '<div class="flex items-center space-x-2">';
    $output .= '<button onclick="openEditModal(' . $supplier['id'] . ')" class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors duration-200 text-sm font-medium">';
    $output .= '<i class="fas fa-edit mr-1.5"></i>';
    $output .= 'Edit';
    $output .= '</button>';
    $output .= '<button onclick="deleteSupplier(' . $supplier['id'] . ')" class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors duration-200 text-sm font-medium">';
    $output .= '<i class="fas fa-trash mr-1.5"></i>';
    $output .= 'Delete';
    $output .= '</button>';
    $output .= '</div>';
    $output .= '</td>';
    
    $output .= '</tr>';
    
    // Generate mobile card for this supplier
    $mobile_card = '<div class="p-4 border-b border-gray-200/50 last:border-b-0">';
    $mobile_card .= '<div class="flex items-start justify-between mb-3">';
    $mobile_card .= '<div class="flex items-center space-x-3">';
    $mobile_card .= '<div class="flex-shrink-0 h-10 w-10 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">';
    $mobile_card .= '<i class="fas fa-truck text-white text-sm"></i>';
    $mobile_card .= '</div>';
    $mobile_card .= '<div class="flex-1 min-w-0">';
    $mobile_card .= '<h4 class="text-sm font-semibold text-gray-900 truncate">' . htmlspecialchars($supplier['name']) . '</h4>';
    $mobile_card .= '<p class="text-xs text-gray-500">ID: ' . $supplier['id'] . '</p>';
    $mobile_card .= '</div>';
    $mobile_card .= '</div>';
    $mobile_card .= '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ' . $status_class . '">';
    $mobile_card .= '<i class="fas fa-circle mr-1 text-xs"></i>';
    $mobile_card .= $status_text;
    $mobile_card .= '</span>';
    $mobile_card .= '</div>';
    
    // Contact info
    if (!empty($supplier['contact_person']) || !empty($supplier['phone']) || !empty($supplier['email'])) {
        $mobile_card .= '<div class="mb-3">';
        if (!empty($supplier['contact_person'])) {
            $mobile_card .= '<div class="text-xs text-gray-600 mb-1"><i class="fas fa-user mr-1"></i>' . htmlspecialchars($supplier['contact_person']) . '</div>';
        }
        if (!empty($supplier['phone'])) {
            $mobile_card .= '<div class="text-xs text-gray-600 mb-1"><i class="fas fa-phone mr-1"></i>' . htmlspecialchars($supplier['phone']) . '</div>';
        }
        if (!empty($supplier['email'])) {
            $mobile_card .= '<div class="text-xs text-gray-600 mb-1"><i class="fas fa-envelope mr-1"></i>' . htmlspecialchars($supplier['email']) . '</div>';
        }
        $mobile_card .= '</div>';
    }
    
    // Address
    if (!empty($supplier['address'])) {
        $mobile_card .= '<div class="mb-3">';
        $mobile_card .= '<div class="text-xs text-gray-600"><i class="fas fa-map-marker-alt mr-1"></i>' . htmlspecialchars(substr($supplier['address'], 0, 50)) . (strlen($supplier['address']) > 50 ? '...' : '') . '</div>';
        $mobile_card .= '</div>';
    }
    
    // Products count
    $mobile_card .= '<div class="flex items-center justify-between mb-3">';
    $mobile_card .= '<div class="flex items-center">';
    $mobile_card .= '<div class="flex-shrink-0 h-6 w-6 bg-purple-100 rounded-lg flex items-center justify-center mr-2">';
    $mobile_card .= '<i class="fas fa-box text-purple-600 text-xs"></i>';
    $mobile_card .= '</div>';
    $mobile_card .= '<span class="text-xs text-gray-600">' . $supplier['products_count'] . ' products</span>';
    $mobile_card .= '</div>';
    $mobile_card .= '</div>';
    
    // Actions
    $mobile_card .= '<div class="flex items-center space-x-2">';
    $mobile_card .= '<button onclick="openEditModal(' . $supplier['id'] . ')" class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors duration-200 text-xs font-medium">';
    $mobile_card .= '<i class="fas fa-edit mr-1.5"></i>';
    $mobile_card .= 'Edit';
    $mobile_card .= '</button>';
    $mobile_card .= '<button onclick="deleteSupplier(' . $supplier['id'] . ')" class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors duration-200 text-xs font-medium">';
    $mobile_card .= '<i class="fas fa-trash mr-1.5"></i>';
    $mobile_card .= 'Delete';
    $mobile_card .= '</button>';
    $mobile_card .= '</div>';
    $mobile_card .= '</div>';
    
    // Store mobile card in a script tag for JavaScript to access
    $output .= '<script class="mobile-card" data-supplier-id="' . $supplier['id'] . '" type="text/template">' . $mobile_card . '</script>';
}

if (mysqli_num_rows($result) == 0) {
    $output = '<tr class="no-data"><td colspan="6" class="px-6 py-8 text-center">';
    $output .= '<div class="flex flex-col items-center">';
    $output .= '<i class="fas fa-truck text-4xl text-gray-300 mb-4"></i>';
    $output .= '<div class="text-gray-500 text-lg font-medium">No suppliers found</div>';
    $output .= '<div class="text-gray-400 text-sm">Add your first supplier to get started</div>';
    $output .= '</div>';
    $output .= '</td></tr>';
    
    // Also generate mobile empty state
    $mobile_output = '<div class="p-8 text-center">';
    $mobile_output .= '<div class="flex flex-col items-center">';
    $mobile_output .= '<i class="fas fa-truck text-3xl text-gray-300 mb-3"></i>';
    $mobile_output .= '<div class="text-gray-500 text-base font-medium">No suppliers found</div>';
    $mobile_output .= '<div class="text-gray-400 text-xs">Add your first supplier to get started</div>';
    $mobile_output .= '</div>';
    $mobile_output .= '</div>';
    
    // Store mobile output in a script tag for JavaScript to access
    $output .= '<script id="mobile-empty-state" type="text/template">' . $mobile_output . '</script>';
}

echo $output;
?>
