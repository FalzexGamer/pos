<?php
include '../include/conn.php';

// Get all members with their tier information
$query = "SELECT m.*, mt.name as tier_name 
          FROM members m 
          LEFT JOIN membership_tiers mt ON m.membership_tier_id = mt.id 
          ORDER BY m.name ASC";
$result = mysqli_query($conn, $query);

if (!$result) {
    echo '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Error loading members</td></tr>';
    exit;
}

if (mysqli_num_rows($result) == 0) {
    echo '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No members found</td></tr>';
    exit;
}

while ($member = mysqli_fetch_assoc($result)) {
    $status_class = $member['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
    $status_text = $member['is_active'] ? 'Active' : 'Inactive';
    
    echo '<tr class="hover:bg-gray-50">';
    echo '<td class="px-6 py-4 whitespace-nowrap">';
    echo '<div class="flex items-center">';
    echo '<div class="flex-shrink-0 h-10 w-10">';
    echo '<div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">';
    echo '<i class="fas fa-user text-blue-600"></i>';
    echo '</div>';
    echo '</div>';
    echo '<div class="ml-4">';
    echo '<div class="text-sm font-medium text-gray-900">' . htmlspecialchars($member['name']) . '</div>';
    echo '<div class="text-sm text-gray-500">' . htmlspecialchars($member['member_code']) . '</div>';
    echo '</div>';
    echo '</div>';
    echo '</td>';
    
    echo '<td class="px-6 py-4 whitespace-nowrap">';
    echo '<div class="text-sm text-gray-900">' . htmlspecialchars($member['phone'] ?: 'N/A') . '</div>';
    echo '<div class="text-sm text-gray-500">' . htmlspecialchars($member['email'] ?: 'N/A') . '</div>';
    echo '</td>';
    
    echo '<td class="px-6 py-4 whitespace-nowrap">';
    echo '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">';
    echo htmlspecialchars($member['tier_name'] ?: 'No Tier');
    echo '</span>';
    echo '</td>';
    
    echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">';
    echo 'RM ' . number_format($member['total_spent'] ?? 0, 2);
    echo '</td>';
    
    echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">';
    echo number_format($member['total_points'] ?? 0);
    echo '</td>';
    
    echo '<td class="px-6 py-4 whitespace-nowrap">';
    echo '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $status_class . '">';
    echo $status_text;
    echo '</span>';
    echo '</td>';
    
    echo '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium">';
    echo '<button onclick="openEditModal(' . $member['id'] . ')" class="text-blue-600 hover:text-blue-900 mr-3">';
    echo '<i class="fas fa-edit"></i>';
    echo '</button>';
    echo '<button onclick="deleteMember(' . $member['id'] . ')" class="text-red-600 hover:text-red-900">';
    echo '<i class="fas fa-trash"></i>';
    echo '</button>';
    echo '</td>';
    echo '</tr>';
}

mysqli_free_result($result);
?>
